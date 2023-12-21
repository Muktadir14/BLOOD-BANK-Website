<?php

$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "rokto_daan"; 

$conn = mysqli_connect($host, $username, $password, $database);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}


function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}


$name = $email = $password = $confirmPassword = "";
$nameErr = $emailErr = $passwordErr = $confirmPasswordErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST["confirmPassword"]);

    if (empty($name)) {
        $nameErr = "Name is required";
    }

    if (empty($email)) {
        $emailErr = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    }

    if (empty($password)) {
        $passwordErr = "Password is required";
    }

    if (empty($confirmPassword)) {
        $confirmPasswordErr = "Confirm Password is required";
    } elseif ($password !== $confirmPassword) {
        $confirmPasswordErr = "Passwords do not match";
    }

    
    if (empty($nameErr) && empty($emailErr) && empty($passwordErr) && empty($confirmPasswordErr)) {
       
        $hashedPassword = hashPassword($password);

       
        $sql = "INSERT INTO admin (name, email, password) VALUES ('$name', '$email', '$hashedPassword')";

        if (mysqli_query($conn, $sql)) {
            echo "Registration successful! You can now <a href='login.php'>login</a>.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

       
        mysqli_close($conn);
    }
}
?>