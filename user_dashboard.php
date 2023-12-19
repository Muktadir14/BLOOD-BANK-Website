<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    header("Location: user_signin.html"); // Redirect to the sign-in page if not authenticated
    exit();
}

// Get user information from the database
$user_id = $_SESSION["user_id"];

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "rokto_daan";

// Create a database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user information from the database using the email (user_id)
// Fetch user information from the database using the user's ID (nid or did)
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

// Function to fetch donation history
function fetchDonationHistory($conn, $user_id)
{
    $donation_history = array();

    // Fetch donation history using the user's NID (did) from the donors table
    $query = "SELECT donationDate, numberofDonations, eventID, bloodbankID FROM donors WHERE did = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $donation_date = $row["donationDate"];
        $donation_number = $row["numberofDonations"];
        $event_id = $row["eventID"];
        $bloodbank_id = $row["bloodbankID"];

        // Fetch event name from the events table (if eventID is not NULL)
        $event_name = "None";
        if (!is_null($event_id)) {
            $event_query = "SELECT name FROM events WHERE eventID = ?";
            $event_stmt = $conn->prepare($event_query);
            $event_stmt->bind_param("i", $event_id);
            $event_stmt->execute();
            $event_result = $event_stmt->get_result();
            if ($event_row = $event_result->fetch_assoc()) {
                $event_name = $event_row["name"];
            }
            $event_stmt->close();
        }

        // Fetch blood bank name from the bloodbank table (if bloodbankID is not NULL)
        $bloodbank_name = "None";
        if (!is_null($bloodbank_id)) {
            $bloodbank_query = "SELECT name FROM bloodbank WHERE bloodbankID = ?";
            $bloodbank_stmt = $conn->prepare($bloodbank_query);
            $bloodbank_stmt->bind_param("i", $bloodbank_id);
            $bloodbank_stmt->execute();
            $bloodbank_result = $bloodbank_stmt->get_result();
            if ($bloodbank_row = $bloodbank_result->fetch_assoc()) {
                $bloodbank_name = $bloodbank_row["name"];
            }
            $bloodbank_stmt->close();
        }

        $donation_history[] = array(
            "donationDate" => $donation_date,
            "donationNumber" => $donation_number,
            "eventName" => $event_name,
            "bloodbankName" => $bloodbank_name
        );
    }

    $stmt->close();

    return $donation_history;
}

// Function to fetch receiving history
function fetchReceivingHistory($conn, $user_id)
{
    $receiving_history = array();

    // Fetch receiving history using the user's NID (pid) from the patient table
    $query = "SELECT requestDate, bloodRequestType, numberofUnits FROM patient WHERE pid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $request_date = $row["requestDate"];
        $blood_type = $row["bloodRequestType"];
        $number_of_units = $row["numberofUnits"];

        $receiving_history[] = array(
            "requestDate" => $request_date,
            "bloodType" => $blood_type,
            "numberOfUnits" => $number_of_units
        );
    }

    $stmt->close();

    return $receiving_history;
}

// Fetch donation and receiving history
$donation_history = fetchDonationHistory($conn, $user_id);
$receiving_history = fetchReceivingHistory($conn, $user_id);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="user_dashboard.css">
    <!-- Include Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="header">
        <!-- User logo and name container -->
        <div class="user-info">
            <img src="user_logo.png" alt="User Logo" class="user-logo">
            <div class="user-name">
                <span><?php echo $first_name . " " . $last_name; ?></span>
            </div>
        </div>
        
        <!-- Menu button for dropdown options -->
        <div class="menu">
            <div class="menu-icon" id="menuButton">&#9776;</div>
            <div class="menu-content" id="menuContent">
                <a href="donate_selection.html">Donate Blood</a>
                <a href="patient_portal.php">Seek Blood</a>
                <a href="user_signout.php" class="exit-button">
                    <i class="fas fa-sign-out-alt"></i> Exit
                </a>
            </div>
        </div>
    </div>
    
    <div class="dashboard-content">
        <h2>Donation History</h2>
        <table class="history-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Donation Number</th>
                    <th>Event Name</th>
                    <th>Blood Bank Name</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donation_history as $donation) : ?>
                    <tr>
                        <td><?php echo $donation["donationDate"]; ?></td>
                        <td><?php echo $donation["donationNumber"]; ?></td>
                        <td><?php echo $donation["eventName"]; ?></td>
                        <td><?php echo $donation["bloodbankName"]; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Receiving History</h2>
        <table class="history-table">
            <thead>
                <tr>
                    <th>Request Date</th>
                    <th>Blood Type</th>
                    <th>No. of Units</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($receiving_history as $receiving) : ?>
                    <tr>
                        <td><?php echo $receiving["requestDate"]; ?></td>
                        <td><?php echo $receiving["bloodType"]; ?></td>
                        <td><?php echo $receiving["numberOfUnits"]; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        // JavaScript code for sign-out functionality
        document.addEventListener("DOMContentLoaded", function () {
            // Add a click event listener to the "Sign Out" link
            document.querySelector(".exit-button").addEventListener("click", function (event) {
                event.preventDefault(); // Prevent the default link behavior
                // Clear the session and redirect to the sign-in page
                fetch("user_signout.php", {
                    method: "POST",
                })
                    .then(function (response) {
                        if (response.redirected) {
                            // Redirect to the sign-in page
                            window.location.href = response.url;
                        }
                    })
                    .catch(function (error) {
                        console.error("Sign-out error:", error);
                    });
            });

            // JavaScript code to toggle the menu content
            var menuButton = document.getElementById("menuButton");
            var menuContent = document.getElementById("menuContent");

            menuButton.addEventListener("click", function () {
                menuContent.classList.toggle("show-menu");
            });

            document.addEventListener("click", function (event) {
                if (!menuButton.contains(event.target) && !menuContent.contains(event.target)) {
                    menuContent.classList.remove("show-menu");
                }
            });
        });


    </script>
</body>
</html>
