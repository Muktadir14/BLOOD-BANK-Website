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

    $query = "SELECT password FROM admin WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($storedHashedPassword);
    $stmt->fetch();

    if (password_verify($password, $storedHashedPassword)) {
  
        echo "Authentication successfull!!";
        exit();
    } else {
        echo "<script>alert('Invalid email or password.'); window.location.href = 'admin_signin.html';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: admin_signin.html");
}
?>
