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

    $query = "SELECT eventID, password FROM events WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($eventID, $storedHashedPassword);
    $stmt->fetch();

    if (password_verify($password, $storedHashedPassword)) {
        // Authentication successful, store eventID in the session
        $_SESSION["eventID"] = $eventID;
        $_SESSION["signin_message"] = "Sign-in successful!";
        // Redirect to the event dashboard
        header("Location: event_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid email or password.'); window.location.href = 'event_signin.html';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: event_signin.html");
}
?>
