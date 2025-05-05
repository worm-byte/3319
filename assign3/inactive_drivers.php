<?php
/* Programmer 70: This page displays all of the drivers that have not 
made deliveries yet.
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- 
    Font: Michroma
    Source: Google Fonts (https://fonts.google.com/share?selection.family=Michroma)
    License: Open Font License
    -->    
<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Michroma&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="solo_leveling.css">
    <title>Inactive Drivers</title>
</head>
<body>
    <div class="container">
        <h1>Inactive Drivers</h1>
        
        <div class="info-message">
            This page displays drivers who have not made any deliveries yet.
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Driver ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Include database connection
                include 'connectdb.php';
                
                // Query to find drivers who have not made any deliveries
                $query = "SELECT d.driverid, d.firstname, d.lastname
                          FROM driver d
                          LEFT JOIN cusorder o ON d.driverid = o.driverid
                          WHERE o.driverid IS NULL
                          ORDER BY d.lastname, d.firstname";
                
                $result = mysqli_query($connection, $query);
                
                if (!$result) {
                    echo "<tr><td colspan='3'>Error fetching data: " . mysqli_error($connection) . "</td></tr>";
                } else if (mysqli_num_rows($result) == 0) {
                    echo "<tr><td colspan='3'>All drivers have made deliveries.</td></tr>";
                } else {
                    // Display inactive drivers
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['driverid']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
                        echo "</tr>";
                    }
                }
                
                // Close connection
                mysqli_close($connection);
                ?>
            </tbody>
        </table>
        
        <a href="mainmenu.php" class="back-button">Back to Main Menu</a>
    </div>
</body>
</html>
