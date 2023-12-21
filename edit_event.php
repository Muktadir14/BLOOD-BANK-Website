<?php
session_start();

if (!isset($_SESSION["eventID"])) {
    header("Location: event_signin.html");
    exit();
}

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "rokto_daan";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $eventID = $_POST["eventID"];
    $name = $_POST["name"];
    $eventdate = $_POST["eventdate"];
    $region = $_POST["region"];
    $location = $_POST["location"];
    $contact = $_POST["contact"];
    $email = $_POST["email"];

    $update_query = "UPDATE events SET name=?, eventdate=?, region=?, location=?, contact=?, email=? WHERE eventID=?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssssssi", $name, $eventdate, $region, $location, $contact, $email, $eventID);

    if ($update_stmt->execute()) {
        echo "Information saved successfully.";
        // Update the session variable with the modified event ID
        $_SESSION["eventID"] = $eventID; // Update the session variable here
    } else {
        echo "Error updating information: " . $conn->error;
    }
    

    $update_stmt->close();
}

$event_id = $_SESSION["eventID"];

$event_query = "SELECT * FROM events WHERE eventID = ?";
$event_stmt = $conn->prepare($event_query);
$event_stmt->bind_param("i", $event_id);

$event_stmt->execute();
$event_result = $event_stmt->get_result();

$event_data = $event_result->fetch_assoc();

$event_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event Information</title>
    <link rel="stylesheet" href="edit_event.css">
</head>
<body>
    <h2>Edit Event Information</h2>
    <a class="event-dashboard-button" href="event_dashboard.php">GO TO DASHBOARD</a>
    <form action="" method="post">
        <input type="hidden" name="eventID" value="<?php echo $event_data['eventID']; ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $event_data['name']; ?>"><br>

        <label for="eventdate">Event Date:</label>
        <input type="date" id="eventdate" name="eventdate" value="<?php echo $event_data['eventdate']; ?>"><br>

        <label for="region">Region:</label>
        <input type="text" id="region" name="region" value="<?php echo $event_data['region']; ?>"><br>

        <label for="location">Location:</label>
        <input type="text" id="location" name="location" value="<?php echo $event_data['location']; ?>"><br>

        <label for="contact">Contact:</label>
        <input type="text" id="contact" name="contact" value="<?php echo $event_data['contact']; ?>"><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $event_data['email']; ?>"><br>

        <input type="submit" value="Save Changes">
    </form>
</body>
</html>
