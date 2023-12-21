<?php
session_start();
require_once('tcpdf/tcpdf.php');

// Check if the user is authenticated
if (!isset($_SESSION["user_id"])) {
    die("Error: User not authenticated.");
}

$userId = $_SESSION["user_id"]; // Get the user ID from the session

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form data
    $bloodbankID = $_POST["bloodbankID"];
    $donationDate = $_POST["donationDate"];

    // Connect to your database (use your credentials)
    $conn = new mysqli("localhost", "root", "", "rokto_daan");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve the last donation date
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
$stmt = $conn->prepare("INSERT INTO donors (did, donationDate, eventID, bloodbankID, numberofDonations) VALUES (?, ?, NULL, ?, ?)");
$stmt->bind_param("ssss", $userId, $donationDate, $bloodbankID, $numberofDonations);
$stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Generate PDF
        $fileName = $userId . '_' . time() . '.pdf';

        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('times', '', 12);

        $content = "
            <h1 style='color: blue; text-align: center;'>Donation Form</h1>
            <h2 style='color: blue; text-align: center;'>Registration successfull!!</h2>
            <h3 style='color: blue; text-align: center;'>Please bring this form to the bloodbank.</h3>
            <p><strong>User ID:</strong> $userId</p>
            <p><strong>Region:</strong> {$_POST['region']}</p>
            <p><strong>Blood Bank:</strong> {$_POST['bloodbank']}</p>
            <p><strong>Donation Date:</strong> $donationDate</p>
            <p><strong>Address:</strong> {$_POST['address']}</p>
            <p><strong>Phone:</strong> {$_POST['phone']}</p>
            <p><strong>Email:</strong> {$_POST['email']}</p>
            <p><strong>Blood Bank ID:</strong> $bloodbankID</p>
        ";

        $pdf->writeHTML($content, true, 0, true, 0);

        // Save the PDF file
        $pdf->Output('donation_form.pdf', 'D'); // 'D' indicates to force download

        echo "Donation record successfully saved in the database!";
    } else {
        echo "Error saving donation record: " . $conn->error;
    }

    $conn->close();
}
?>
