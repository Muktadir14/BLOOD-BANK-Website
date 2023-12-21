<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "rokto_daan";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['did'])) {
    $did = $_GET['did'];

    // Query to fetch user information associated with the donor ID
    $query = "SELECT users.* FROM users 
              INNER JOIN donors ON users.nid = donors.nid 
              WHERE donors.did = $did";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        // Display fetched user information as HTML
        echo "<h3>User Information</h3>";
        echo "<p>ID: " . $row['nid'] . "</p>";
        echo "<p>First Name: " . $row['first_name'] . "</p>";
        echo "<p>Last Name: " . $row['last_name'] . "</p>";
        echo "<p>Email: " . $row['email'] . "</p>";
        echo "<p>Gender: " . $row['gender'] . "</p>";
        echo "<p>Bloodgroup: " . $row['blood_group'] . "</p>";
        echo "<p>Disease: " . $row['disease'] . "</p>";
        echo "<p>Date of birth: " . $row['birthdate'] . "</p>";
        echo "<p>Phone number: " . $row['phone_number'] . "</p>";
        echo "<p>Address: " . $row['address'] . "</p>";
        // Add more fields as needed
   }
    } else {
        echo "No user information found for this donor.";
}

// Close connection
$conn->close();
?>
   