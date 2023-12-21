<?php
session_start();

if (isset($_SESSION['email'])) {
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "rokto_daan";

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_GET['bloodbankID']) && isset($_GET['bloodTypes'])) {
        $bloodbankID = $_GET['bloodbankID'];
        $bloodTypes = $_GET['bloodTypes'];

        // Prepare the query with placeholders
        $updateQuery = "UPDATE availability SET remainingUnits = remainingUnits + 1 WHERE bloodbankID = ? AND bloodTypes = ?";
        
        // Prepare the statement
        $stmt = $conn->prepare($updateQuery);

        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("is", $bloodbankID, $bloodTypes);

            // Execute the statement
            if ($stmt->execute()) {
                // Check if any rows were affected
                if ($stmt->affected_rows > 0) {
                    echo "Update successful";
                } else {
                    echo "No records updated";
                }
            } else {
                echo "Error updating record: " . $stmt->error;
            }
            
            // Close the statement
            $stmt->close();
        } else {
            echo "Statement preparation failed";
        }

        // Close the connection
        $conn->close();
    }
} else {
    echo "User not logged in";
}
?>
