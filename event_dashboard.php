<?php
session_start();

// Check if the event is authenticated
if (!isset($_SESSION["eventID"])) {
    header("Location: event_signin.html"); // Redirect to the sign-in page if not authenticated
    exit();
}

// Get event information from the database
$event_id = $_SESSION["eventID"];

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "rokto_daan";

// Create a database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch event information from the database using the event ID
$event_query = "SELECT name FROM events WHERE eventID = ?";
$event_stmt = $conn->prepare($event_query);
$event_stmt->bind_param("i", $event_id);

$event_stmt->execute();
$event_result = $event_stmt->get_result();

$event_name = "";

if ($event_row = $event_result->fetch_assoc()) {
    $event_name = $event_row["name"];
}

$event_stmt->close();

// Function to fetch participants information
function fetchParticipantsInfo($conn, $event_id)
{
    $participants_info = array();

    // Fetch participants information using the event ID from the participants table
    $query = "SELECT p.eventID, e.name AS eventName, p.numberofParticipants, e.eventDate 
              FROM participants p
              INNER JOIN events e ON p.eventID = e.eventID
              WHERE p.eventID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $event_id = $row["eventID"];
        $event_name = $row["eventName"];
        $participants_count = $row["numberofParticipants"];
        $event_date = $row["eventDate"];

        $participants_info[] = array(
            "eventID" => $event_id,
            "eventName" => $event_name,
            "participantsCount" => $participants_count,
            "eventDate" => $event_date
        );
    }

    $stmt->close();

    return $participants_info;
}

// Fetch participants information
$participants_info = fetchParticipantsInfo($conn, $event_id);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Dashboard</title>
    <link rel="stylesheet" href="event_dashboard.css">
    <script>
        function toggleMenu() {
            var menuContent = document.getElementById("menuContent");
            menuContent.classList.toggle("show-menu");
        }
    </script>
</head>
<body>
    <div class="header">
        <!-- Event name container -->
        <div class="event-info">
            <div class="event-name">
                <h2>Welcome <span><?php echo $event_name; ?></span></h2>
            </div>
        </div>


        <div class="menu">
    <div class="menu-icon" onclick="toggleMenu()">☰</div>
    <div class="menu-content" id="menuContent">
        <a href="view_event_details.php" class="menu-item">Check Information</a>
        <a href="event_signout.php" class="menu-item">Sign out</a>
    </div>
</div>
    
        
    </div>
    
    <div class="dashboard-content">
        <h2>Event Information</h2>
        <table class="participants-table">
            <thead>
                <tr>
                    <th>Event ID</th>
                    <th>Event Name</th>
                    <th>Number of Participants</th>
                    <th>Event Date</th>
                </tr>
            </thead>
            <tbody>
    <?php foreach ($participants_info as $participant) : ?>
        <tr class="participant-row">
            <td><?php echo $participant["eventID"]; ?></td>
            <td><?php echo $participant["eventName"]; ?></td>
            <td><?php echo $participant["participantsCount"]; ?></td>
            <td><?php echo $participant["eventDate"]; ?></td>
        </tr>
    <?php endforeach; ?>
</tbody>
        </table>
        <?php foreach ($participants_info as $participant) : ?>
    <h2>Participants of the event  <?php echo $participant["eventName"] ; ?></h2>
    <table class="participants-table">
        <thead>
            <tr>
                <th>DONOR ID</th>
                <th>Name of Participant</th>
                <th>Phone number</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $eventID = $participant["eventID"];
            // Include and get participants array
            $participants = include "event_participants.php";
            
            // Display participants in the table
            foreach ($participants as $participant) :
            ?>
                <tr>
                    <td><?php echo $participant["donorID"]; ?></td>
                    <td><?php echo $participant["participantName"]; ?></td>
                    <td><a href="tel:<?php echo $participant["phoneNumber"]; ?>"><?php echo $participant["phoneNumber"]; ?></a></td>
                    <td><a href="mailto:<?php echo $participant["email"]; ?>"><?php echo $participant["email"]; ?></a></td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endforeach; ?>
</div>

    </div>
</body>
</html>
