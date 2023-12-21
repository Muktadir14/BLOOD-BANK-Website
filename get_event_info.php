<?php
  $db_host = "localhost";
  $db_user = "root";
  $db_pass = "";
  $db_name = "rokto_daan";

  $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['table'])) {
    $table = $_GET['table'];
    
    if ($table === 'events') {
        // Perform necessary SQL queries to fetch event information
        // Retrieve event details and participants
        // Format the fetched data as HTML and echo it
        
        // Example:
        // $html = "<h3>Event Information</h3>";
        // $html .= "<p>... Event details ...</p>";
        // $html .= "<p>... Participants of this event ...</p>";
        
        // echo $html;
    }
}
?>
