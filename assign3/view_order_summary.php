<?php
/*Programmer 70: This page displays the confirmation details of a new order
after it has been placed.
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
    <title>Order Summary</title>
</head>
<body>
    <div class="container">
	<div class="container-content">
        <h1>Order Summary</h1>
        
        <div class="success-message">
            Order has been successfully placed!
        </div>
        
        <?php
        // Include database connection
        include 'connectdb.php';
        
        // Get orderid from URL parameter
        if (!isset($_GET['orderid'])) {
            echo "<p>Error: No order ID specified.</p>";
            echo "<a href='mainmenu.php' class='back-button'>Back to Main Menu</a>";
            exit();
        }
        
        $orderid = $_GET['orderid'];
        
        // Get order details
        $query = "SELECT o.*, 
                        c.firstname AS customer_firstname, c.lastname AS customer_lastname,
                        d.firstname AS driver_firstname, d.lastname AS driver_lastname
                  FROM cusorder o
                  JOIN customer c ON o.cusid = c.cusid
                  LEFT JOIN driver d ON o.driverid = d.driverid
                  WHERE o.orderid = '$orderid'";
        
        $result = mysqli_query($connection, $query);
        
        if (!$result || mysqli_num_rows($result) == 0) {
            echo "<p>Error: Order not found.</p>";
            echo "<a href='mainmenu.php' class='back-button'>Back to Main Menu</a>";
            exit();
        }
        
        $order = mysqli_fetch_assoc($result);
        ?>
        
        <div class="order-details">
            <h2>Order #<?php echo htmlspecialchars($orderid); ?></h2>
            <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_firstname'] . ' ' . $order['customer_lastname']); ?></p>
            
            <?php if ($order['pickuporder'] == 'N'): ?>
                <p><strong>Driver:</strong> <?php echo htmlspecialchars($order['driver_firstname'] . ' ' . $order['driver_lastname']); ?></p>
                <p><strong>Delivery Rating:</strong> <?php echo htmlspecialchars($order['deliveryrating']); ?> / 5</p>
            <?php else: ?>
                <p><strong>Order Type:</strong> Pickup</p>
            <?php endif; ?>
            
            <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['deladdress']); ?></p>
            <p><strong>Date Placed:</strong> <?php echo htmlspecialchars($order['dateplaced']); ?></p>
            <p><strong>Time Placed:</strong> <?php echo htmlspecialchars($order['timeplaced']); ?></p>
            
            <?php if ($order['pickuporder'] == 'N' && !empty($order['timedelivered'])): ?>
                <p><strong>Time Delivered:</strong> <?php echo htmlspecialchars($order['timedelivered']); ?></p>
            <?php endif; ?>
        </div>
        
        <h2>Order Items</h2>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Get order items
                $query = "SELECT oi.quantity, m.dishname, m.price
                          FROM overallorder oi
                          JOIN menuitem m ON oi.menuitemid = m.menuitemid
                          WHERE oi.orderid = '$orderid'";
                
                $result = mysqli_query($connection, $query);
                
                $total = 0;
                
                if (!$result || mysqli_num_rows($result) == 0) {
                    echo "<tr><td colspan='4'>No items found for this order.</td></tr>";
                } else {
                    while ($item = mysqli_fetch_assoc($result)) {
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                        
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($item['dishname']) . "</td>";
                        echo "<td>$" . number_format($item['price'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($item['quantity']) . "</td>";
                        echo "<td>$" . number_format($subtotal, 2) . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
                
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">Total:</td>
                    <td>$<?php echo number_format($total, 2); ?></td>
                </tr>
            </tbody>
        </table>
        
        <a href="mainmenu.php" class="back-button">Back to Main Menu</a>
        <a href="new_order.php" class="back-button" style="background-color: #555;">Place Another Order</a>
    </div>
</div>
</body>
</html>
