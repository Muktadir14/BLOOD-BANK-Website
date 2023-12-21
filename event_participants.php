<?php
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

$event_id = $_SESSION["eventID"];

$query = "SELECT d.did, u.first_name, u.last_name, u.phone_number, u.email
          FROM donors d 
          INNER JOIN users u 
          ON d.did = u.nid 
          WHERE d.eventID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$participants = array();

while ($row = $result->fetch_assoc()) {
    $donor_id = $row["did"];
    $participant_name = $row["first_name"] . " " . $row["last_name"];
    $phone_number = $row["phone_number"];
    $email = $row["email"];

    $participants[] = array(
        "donorID" => $donor_id,
        "participantName" => $participant_name,
        "phoneNumber" => $phone_number,
        "email" => $email // Add email to the participants array
    );
}

$stmt->close();
$conn->close();

// Return participants array
return $participants;
?>
