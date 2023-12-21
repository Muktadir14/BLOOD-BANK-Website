<?php
session_start();

// Check if the event is authenticated
if (!isset($_SESSION["eventID"])) {
    header("Location: event_signin.html"); // Redirect to the sign-in page if not authenticated
    exit();
}

// Get event information from the database
$event_id = $_SESSION["eventID"];

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "rokto_daan";

// Create a database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch event information from the database using the event ID
$event_query = "SELECT * FROM events WHERE eventID = ?";
$event_stmt = $conn->prepare($event_query);
$event_stmt->bind_param("i", $event_id);

$event_stmt->execute();
$event_result = $event_stmt->get_result();

$event_info = array();

if ($event_row = $event_result->fetch_assoc()) {
    $event_info = $event_row;
}

$event_stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details</title>
    <link rel="stylesheet" href="view_event_details.css">
</head>
<body>
    <div class="header">
        <h2>Event Details</h2>
        <a class="edit-button" href="edit_event.php">EDIT</a>
        <a class="event-dashboard-button" href="event_dashboard.php">GO TO DASHBOARD</a>
    </div>

    <div class="event-details">
        <table class="event-details-table">
            <tr>
                <td>Event ID</td>
                <td><?php echo $event_info["eventID"]; ?></td>
            </tr>
            <tr>
                <td>Name</td>
                <td><?php echo $event_info["name"]; ?></td>
            </tr>
            <tr>
                <td>Event Date</td>
                <td><?php echo $event_info["eventdate"]; ?></td>
            </tr>
            <tr>
                <td>Region</td>
                <td><?php echo $event_info["region"]; ?></td>
            </tr>
            <tr>
                <td>Location</td>
                <td><?php echo $event_info["location"]; ?></td>
            </tr>
            <tr>
                <td>Contact</td>
                <td><?php echo $event_info["contact"]; ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><?php echo $event_info["email"]; ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
