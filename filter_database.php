<?php
// Simulated database connection
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "rokto_daan";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$filterType = $_GET['filterType']; // Type of table to filter
$filterValue = $_GET['filterValue']; // Value to filter by

$query = ""; // Initialize the query variable based on the filter type

switch ($filterType) {
    case 'bloodbank':
        $query = "SELECT * FROM bloodbank WHERE region = '$filterValue'";
        break;
    case 'events':
        $query = "SELECT * FROM events WHERE location = '$filterValue'";
        break;
    case 'availability':
        $query = "SELECT * FROM availability WHERE bloodTypes = '$filterValue'";
        break;
    // Add cases for other filter types if needed
    default:
        // Handle invalid or unspecified filter type
        echo "Invalid filter type";
        break;
}

if ($query !== "") {
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Display filtered results in a table or suitable format
        echo "<table>";
        echo "<tr>";
        while ($header = $result->fetch_field()) {
            echo "<th>" . $header->name . "</th>";
        }
        echo "</tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<td>" . $value . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No results found";
    }
}

$conn->close();
?>
