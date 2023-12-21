<?php
session_start();

if (isset($_SESSION['email'])) {
    $table = $_GET['table'];

    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "rokto_daan";

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    

    if ($table === 'events') {
        $query = "SELECT * FROM events";
        $result = $conn->query($query);
    
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr>";
            while ($header = $result->fetch_field()) {
                echo "<th>" . $header->name . "</th>";
            }
            echo "<th>Action</th>";
            echo "</tr>";
    
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    if ($key === 'phone_number' || $key === 'contact' || $key === 'phone') {
                        echo "<td><a href='tel:" . $value . "'>" . $value . "</a></td>";
                    } elseif ($key === 'email') {
                        echo "<td><a href='mailto:" . $value . "'>" . $value . "</a></td>";
                    } else {
                        echo "<td>" . $value . "</td>";
                    }
                }
                echo "<td><button onclick='deleteEvent(" . $row['eventID'] . ")'>Delete</button></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No data available";
        }
    }elseif ($table === 'availability') {
        $query = "SELECT availability.*, bloodbank.name AS Bloodbank  
                  FROM availability 
                  INNER JOIN bloodbank ON availability.bloodbankID = bloodbank.bloodbankID";
        
        $result = $conn->query($query);
    
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr>";
            while ($header = $result->fetch_field()) {
                echo "<th>" . $header->name . "</th>";
            }
            echo "<th>Action</th>";
            echo "</tr>";
    
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    if ($key === 'phone_number' || $key === 'contact' || $key === 'phone') {
                        echo "<td><a href='tel:" . $value . "'>" . $value . "</a></td>";
                    } elseif ($key === 'email') {
                        echo "<td><a href='mailto:" . $value . "'>" . $value . "</a></td>";
                    } else {
                        echo "<td>" . $value . "</td>";
                    }
                }
                echo "<td><button onclick='incrementUnit(" . $row['bloodbankID'] . ", \"" . $row['bloodTypes'] . "\")'>Increase</button></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No data available";
        }
    } elseif ($table === 'patient' || $table === 'donors') {
        $idColumn = ($table === 'patient') ? 'pid' : 'did'; // Adjust the ID column name based on the table
        
        $query = "SELECT $table.*, users.email AS Email, users.phone_number AS Phone 
                  FROM $table 
                  INNER JOIN users ON $table.$idColumn = users.nid";
    
        $result = $conn->query($query);
    
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr>";
            // Display headers for the combined data from donors/patients and users tables
            $headers = $result->fetch_fields();
            foreach ($headers as $header) {
                echo "<th>" . $header->name . "</th>";
            }
            echo "<th>Email</th>"; // Adding Email column header
            echo "<th>Phone</th>"; // Adding Phone column header
            echo "</tr>";
    
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    echo "<td>" . $value . "</td>";
                }
                echo "<td><a href='mailto:" . $row['Email'] . "'>" . $row['Email'] . "</a></td>"; // Displaying Email as a clickable link
                echo "<td><a href='tel:" . $row['Phone'] . "'>" . $row['Phone'] . "</a></td>"; // Displaying Phone as a clickable link
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No data available";
        }
    }
     elseif ($table === 'bloodbank' || $table === 'participants' || $table === 'users') {
        $query = "SELECT * FROM " . $table;
    
        $result = $conn->query($query);
    
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr>";
            while ($header = $result->fetch_field()) {
                echo "<th>" . $header->name . "</th>";
            }
            echo "</tr>";
    
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    if ($key === 'phone_number' || $key === 'contact' || $key === 'phone') {
                        echo "<td><a href='tel:" . $value . "'>" . $value . "</a></td>";
                    } elseif ($key === 'email') {
                        echo "<td><a href='mailto:" . $value . "'>" . $value . "</a></td>";
                    } else {
                        echo "<td>" . $value . "</td>";
                    }
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No data available";
        }
    } else {
        echo "Invalid table name";
    }

    

   // Function to delete an event
   if (isset($_GET['eventID']) && $table === 'events') {
    $eventID = $_GET['eventID'];

    $deleteQuery = "DELETE FROM events WHERE eventID = $eventID";

    if ($conn->query($deleteQuery) === TRUE) {
        echo "Event deleted successfully";
    } else {
        echo "Error deleting event: " . $conn->error;
    }
}

    $conn->close();
} else {
    header("Location: admin_signin.html");
}
?>
