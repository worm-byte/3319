<?php
/*Programmer 70: This page is where you can view all menu items and sort them
by name or price in ascending or decending order.
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
    <title>View Menu Items</title>
</head>
<body>
    <div class="container">
        <h1>Menu Items</h1>
        <form method="get" class="sort-options">
            <div>
                <strong>Sort by:</strong>
                <label><input type="radio" name="sort_by" value="dishname" <?php echo (!isset($_GET['sort_by']) || $_GET['sort_by'] == 'dishname') ? 'checked' : ''; ?>> Dish Name</label>
                <label><input type="radio" name="sort_by" value="price" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'price') ? 'checked' : ''; ?>> Price</label>
            </div>
            <div style="margin-top: 10px;">
                <strong>Order:</strong>
                <label><input type="radio" name="order" value="asc" <?php echo (!isset($_GET['order']) || $_GET['order'] == 'asc') ? 'checked' : ''; ?>> Ascending</label>
                <label><input type="radio" name="order" value="desc" <?php echo (isset($_GET['order']) && $_GET['order'] == 'desc') ? 'checked' : ''; ?>> Descending</label>
            </div>
            <button type="submit" style="margin-top: 10px; padding: 5px 10px;">Apply Sorting</button>
        </form>
        
        <table>
            <thead>
                <tr>
                    <th>Menu Item ID</th>
                    <th>Dish Name</th>
                    <th>Price</th>
                    <th>Calories</th>
                    <th>Vegetarian</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Include database connection
                include 'connectdb.php';
                
                // Default values
                $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'dishname';
                $order = isset($_GET['order']) ? $_GET['order'] : 'asc';
                
                // Validate inputs
                if ($sort_by != 'dishname' && $sort_by != 'price') {
                    $sort_by = 'dishname';
                }
                
                if ($order != 'asc' && $order != 'desc') {
                    $order = 'asc';
                }
                
                // Query to fetch menu items with sorting
                $query = "SELECT * FROM menuitem ORDER BY $sort_by $order";
                $result = mysqli_query($connection, $query);
                
                if (!$result) {
                    echo "<tr><td colspan='5'>Error: " . mysqli_error($connection) . "</td></tr>";
                } else if (mysqli_num_rows($result) == 0) {
                    echo "<tr><td colspan='5'>No menu items found.</td></tr>";
                } else {
                    // Display each menu item
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['menuitemid']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['dishname']) . "</td>";
                        echo "<td>$" . number_format($row['price'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($row['caloriecount']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['veggie']) . "</td>";
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
