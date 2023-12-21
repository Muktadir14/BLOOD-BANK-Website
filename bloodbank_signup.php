<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $region = $_POST["region"];
    $address = $_POST["address"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

   
    if (empty($name) || empty($phone) || empty($address) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href = 'signup.html';</script>";
        exit();
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.location.href = 'signup.html';</script>";
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

    $check_email_query = "SELECT email FROM bloodbank WHERE email = '$email'";
    $email_result = $conn->query($check_email_query);

    if ($email_result->num_rows > 0) {
        echo "<script>alert('Email is already in use. Please use a different email.'); window.location.href = 'signup.html';</script>";
        exit();
    }


    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    
    $query = "INSERT INTO bloodbank (name, phone, region, address, email, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $name, $phone, $region, $address, $email, $hashed_password);

    if ($stmt->execute()) {
       
        echo "<script>alert('Sign-up successful! Please proceed to the sign-in page.'); window.location.href = 'index.php';</script>";
        exit();
    } else {
     
        echo "Error: " . $stmt->error;
    }


    $stmt->close();
    $conn->close();
} else {
    header("Location: signup.html");
}
?>
