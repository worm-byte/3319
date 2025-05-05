<?php
/*Programmer 70: This is the page where the user can modify the price or 
calories of a menu item and save it.
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
    <title>Modify Menu Item</title>
</head>
<body>
    <div class="container">
        <h1>Modify Menu Item</h1>
        
        <?php
        // Include database connection
        include 'connectdb.php';
        
        // Initialize variables
        $menuitemid = "";
        $price = "";
        $caloriecount = "";
        $message = "";
        $editing = false;
        
        // Process edit form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
            $menuitemid = $_POST['menuitemid'];
            $price = $_POST['price'];
            $caloriecount = $_POST['caloriecount'];
            
            // Validate input
            if (!is_numeric($price) || $price <= 0) {
                $message = "<div class='error-message'>Price must be a positive number.</div>";
            } else if (!is_numeric($caloriecount) || $caloriecount < 0) {
                $message = "<div class='error-message'>Calorie count must be a non-negative number.</div>";
            } else {
                // Update the menu item
                $update_query = "UPDATE menuitem SET price = '$price', caloriecount = '$caloriecount' WHERE menuitemid = '$menuitemid'";
                
                if (mysqli_query($connection, $update_query)) {
                    $message = "<div class='success-message'>Menu item has been successfully updated.</div>";
                    // Reset editing mode
                    $editing = false;
                } else {
                    $message = "<div class='error-message'>Error updating menu item: " . mysqli_error($connection) . "</div>";
                }
            }
        } else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
            // User clicked edit button, enter editing mode
            $menuitemid = $_POST['menuitemid'];
            $editing = true;
            
            // Get the current menu item data
            $query = "SELECT * FROM menuitem WHERE menuitemid = '$menuitemid'";
            $result = mysqli_query($connection, $query);
            
            if (!$result || mysqli_num_rows($result) == 0) {
                $message = "<div class='error-message'>Menu item not found.</div>";
                $editing = false;
            } else {
                $item = mysqli_fetch_assoc($result);
                $price = $item['price'];
                $caloriecount = $item['caloriecount'];
            }
        }
        
        // Display message if any
        echo $message;
        
        // Display edit form if in editing mode
        if ($editing) {
        ?>
        
        <div class="form-container">
            <h2>Edit Menu Item: <?php echo htmlspecialchars($item['dishname']); ?></h2>
            
            <form method="post" action="https://cs3319.gaul.csd.uwo.ca/vm227/a3turtle/modify_menu_item.php">
                <input type="hidden" name="menuitemid" value="<?php echo htmlspecialchars($menuitemid); ?>">
                
                <div class="form-group">
                    <label for="price">Price ($):</label>
                    <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>" step="0.01" min="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="caloriecount">Calorie Count:</label>
                    <input type="number" id="caloriecount" name="caloriecount" value="<?php echo htmlspecialchars($caloriecount); ?>" min="0" required>
                </div>
                
                <button type="submit" name="update">Update Menu Item</button>
		<a href="https://cs3319.gaul.csd.uwo.ca/vm227/a3turtle/modify_menu_item.php" class="cancel-btn">Cancel</a>
            </form>
        </div>
        
        <?php
        } else {
            // Display list of menu items
        ?>
        
        <table>
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Dish Name</th>
                    <th>Price</th>
                    <th>Calories</th>
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
                    // Display each menu item with edit button
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['menuitemid']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['dishname']) . "</td>";
                        echo "<td>$" . number_format($row['price'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($row['caloriecount']) . "</td>";

                        echo "<td>";
                        echo "<form method=\"post\" action=\"https://cs3319.gaul.csd.uwo.ca/vm227/a3turtle/modify_menu_item.php\">";
                        echo "<input type='hidden' name='menuitemid' value='" . $row['menuitemid'] . "'>";
                        echo "<button type='submit' name='edit' class='edit-btn'>Edit</button>";
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
