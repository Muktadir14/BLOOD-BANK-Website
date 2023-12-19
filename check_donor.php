<?php
session_start();

if (!isset($_SESSION["bloodbank_email"])) {
    header("Location: bloodbank_signin.html");
    exit();
}

$bloodbank_email = $_SESSION["bloodbank_email"];

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "rokto_daan";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT bloodbankID FROM bloodbank WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $bloodbank_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Blood bank not found.'); window.location.href = 'bloodbank_signin.html';</script>";
    exit();
}

$row = $result->fetch_assoc();
$bloodbank_id = $row['bloodbankID'];

$stmt->close();

$donor_query = "SELECT donors.donationDate, donors.did, users.first_name, users.last_name, users.email, 
                users.gender, users.blood_group, YEAR(CURDATE()) - YEAR(users.birthdate) AS age, 
                users.phone_number, users.address
                FROM donors
                JOIN users ON donors.did = users.nid
                WHERE donors.bloodbankID = ?";
$donor_stmt = $conn->prepare($donor_query);
$donor_stmt->bind_param("i", $bloodbank_id);
$donor_stmt->execute();
$donor_result = $donor_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Donor</title>
    <link rel="stylesheet" href="check_donor.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="header">
        <span class="header-title"> Donor List </span>
        <button onclick="goBack()" class="return-btn">Return</button>
    </div>

    <div class="dashboard-content">
        <div class="search-bar">
            <label for="searchInput">Search:</label>
            <input type="text" id="searchInput" placeholder="Search..." onkeyup="filterTable()">
        </div>

        <table id="donorTable">
            <thead>
                <tr>
                    <th>Donation Date</th>
                    <th>Donor ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Blood Group</th>
                    <th>Age</th>
                    <th>Contact</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody id="donorTableBody">
                <?php
                while ($donor_row = $donor_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $donor_row['donationDate'] . "</td>";
                    echo "<td>" . $donor_row['did'] . "</td>";
                    echo "<td>" . $donor_row['first_name'] . "</td>";
                    echo "<td>" . $donor_row['last_name'] . "</td>";
                    echo "<td>" . $donor_row['email'] . "</td>";
                    echo "<td>" . $donor_row['gender'] . "</td>";
                    echo "<td>" . $donor_row['blood_group'] . "</td>";
                    echo "<td>" . $donor_row['age'] . "</td>";
                    echo "<td>" . $donor_row['phone_number'] . "</td>";
                    echo "<td>" . $donor_row['address'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }

        function filterTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("donorTable");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                tds = tr[i].getElementsByTagName("td");
                let display = "none";

                for (let j = 0; j < tds.length; j++) {
                    let td = tds[j];
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            display = "";
                            break;
                        }
                    }
                }
                tr[i].style.display = display;
            }

            if (filter === "") {
                for (i = 0; i < tr.length; i++) {
                    tr[i].style.display = "";
                }
            }
        }
    </script>
</body>
</html>

<?php
$donor_stmt->close();
$conn->close();
?>
