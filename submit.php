<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rokto_daan";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sanitize($conn, $input) {
    return mysqli_real_escape_string($conn, htmlspecialchars($input));
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['donationDate'])) {
    $donationDate = date('Y-m-d', strtotime($_POST['donationDate']));

    $formType = $_POST['formType'];
    $eventID = ($formType == 'event') ? sanitize($conn, $_POST['eventID']) : 'NULL';
    $bloodbankID = ($formType == 'bloodbank') ? sanitize($conn, $_POST['bloodbankID']) : 'NULL';

    // Retrieve the user's nid from the session
    session_start();
    $user_id = $_SESSION["user_id"];

    // Retrieve the current value of numberofDonations
    $sql_select = "SELECT numberofDonations FROM donors WHERE did='$user_id'";
    $result = $conn->query($sql_select);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $numberofDonations = $row['numberofDonations'];
        $numberofDonations++; // Increment the value
    } else {
        $numberofDonations = 1; // Set to 1 if no previous donations
        $sql_insert_user = "INSERT INTO donors (did, numberofDonations) VALUES ('$user_id', 1)";
    $conn->query($sql_insert_user);
    }

    // Update the database with the new value
    $sql_update = "UPDATE donors SET numberofDonations='$numberofDonations' WHERE did='$user_id'";
    if ($conn->query($sql_update) === TRUE) {
        $sql_insert = "INSERT INTO donors (did, numberofDonations, donationDate, eventID, bloodbankID)
            VALUES ('$user_id', '$numberofDonations', '$donationDate', $eventID, $bloodbankID)";

        if ($conn->query($sql_insert) === TRUE) {
            echo "Form submitted successfully!";
        } else {
            echo "Error: " . $sql_insert . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $sql_update . "<br>" . $conn->error;
    }
} else {
    echo "No data received";
}

$conn->close();
?>
