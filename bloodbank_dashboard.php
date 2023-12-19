<?php
session_start();

if (!isset($_SESSION["bloodbank_email"])) {
    header("Location: bloodbank_signin.html");
    exit();
}

$bloodbank_email = $_SESSION["bloodbank_email"];

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "rokto_daan";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT bloodbankID, name FROM bloodbank WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $bloodbank_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Blood bank not found.'); window.location.href = 'bloodbank_signin.html';</script>";
    exit();
}

$row = $result->fetch_assoc();
$bloodbank_id = $row['bloodbankID'];

$stmt->close();

$availability_query = "SELECT bloodTypes, remainingUnits FROM availability WHERE bloodbankID = ?";
$availability_stmt = $conn->prepare($availability_query);
$availability_stmt->bind_param("i", $bloodbank_id);
$availability_stmt->execute();
$availability_result = $availability_stmt->get_result();

$blood_types = [];

while ($availability_row = $availability_result->fetch_assoc()) {
    $blood_types[$availability_row['bloodTypes']] = $availability_row['remainingUnits'];
}

$availability_stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['type'])) {
        $type = $_POST['type'];
        $units = isset($blood_types[$type]) ? $blood_types[$type] : 0;
        
        if (isset($_POST['increment'])) {
            if ($units == 0) {
                $insert_query = "INSERT INTO availability (bloodTypes, remainingUnits, bloodbankID) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                $units = 1;
            } else {
                $units++;
                $update_query = "UPDATE availability SET remainingUnits = ? WHERE bloodTypes = ? AND bloodbankID = ?";
                $update_stmt = $conn->prepare($update_query);
            }

            if (isset($insert_stmt)) {
                $insert_stmt->bind_param("sii", $type, $units, $bloodbank_id);
                $insert_stmt->execute();
                $insert_stmt->close();
            } else {
                $update_stmt->bind_param("isi", $units, $type, $bloodbank_id);
                $update_stmt->execute();
                $update_stmt->close();
            }

            $blood_types[$type] = $units;
        } elseif (isset($_POST['decrement'])) {
            if ($units > 0) {
                $units--;
                if ($units == 0) {
                    $delete_query = "DELETE FROM availability WHERE bloodTypes = ? AND bloodbankID = ?";
                    $delete_stmt = $conn->prepare($delete_query);
                    $delete_stmt->bind_param("si", $type, $bloodbank_id);
                    $delete_stmt->execute();
                    $delete_stmt->close();
                    unset($blood_types[$type]);
                } else {
                    $update_query = "UPDATE availability SET remainingUnits = ? WHERE bloodTypes = ? AND bloodbankID = ?";
                    $update_stmt = $conn->prepare($update_query);
                    $update_stmt->bind_param("isi", $units, $type, $bloodbank_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                    $blood_types[$type] = $units;
                }
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Dashboard</title>
    <link rel="stylesheet" href="bloodbank_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function toggleDropdown() {
            var dropdownContent = document.getElementById("dropdownContent");
            if (dropdownContent.style.display === "none" || dropdownContent.style.display === "") {
                dropdownContent.style.display = "block";
            } else {
                dropdownContent.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <div class="header">
        <img src="user_logo.png" alt="User Logo" class="user-logo">
        <div class="user-details">
            <span><?php echo $row['name']; ?>'s Dashboard</span>
        </div>
        <div class="dropdown">
            <button onclick="toggleDropdown()" class="dropbtn"><i class="fas fa-bars"></i></button>
            <div id="dropdownContent" class="dropdown-content">
                <a href="check_donor.php">Check Donor</a>
                <a href="bloodbank_signin.html">Exit</a>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <h2>Stock</h2>
        <table>
            <thead>
                <tr>
                    <th>Blood Type</th>
                    <th>Units</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $all_blood_types = ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];

                foreach ($all_blood_types as $type) {
                    $units = isset($blood_types[$type]) ? $blood_types[$type] : 0;
                    echo "<tr>";
                    echo "<td>$type</td>";
                    echo "<td><span id='$type'>$units</span></td>";
                    echo "<td>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='type' value='$type'>";
                    echo "<button type='submit' name='increment'>+</button>";
                    echo "<button type='submit' name='decrement'>-</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Rest of your dashboard content goes here -->
</body>
</html>
