<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "rokto_daan";
    
    // Create a database connection
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Prepare and execute a query to fetch the hashed password and nid
    $query = "SELECT nid, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($nid, $storedHashedPassword);
    $stmt->fetch();
    
    // Verify the password
    if (password_verify($password, $storedHashedPassword)) {
        // Authentication successful, store user nid in the session
        session_start();
        $_SESSION["user_id"] = $nid; // Using nid as the user identifier
        $_SESSION["signin_message"] = "Sign-in successful!";
        header("Location: user_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid email or password.'); window.location.href = 'user_signin.html';</script>";
    }
    
    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    header("Location: user_signin.html");
}
?>
