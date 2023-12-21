<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
<div class="header">
<div class="header">
<div class="menu-button">
            Menu
            <div class="dropdown-content">
                <button onclick="signOut()">Sign Out</button>
            </div>
        </div>

    <div class="header">
        <h1>Welcome admin</h1>
    </div>
    <div class="container">
        <div class="tables">
            <button onclick="showTable('availability')">Availability</button>
            <button onclick="showTable('bloodbank')">Blood Bank</button>
            <button onclick="showTable('donors')">Donors</button>
            <button onclick="showTable('events')">Events</button>
            <button onclick="showTable('participants')">Participants</button>
            <button onclick="showTable('patient')">Patients</button>
            <button onclick="showTable('users')">Users</button>
            <!-- Add buttons for other tables here -->
        </div>
        <div class="data-display" id="dataDisplay">
            <!-- Data will be displayed here -->
        </div>

    </div>

    <script>
        function showTable(tableName) {
    if (tableName === 'events') {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("dataDisplay").innerHTML = "<button class='custom-button' onclick=\"location.href='event_signup.html'\">Add new event</button><br><br>" + this.responseText;
            }
        };
        xhttp.open("GET", "get_table_data.php?table=" + tableName, true);
        xhttp.send();
    } else if (tableName === 'bloodbank') {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("dataDisplay").innerHTML = "<button class='custom-button' onclick=\"location.href='bloodbank_signup.html'\">Create Bloodbank</button><br><br>" + this.responseText;
            }
        };
        xhttp.open("GET", "get_table_data.php?table=" + tableName, true);
        xhttp.send();
    } else if (tableName === 'users') {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("dataDisplay").innerHTML = "<button class='custom-button' onclick=\"location.href='user_signup.html'\">New User</button><br><br>" + this.responseText;
            }
        };
        xhttp.open("GET", "get_table_data.php?table=" + tableName, true);
        xhttp.send();
    }    else if (tableName === 'patient' || tableName === 'donors') {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("dataDisplay").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "get_table_data.php?table=" + tableName, true);
        xhttp.send();
    } 
          else {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("dataDisplay").innerHTML = this.responseText;
                    }
                };
                xhttp.open("GET", "get_table_data.php?table=" + tableName, true);
                xhttp.send();
            }
        }

        //Fetch user function
        function fetchUserInfo(pid, did) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("dataDisplay").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "get_user_info.php?pid=" + pid + "&did=" + did, true);
            xhttp.send();
        }


// Function to handle event deletion
function deleteEvent(eventID) {
    var confirmDelete = confirm("Are you sure you want to delete this event?");
    if (confirmDelete) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                // Refresh the events table after deleting the event
                showTable('events');
            }
        };
        xhttp.open("GET", "get_table_data.php?table=events&eventID=" + eventID, true);
        xhttp.send();
    }
}

function incrementUnit(bloodbankID, bloodTypes) {
    console.log("Incrementing for bloodbankID: " + bloodbankID + ", bloodTypes: " + bloodTypes);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            // Refresh the table after updating
            showTable('availability');
            console.log(this.responseText);
        }
    };
    xhttp.open("GET", "update_remaining_units.php?bloodbankID=" + bloodbankID + "&bloodTypes=" + bloodTypes, true);
    xhttp.send();
}


    // Function to handle sign out
    function signOut() {
        // Perform sign-out actions here (clear sessions, redirect to admin_signin.html)
        window.location.href = 'admin_signin.html';
    }

    

    </script>
</body>
</html>


