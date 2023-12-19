<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nid = $_POST["nid"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $gender = $_POST["gender"];
    $blood_group = $_POST["blood_group"]; 
    $disease_radio = $_POST["disease_radio"];
    $disease_name = ($disease_radio === "yes") ? $_POST["disease_name"] : "None";
    $birthdate = $_POST["birthdate"];
    $phone_number = $_POST["phone_number"];
    $address = $_POST["address"];

    
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password) || empty($gender) || empty($birthdate) || empty($phone_number) || empty($address)) {
        echo "<script>alert('Please fill in all required fields.'); window.location.href = 'signup.html';</script>";
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

    
    $check_email_query = "SELECT email FROM users WHERE email = '$email'";
    $email_result = $conn->query($check_email_query);

    if ($email_result->num_rows > 0) {
        echo "<script>alert('Email is already in use. Please use a different email.'); window.location.href = 'signup.html';</script>";
        exit();
    }

    
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    
    $query = "INSERT INTO users (nid, first_name, last_name, email, password, gender, blood_group, disease, birthdate, phone_number, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssss", $nid, $first_name, $last_name, $email, $hashed_password, $gender, $blood_group, $disease_name, $birthdate, $phone_number, $address);

    if ($stmt->execute()) {
        
        echo "<script>alert('Sign-up successful! Please proceed to the sign-in page.'); window.location.href = 'user_signin.html';</script>";
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
