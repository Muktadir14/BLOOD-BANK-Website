<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $region = $_POST["region"];

    // Connect to your database (use your credentials)
    $conn = new mysqli("localhost", "root", "", "rokto_daan");
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $stmt = $conn->prepare("SELECT name, bloodbankID, address, phone, email FROM bloodbank WHERE region = ?");
    $stmt->bind_param("s", $region);
    $stmt->execute();
    $stmt->bind_result($name, $bloodbankID, $address, $phone, $email);
    
    $options = '';
    while ($stmt->fetch()) {
        $options .= "<option value='$bloodbankID' data-address='$address' data-phone='$phone' data-email='$email'>$name</option>";
    }

    echo json_encode([
        'options' => $options,
        'address' => $address,
        'phone' => $phone,
        'email' => $email,
        'bloodbankID' => $bloodbankID
    ]);
    
    $stmt->close();
    $conn->close();
}
?>
