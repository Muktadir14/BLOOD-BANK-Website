<?php
session_start();

// Destroy the user's session to log them out
session_destroy();

// Redirect the user to the sign-in page
header("Location: user_signin.html");
exit();
?>
