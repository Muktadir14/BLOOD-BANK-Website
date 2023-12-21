<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "rokto_daan";

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT bloodbankID, password FROM bloodbank WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($bloodbankID, $storedHashedPassword);
    $stmt->fetch();

    if (password_verify($password, $storedHashedPassword)) {
        // Authentication successful - set session and redirect
        $_SESSION["bloodbank_email"] = $email;
        $_SESSION["bloodbank_id"] = $bloodbankID;
        header("Location: bloodbank_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid email or password.'); window.location.href = 'bloodbank_signin.html';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: bloodbank_signin.html");
}
?>
