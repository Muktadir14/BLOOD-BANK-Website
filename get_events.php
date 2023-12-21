<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $region = $_POST["region"];

    // Connect to your database (use your credentials)
    $conn = new mysqli("localhost", "root", "", "rokto_daan");
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $stmt = $conn->prepare("SELECT name, eventID, location, contact, email, eventdate FROM events WHERE region = ?");
    $stmt->bind_param("s", $region);
    $stmt->execute();
    $stmt->bind_result($name, $eventID, $location, $contact, $email,$eventDate);
    
    $options = '';
    while ($stmt->fetch()) {
        $options .= "<option value='$eventID' data-location='$location' data-contact='$contact' data-email='$email' data-eventdate='$eventDate'>$name</option>";
    }

    echo json_encode([
        'options' => $options,
        'location' => $location,
        'contact' => $contact,
        'email' => $email,
        'eventID' => $eventID,
        'eventDate' => $eventDate
    ]);
    
    $stmt->close();
    $conn->close();
}
?>
