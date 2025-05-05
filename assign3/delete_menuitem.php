<?php
/*Programmer 70: This page is for deleting a menu item. It asks the user to confirm 
deletion before proceeding. It will not delete items if they are tied
to an order*/
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
    <title>Delete Menu Item</title>
</head>
<body>
    <div class="container">
        <h1>Delete Menu Item</h1>
        
        <?php
        // Database connection
        include 'connectdb.php';
        
        // Initialize variables
        $menuitemid = "";
        $message = "";
        $confirm = false;
        
        // Handle delete form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == 'yes') {
                // User confirmed deletion
                $menuitemid = $_POST['menuitemid'];
                
                // Check if the menu item is in any orders
                $check_query = "SELECT COUNT(*) as count FROM overallorder WHERE menuitemid = '$menuitemid'";
                $result = mysqli_query($connection, $check_query);
                $row = mysqli_fetch_assoc($result);
                
                if ($row['count'] > 0) {
                    // Menu item is in orders, cannot delete
                    $message = "<div class='error-message'>This menu item cannot be deleted because it is part of one or more orders.</div>";
                } else {
                    // Proceed with deletion
                    $delete_query = "DELETE FROM menuitem WHERE menuitemid = '$menuitemid'";
                    
                    if (mysqli_query($connection, $delete_query)) {
                        $message = "<div class='success-message'>Menu item has been successfully deleted.</div>";
                    } else {
                        $message = "<div class='error-message'>Error deleting menu item: " . mysqli_error($connection) . "</div>";
                    }
                }
            } else if (isset($_POST['delete'])) {
                // Show confirmation dialog
                $menuitemid = $_POST['menuitemid'];
                $confirm = true;
                
                // Get menu item details for confirmation
                $query = "SELECT * FROM menuitem WHERE menuitemid = '$menuitemid'";
                $result = mysqli_query($connection, $query);
                
                if (!$result || mysqli_num_rows($result) == 0) {
                    $message = "<div class='error-message'>Menu item not found.</div>";
                    $confirm = false;
                } else {
                    $item = mysqli_fetch_assoc($result);
                }
            }
        }
        
        // Display confirmation dialog if needed
        if ($confirm) {
            echo $message;
            echo "<div class='confirm-dialog'>";
            echo "<p>Are you sure you want to delete this menu item?</p>";
            echo "<p><strong>Item ID:</strong> " . htmlspecialchars($item['menuitemid']) . "</p>";
            echo "<p><strong>Dish Name:</strong> " . htmlspecialchars($item['dishname']) . "</p>";
            echo "<p><strong>Price:</strong> $" . number_format($item['price'], 2) . "</p>";
            echo "<p><strong>Calories:</strong> " . htmlspecialchars($item['caloriecount']) . "</p>";
            echo "<p><strong>Vegetarian:</strong> " . (htmlspecialchars($item['veggie']) == 'Y' ? 'Yes' : 'No') . "</p>";
            
            echo "<form method='post' action='delete_menuitem.php'>";
            echo "<input type='hidden' name='menuitemid' value='" . htmlspecialchars($menuitemid) . "'>";
            echo "<input type='hidden' name='confirm_delete' value='yes'>";
            echo "<button type='submit' class='confirm-btn'>Yes, Delete Item</button>";
            echo "<a href='delete_menuitem.php' class='cancel-btn' style='text-decoration: none; display: inline-block; padding: 8px 12px;'>Cancel</a>";
            echo "</form>";
            echo "</div>";
        } else {
            // Display list of menu items
            echo $message;
        ?>
        
        <table>
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Dish Name</th>
                    <th>Price</th>
                    <th>Calories</th>
                    <th>Vegetarian</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Get menu items from database
                $query = "SELECT * FROM menuitem ORDER BY dishname";
                $result = mysqli_query($connection, $query);
                
                if (!$result) {
                    echo "<tr><td colspan='6'>Error fetching menu items: " . mysqli_error($connection) . "</td></tr>";
                } else if (mysqli_num_rows($result) == 0) {
                    echo "<tr><td colspan='6'>No menu items found.</td></tr>";
                } else {
                    // Display each menu item with delete button
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['menuitemid']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['dishname']) . "</td>";
                        echo "<td>$" . number_format($row['price'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($row['caloriecount']) . "</td>";
                        echo "<td>" . (htmlspecialchars($row['veggie']) == 'Y' ? 'Yes' : 'No') . "</td>";
                        echo "<td>";
                        echo "<form method='post' action='/vm227/a3turtle/delete_menuitem.php'>";
                        echo "<input type='hidden' name='menuitemid' value='" . $row['menuitemid'] . "'>";
                        echo "<button type='submit' name='delete' class='delete-btn'>Delete</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
        
        <?php
        }
        
        // Close connection
        mysqli_close($connection);
        ?>
        
        <a href="mainmenu.php" class="back-button">Back to Main Menu</a>
    </div>
</body>
</html>
