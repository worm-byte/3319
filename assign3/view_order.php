<?php
/*Programmer 70: This page displays order details once the user picks
an order id from the dropdown menu.
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
<title>View Order Details</title>
</head>
<body>
    <div class="container">
        <h1>View Order Details</h1>
        
        <div class="order-selection">
            <form method="get" action="https://cs3319.gaul.csd.uwo.ca/vm227/a3turtle/view_order.php">
                <label for="orderid"><strong>Select an Order:</strong></label>
                <select id="orderid" name="orderid">
                    <option value="">-- Select Order --</option>
                    <?php
                    // Include database connection
                    include 'connectdb.php';
                    
                    // Get all orders
                    $query = "SELECT o.orderid, c.firstname AS customer_firstname, c.lastname AS customer_lastname,
                                    o.dateplaced, o.timeplaced
                              FROM cusorder o
                              JOIN customer c ON o.cusid = c.cusid
                              ORDER BY o.dateplaced DESC, o.timeplaced DESC";
                    
                    $result = mysqli_query($connection, $query);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $selected = (isset($_GET['orderid']) && $_GET['orderid'] == $row['orderid']) ? 'selected' : '';
                            echo "<option value='" . $row['orderid'] . "' $selected>";
                            echo "Order #" . htmlspecialchars($row['orderid']) . " - " . 
                                htmlspecialchars($row['customer_firstname'] . " " . $row['customer_lastname']) . 
                                " (" . htmlspecialchars($row['dateplaced'] . " " . $row['timeplaced']) . ")";
                            echo "</option>";
                        }
                    } else {
                        echo "<option value=''>No orders found</option>";
                    }
                    ?>
                </select>
                <button type="submit">View Order</button>
            </form>
        </div>
        
        <?php
        // Display order details if an order is selected
        if (isset($_GET['orderid']) && !empty($_GET['orderid'])) {
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
                echo "<p>Order not found.</p>";
            } else {
                $order = mysqli_fetch_assoc($result);
        ?>
                
                <h2>Order #<?php echo htmlspecialchars($orderid); ?> Details</h2>
                
                <div class="order-details">
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
                
                <h3>Order Items</h3>
                
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
                        $query = "SELECT oo.quantity, m.dishname, m.price
                                  FROM overallorder oo
                                  JOIN menuitem m ON oo.menuitemid = m.menuitemid
                                  WHERE oo.orderid = '$orderid'";
                        
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
        <?php
            }
        }
        ?>
        
        <a href="https://cs3319.gaul.csd.uwo.ca/vm227/a3turtle/mainmenu.php" class="back-button">Back to Main Menu</a>
    </div>
</body>
</html>
