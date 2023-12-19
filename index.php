<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "rokto_daan";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$currentDate = date("Y-m-d"); // Get the current date in the format "YYYY-MM-DD"

$query = "SELECT name as Name, eventdate as Date, region as Area FROM events WHERE eventdate >= '$currentDate' ORDER BY eventdate ASC";
$result = $conn->query($query);

$events = array();
$eventNumber = 1; // Initialize event number

while ($row = $result->fetch_assoc()) {
    $row['Number'] = $eventNumber++; // Add event number to the row
    $events[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rokto Daan</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="Rokto Daan.png" alt="Blood Donation Center Logo">
        </div>
        <div class="dropdown">
            <button class="dropbtn">Menu</button>
            <div class="dropdown-content">
                <a href="admin_signin.html">Admin</a>
                <a href="event_signin.html">Event</a>
                <a href="bloodbank_signin.html">Blood Bank</a>
                <a href="about_us.html">About Us</a>
            </div>
        </div>
    </header>
    <section class="hero-section">
        <h1>Every Donation Counts<br> Join the Lifesaving Cause!</h1>
        <div class="cta-buttons">
            <a href="user_signin.html" class="cta-button">Sign In</a>
            <a href="user_signup.html" class="cta-button">Sign Up</a>
        </div>
    </section>
    <div class="events-section">
        <div class="events-window">
            <h2>Check out our upcoming <br>Blood drive events</h2>
            <div class="events-content">
                <textarea rows="5" cols="30" readonly>
                    <?php foreach ($events as $event) : ?>
                        <?php echo $event["Number"] . ". " . $event["Name"] . " - " . $event["Date"] . " - " . $event["Area"] . "\n"; ?>
                    <?php endforeach; ?>
                </textarea>
            </div>
        </div>
    </div>
    <div class="footer">
        <p>ROKTO DAAN: A project by PHOENIX</p>
    </div>
</body>
</html>
