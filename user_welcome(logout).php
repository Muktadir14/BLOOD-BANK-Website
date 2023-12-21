<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.html");
    exit();
}

if (isset($_POST['logout'])) {
  
    session_destroy();

  
    header("Location: signin.html");
    exit();
}
?>
