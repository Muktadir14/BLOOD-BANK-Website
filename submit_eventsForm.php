<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    die("Error: User not authenticated.");
}

$userId = $_SESSION["user_id"]; // Get the user ID from the session

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $eventID = $_POST["eventID"];
    $donationDate = $_POST["donationDate"];

    // Connect to your database (use your credentials)
    $conn = new mysqli("localhost", "root", "", "rokto_daan");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    /// Retrieve the last donation date
    $stmt = $conn->prepare("SELECT MAX(donationDate) FROM donors WHERE did = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $stmt->bind_result($lastDonationDate);
    $stmt->fetch();
    $stmt->close();

    // Check if 3 months have passed since the last donation
    $threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));
    if ($lastDonationDate >= $threeMonthsAgo) {
        echo "Your next donation time is not before " . date('d-m-Y', strtotime($lastDonationDate . ' +3 months'));
        exit;
    }

    // Get the number of donations for the user
$stmt = $conn->prepare("SELECT COUNT(*) FROM donors WHERE did = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$stmt->bind_result($numberofDonations);
$stmt->fetch();
$stmt->close();

$numberofDonations++; // Increment the donation number

// Insert the donation record
$stmt = $conn->prepare("INSERT INTO donors (did, donationDate, eventID, bloodbankID, numberofDonations) VALUES (?, ?, ?, NULL, ?)");
$stmt->bind_param("ssss", $userId, $donationDate, $eventID, $numberofDonations);
$stmt->execute();

    if ($stmt->affected_rows > 0) {
         // Fetch the event name
    $stmt = $conn->prepare("SELECT name FROM events WHERE eventID = ?");
    $stmt->bind_param("s", $eventID);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();
    $stmt->close();
        // Generate PDF
        require_once('tcpdf/tcpdf.php');

        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('times', '', 12);

        $content = "
            <h1 style='color: blue; text-align: center;'>Event Participation Form</h1>
            <h2 style='color: blue; text-align: center;'>Registration successfull!!</h2>
            <h3 style='color: blue; text-align: center;'>Please bring this form to the event venue.</h3>
            <p><strong>User ID:</strong> $userId</p>
            <p><strong>Region:</strong> {$_POST['region']}</p>
            <p><strong>Name of Event:</strong> $name</p> <!-- Use the fetched event name -->
            <p><strong>Date of Donation:</strong> $donationDate</p>
            <p><strong>Location:</strong> {$_POST['location']}</p>
            <p><strong>Contact:</strong> {$_POST['contact']}</p>
            <p><strong>Email:</strong> {$_POST['email']}</p>
            <p><strong>Event ID:</strong> $eventID</p>
        ";

        $pdf->writeHTML($content, true, 0, true, 0);

        // Save the PDF file
        $fileName = $userId . '_' . $eventID . '_' . time() . '.pdf';
        $pdf->Output($fileName, 'D'); // 'D' indicates to force download

        echo "Donation record successfully saved in the database!";

    } else {
        echo "Error saving donation record: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
