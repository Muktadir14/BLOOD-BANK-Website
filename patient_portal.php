<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: user_signin.html");
    exit();
}

$user_id = $_SESSION["user_id"];

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "rokto_daan";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_query = "SELECT first_name, last_name FROM users WHERE nid = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("s", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

$first_name = "";
$last_name = "";

if ($user_row = $user_result->fetch_assoc()) {
    $first_name = $user_row["first_name"];
    $last_name = $user_row["last_name"];
}

$user_stmt->close();

function fetchBloodData($conn, $region, $bloodType)
{
    $blood_data = array();

    $query = "SELECT bb.name AS bank_name, bb.phone, bb.region, bb.address, bb.email, av.bloodTypes, av.remainingUnits, av.bloodbankID
              FROM bloodbank AS bb
              LEFT JOIN availability AS av ON bb.bloodbankID = av.bloodbankID
              WHERE bb.region = ? AND av.bloodTypes = ? AND av.remainingUnits > 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $region, $bloodType);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $blood_data[] = array(
            "Bank Name" => $row["bank_name"],
            "Contact" => $row["phone"],
            "Region" => $row["region"],
            "Location" => $row["address"],
            "Email" => $row["email"],
            "Blood Type" => $row["bloodTypes"],
            "Units available" => $row["remainingUnits"],
            "Blood Bank ID" => $row["bloodbankID"]
        );
    }

    $stmt->close();

    return $blood_data;
}

$bloodData = array();
$selectedBloodType = "";
$selectedBankName = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["check_availability"])) {
        $selectedRegion = $_POST["region"];
        $selectedBloodType = $_POST["bloodType"];

        $bloodData = fetchBloodData($conn, $selectedRegion, $selectedBloodType);
    } elseif (isset($_POST["submit_request"])) {
        $selectedBloodType = $_POST["bloodType"];
        $units = $_POST["units"];
        $requestDate = $_POST["requestDate"];
        $selectedBankName = $_POST["bank_name"];

        // Retrieve the Blood Bank ID from the bloodbank table
        $bloodBankQuery = "SELECT bloodbankID FROM bloodbank WHERE name = ?";
        $bloodBankStmt = $conn->prepare($bloodBankQuery);
        $bloodBankStmt->bind_param("s", $selectedBankName);
        $bloodBankStmt->execute();
        $bloodBankResult = $bloodBankStmt->get_result();

        $bloodBankID = null;

        if ($bloodBankRow = $bloodBankResult->fetch_assoc()) {
            $bloodBankID = $bloodBankRow["bloodbankID"];
        }

        $bloodBankStmt->close();

        if ($bloodBankID !== null) {
            // Deduct the requested units from the availability table
            $deductUnitsQuery = "UPDATE availability SET remainingUnits = remainingUnits - ? WHERE bloodbankID = ? AND bloodTypes = ? AND remainingUnits >= ?";
            $deductUnitsStmt = $conn->prepare($deductUnitsQuery);
            $deductUnitsStmt->bind_param("dsss", $units, $bloodBankID, $selectedBloodType, $units);
            $deductUnitsStmt->execute();

            if ($deductUnitsStmt->affected_rows > 0) {
                // Store the blood request in the patient table
                $storeRequestQuery = "INSERT INTO patient (pid, bloodRequestType, numberofUnits, requestDate, bloodbankID) VALUES (?, ?, ?, ?, ?)";
                $storeRequestStmt = $conn->prepare($storeRequestQuery);
                $storeRequestStmt->bind_param("sssss", $user_id, $selectedBloodType, $units, $requestDate, $bloodBankID);
                $storeRequestStmt->execute();

                if ($storeRequestStmt->affected_rows > 0) {
                    $message = "Your Request Is Sent Successfully";
                    $isSuccess = true;
                } else {
                    $message = "Error: Request Could Not Be Stored";
                    $isSuccess = false;
                }

                $storeRequestStmt->close();
            } else {
                $message = "Error: Insufficient Units Available";
                $isSuccess = false;
            }

            $deductUnitsStmt->close();
        } else {
            $message = "Error: Blood Bank not found";
            $isSuccess = false;
        }

        // Redirect back to the patient portal with the message
        header("Location: patient_portal.php?message=" . urlencode($message) . "&success=" . urlencode($isSuccess));
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Portal</title>
    <link rel="stylesheet" href="patient_portal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="header">
        <a href="user_dashboard.php" class="return-button">
            <i class="fas fa-arrow-left"></i> Return
        </a>
        <div class="user-info">
            <h1>Welcome to Patient's Portal, <?php echo $first_name . " " . $last_name; ?></h1>
        </div>
    </div>
    
    <div class="portal-content">
        <h2>Check Blood Availability</h2>
        <form method="POST" action="">
            <label for="region">Enter Region:</label>
            <select id="region" name="region">
            <option value="Abdullahpur">Abdullahpur</option>
                <option value="Uttara">Uttara</option>
                <option value="Mirpur">Mirpur</option>
                <option value="Pallabi">Pallabi</option>
                <option value="Kazipara">Kazipara</option>
                <option value="Kafrul">Kafrul</option>
                <option value="Agargaon">Agargaon</option>
                <option value="Sher-e-Bangla Nagar">Sher-e-Bangla Nagar</option>
                <option value="Cantonment area">Cantonment area</option>
                <option value="Banani">Banani</option>
                <option value="Gulshan">Gulshan</option>
                <option value="Niketan, Gulshan">Niketan, Gulshan</option>
                <option value="Mohakhali">Mohakhali</option>
                <option value="Bashundhara">Bashundhara</option>
                <option value="Banasree">Banasree</option>
                <option value="Baridhara">Baridhara</option>
                <option value="Uttarkhan">Uttarkhan</option>
                <option value="Dakshinkhan">Dakshinkhan</option>
                <option value="Bawnia">Bawnia</option>
                <option value="Khilkhet">Khilkhet</option>
                <option value="Tejgaon">Tejgaon</option>
                <option value="Farmgate">Farmgate</option>
                <option value="Mohammadpur">Mohammadpur</option>
                <option value="Rampura">Rampura</option>
                <option value="Badda">Badda</option>
                <option value="Satarkul">Satarkul</option>
                <option value="Beraid">Beraid</option>
                <option value="Khilgaon">Khilgaon</option>
                <option value="Vatara">Vatara</option>
                <option value="Gabtali">Gabtali</option>
                <option value="Hazaribagh">Hazaribagh</option>
                <option value="Dhanmondi">Dhanmondi</option>
                <option value="Segunbagicha">Segunbagicha</option>
                <option value="Ramna">Ramna</option>
                <option value="Motijheel">Motijheel</option>
                <option value="Sabujbagh">Sabujbagh</option>
                <option value="Lalbagh">Lalbagh</option>
                <option value="Kamalapur">Kamalapur</option>
                <option value="Kamrangirchar">Kamrangirchar</option>
                <option value="Islampur">Islampur</option>
                <option value="Sadarghat">Sadarghat</option>
                <option value="Wari">Wari</option>
                <option value="Kotwali">Kotwali</option>
                <option value="Sutrapur">Sutrapur</option>
                <option value="Jurain">Jurain</option>
                <option value="Dania">Dania</option>
                <option value="Demra">Demra</option>
                <option value="Shyampur">Shyampur</option>
                <option value="Nimtoli">Nimtoli</option>
                <option value="Matuail">Matuail</option>
                <option value="Shahbagh">Shahbagh</option>
                <option value="Paltan">Paltan</option>
            </select>
            
            <label for="bloodType">Blood Type:</label>
            <select id="bloodType" name="bloodType">
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
            </select>
            
            <button type="submit" name="check_availability">Search</button>
        </form>

        <?php if (!empty($bloodData) && count($bloodData) > 0) : ?>
            <h2>Blood Banks and Availability</h2>
            <table class="combined-table">
                <thead>
                    <tr>
                        <th>Bank Name</th>
                        <th>Contact</th>
                        <th>Region</th>
                        <th>Location</th>
                        <th>Email</th>
                        <th>Blood Type</th>
                        <th>Units available</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bloodData as $data) : ?>
                        <tr>
                            <td><?php echo $data["Bank Name"]; ?></td>
                            <td><a href="tel:<?php echo $data["Contact"]; ?>"><?php echo $data["Contact"]; ?></a></td>
                            <td><?php echo $data["Region"]; ?></td>
                            <td><a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($data["Location"]); ?>" target="_blank"><?php echo $data["Location"]; ?></a></td>
                            <td><a href="mailto:<?php echo $data["Email"]; ?>"><?php echo $data["Email"]; ?></a></td>
                            <td><?php echo $data["Blood Type"]; ?></td>
                            <td><?php echo $data["Units available"]; ?></td>
                            <td>
                                <button class="request-button" onclick="openRequestForm(
                                    '<?php echo $data["Blood Type"]; ?>',
                                    '<?php echo $data["Bank Name"]; ?>',
                                    '<?php echo $data["Units available"]; ?>',
                                    '<?php echo $data["Blood Bank ID"]; ?>'
                                )">Request</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (isset($_POST["check_availability"])) : ?>
            <p>No blood banks found with the selected criteria.</p>
        <?php endif; ?>
    </div>

    <!-- Request Form Modal -->
    <div id="requestModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeRequestForm()">&times;</span>
            <h3>Request Form</h3>
            <form method="POST" action="">
                <!-- Blood Type -->
                <label for="bloodType">Blood Type:</label>
                <input type="text" id="bloodTypeField" name="bloodType" readonly>

                <!-- Bank Name -->
                <label for="bank_name">Bank Name:</label>
                <input type="text" id="bank_name" name="bank_name" readonly>

                <!-- Units -->
                <label for="units">Units:</label>
                <input type="number" id="units" name="units" min="1" max="" required>

                <!-- Request Date -->
                <label for="requestDate">Request Date:</label>
                <input type="date" id="requestDate" name="requestDate" required
                min="<?php echo date('Y-m-d'); ?>">

                <!-- Patient ID (Autofilled) -->
                <label for="pid">Patient ID:</label>
                <input type="text" id="pid" name="pid" value="<?php echo $user_id; ?>" readonly>

                <!-- Blood Bank ID (Hidden) -->
                <input type="hidden" id="bloodbankID" name="bloodbankID">

                <!-- Submit Button -->
                <button type="submit" name="submit_request">Submit Request</button>
            </form>
        </div>
    </div>

    <!-- Popup Message -->
    <div id="popupMessage" class="popup-message" style="display: none;"></div>

    <script>
    function openRequestForm(bloodType, bankName, availableUnits, bloodBankID) {
        document.getElementById("bloodTypeField").value = bloodType;  // Autofill Blood Type
        document.getElementById("bank_name").value = bankName;
        document.getElementById("units").setAttribute("max", availableUnits);
        document.getElementById("units").value = 1;
        document.getElementById("bloodbankID").value = bloodBankID;
        
        const requestModal = document.getElementById("requestModal");
        requestModal.style.display = "block";
    }

    function closeRequestForm() {
        const requestModal = document.getElementById("requestModal");
        requestModal.style.display = "none";
    }

    function showMessage(message, isSuccess) {
    const popupMessage = document.getElementById("popupMessage");
    popupMessage.textContent = message;

    if (isSuccess) {
        popupMessage.style.backgroundColor = "#4CAF50"; // Green color for success
    } else {
        popupMessage.style.backgroundColor = "#f44336"; // Red color for error
    }

    popupMessage.style.display = "block";

    // Hide the message after 3 seconds (3000 milliseconds)
    setTimeout(function() {
        popupMessage.style.display = "none";
    }, 3000);
}

    </script>
</body>
</html>
