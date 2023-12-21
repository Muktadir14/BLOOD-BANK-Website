<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    die(json_encode(["lastDonationDate" => null]));
}

$userId = $_SESSION["user_id"];

$conn = new mysqli("localhost", "root", "", "rokto_daan");

if ($conn->connect_error) {
    die(json_encode(["lastDonationDate" => null]));
}

$stmt = $conn->prepare("SELECT MAX(donationDate) FROM donors WHERE did = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();
$stmt->bind_result($lastDonationDate);
$stmt->fetch();
$stmt->close();

$conn->close();

echo json_encode(["lastDonationDate" => $lastDonationDate]);
?>
