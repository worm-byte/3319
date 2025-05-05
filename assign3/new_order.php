<?php
/*
Programmer: 70
This page takes in all the details required for a new order and
displays a summary of all the order details at the end.
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
    <title>Place New Order</title>
    <link rel="stylesheet" href="solo_leveling.css">
</head>
<body>
    <div class="container">
	<div class = "container-content">
        <h1>Place New Order</h1>
        
        <?php
        // Include database connection
        include 'connectdb.php';
        
        // Initialize variables
        $orderid = "";
        $deladdress = "";
        
        $dateplaced = date("Y-m-d"); // Today's date as default (in EST)
        $timeplaced = date("H:i"); // Current time as default (in EST)
        $timedelivered = "";
        $pickuporder = "N";
        $deliveryrating = "";
        $driverid = "";
        $cusid = "";
        $error_message = "";
        $success = false;
        
        // Process form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get form data
            $orderid = $_POST['orderid'];
            $deladdress = $_POST['deladdress'];
            $dateplaced = $_POST['dateplaced'];
            $timeplaced = $_POST['timeplaced'];
            $pickuporder = $_POST['pickuporder'];
            
            // Get other fields based on order type (pickup or delivery)
            if ($pickuporder == 'N') {
                // Delivery order
                $timedelivered = $_POST['timedelivered'];
                $deliveryrating = $_POST['deliveryrating'];
                $driverid = $_POST['driverid'];
            } else {
                // Pickup order - set NULL values for delivery-specific fields
                $timedelivered = null;
                $deliveryrating = null;
                $driverid = null;
            }
            
            $cusid = $_POST['cusid'];
            
            // Check if orderid is unique
            $check_query = "SELECT orderid FROM cusorder WHERE orderid = '$orderid'";
            $result = mysqli_query($connection, $check_query);
            
            if (mysqli_num_rows($result) > 0) {
                $error_message = "Error: Order ID already exists. Please use a different ID.";
            } 
            // Check if times for delivery orders
            else if ($pickuporder == 'N' && strtotime($timeplaced) >= strtotime($timedelivered)) {
                $error_message = "Error: Order placed time must be before order delivered time.";
            }
            // Check if rating for delivery orders
            else if ($pickuporder == 'N' && ($deliveryrating < 1 || $deliveryrating > 5)) {
                $error_message = "Error: Rating must be between 1 and 5 for delivery orders.";
            }
            // Check if menu items are selected
            else if (!isset($_POST['menu_items']) || count($_POST['menu_items']) == 0) {
                $error_message = "Error: Please select at least one menu item.";
            }
            else {
                // Begin transaction
                mysqli_begin_transaction($connection);
                
                try {
                    // Insert into cusorder table
                    $query = "INSERT INTO cusorder (orderid, deladdress, dateplaced, timeplaced, timedelivered, pickuporder, deliveryrating, driverid, cusid) 
                              VALUES ('$orderid', '$deladdress', '$dateplaced', '$timeplaced', ";
                    
                    // Handle NULL values properly for pickup orders
                    if ($pickuporder == 'Y') {
                        $query .= "NULL, 'Y', NULL, NULL, '$cusid')";
                    } else {
                        $query .= "'$timedelivered', 'N', $deliveryrating, '$driverid', '$cusid')";
                    }
                    
                    if (!mysqli_query($connection, $query)) {
                        throw new Exception("Error inserting order: " . mysqli_error($connection));
                    }
                    
                    // Insert order items
                    $itemsInserted = false;
                    
                    foreach ($_POST['menu_items'] as $menuitemid) {
   			 // Check if the menu item has a quantity assigned
    			if (isset($_POST['quantity'][$menuitemid])) {
        			$quantity = (int)$_POST['quantity'][$menuitemid];
        
        			if ($quantity > 0) {
            				$query = "INSERT INTO overallorder (orderid, menuitemid, quantity) 
        				 VALUES ('$orderid', '$menuitemid', '$quantity')";
            
            				if (!mysqli_query($connection, $query)) {
                				throw new Exception("Error inserting order item: " . mysqli_error($connection));
            				}
            
            			$itemsInserted = true;
        			}
    			}
		}
                    
                    // Make sure at least one item was inserted
                    if (!$itemsInserted) {
                        throw new Exception("Error: No valid menu items were selected.");
                    }
                    
                    // Commit transaction
                    mysqli_commit($connection);
                    
                    // Set success flag
                    $success = true;
                    
                    // Redirect to order summary page with complete path
                    header("Location: https://cs3319.gaul.csd.uwo.ca/vm227/a3turtle/view_order_summary.php?orderid=$orderid");
                    exit();
                    
                } catch (Exception $e) {
                    // Rollback transaction on error
                    mysqli_rollback($connection);
                    $error_message = $e->getMessage();
                }
            }
        }
        
        if (!$success) {
            // Display error message if any
            if (!empty($error_message)) {
                echo "<div class='error'>$error_message</div>";
            }
        ?>
        
        <form method="post" action="https://cs3319.gaul.csd.uwo.ca/vm227/a3turtle/new_order.php">           
	 <div class="form-group">
                <label for="orderid">Order ID:</label>
                <input type="text" id="orderid" name="orderid" value="<?php echo htmlspecialchars($orderid); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="cusid">Customer:</label>
                <select id="cusid" name="cusid" required>
                    <option value="">Select Customer</option>
                    <?php
                    // Get customers from database
                    $query = "SELECT cusid, CONCAT(firstname, ' ', lastname) AS customer_name FROM customer ORDER BY lastname, firstname";
                    $result = mysqli_query($connection, $query);
                    
                    while ($row = mysqli_fetch_assoc($result)) {
                        $selected = ($row['cusid'] == $cusid) ? 'selected' : '';
                        echo "<option value='" . $row['cusid'] . "' $selected>" . htmlspecialchars($row['customer_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="deladdress">Delivery Address:</label>
                <input type="text" id="deladdress" name="deladdress" value="<?php echo htmlspecialchars($deladdress); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="dateplaced">Date Placed:</label>
                <input type="date" id="dateplaced" name="dateplaced" value="<?php echo htmlspecialchars($dateplaced); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="timeplaced">Time Placed:</label>
                <input type="time" id="timeplaced" name="timeplaced" value="<?php echo htmlspecialchars($timeplaced); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Order Type:</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="pickuporder" value="N" <?php echo ($pickuporder == 'N') ? 'checked' : ''; ?> onclick="toggleDeliveryFields(false)"> Delivery
                    </label>
                    <label>
                        <input type="radio" name="pickuporder" value="Y" <?php echo ($pickuporder == 'Y') ? 'checked' : ''; ?> onclick="toggleDeliveryFields(true)"> Pickup
                    </label>
                </div>
            </div>
            
            <div id="delivery-fields" style="<?php echo ($pickuporder == 'Y') ? 'display:none;' : ''; ?>">
                <div class="form-group">
                    <label for="driverid">Driver:</label>
                    <select id="driverid" name="driverid" <?php echo ($pickuporder == 'N') ? 'required' : ''; ?>>
                        <option value="">Select Driver</option>
                        <?php
                        // Get drivers from database
                        $query = "SELECT driverid, CONCAT(firstname, ' ', lastname) AS driver_name FROM driver ORDER BY lastname, firstname";
                        $result = mysqli_query($connection, $query);
                        
                        while ($row = mysqli_fetch_assoc($result)) {
                            $selected = ($row['driverid'] == $driverid) ? 'selected' : '';
                            echo "<option value='" . $row['driverid'] . "' $selected>" . htmlspecialchars($row['driver_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="timedelivered">Time Delivered:</label>
                    <input type="time" id="timedelivered" name="timedelivered" value="<?php echo htmlspecialchars($timedelivered); ?>">
                </div>
                
                <div class="form-group">
                    <label for="deliveryrating">Delivery Rating (1-5):</label>
                    <input type="number" id="deliveryrating" name="deliveryrating" min="1" max="5" value="<?php echo htmlspecialchars($deliveryrating); ?>">
                </div>
            </div>
            
            <div class="menu-items">
                <h2>Select Menu Items:</h2>
                <?php
                // Get menu items from database
                $query = "SELECT * FROM menuitem ORDER BY dishname";
                $result = mysqli_query($connection, $query);
                
                while ($row = mysqli_fetch_assoc($result)) {
                    // Display menu item
                    echo "<div class='menu-item'>";
                    echo "<input type='checkbox' class='menu-item-checkbox' name='menu_items[]' value='" . $row['menuitemid'] . "' id='item_" . $row['menuitemid'] . "'>";
                    echo "<div class='menu-item-details'>";
                    echo "<label for='item_" . $row['menuitemid'] . "'>";
                    echo "<strong>" . htmlspecialchars($row['dishname']) . "</strong> - $" . number_format($row['price'], 2);
                    echo " (" . htmlspecialchars($row['caloriecount']) . " calories)";
                    echo "</label>";
                    echo "</div>";
                    echo "<input type='number' class='menu-item-quantity' name='quantity[" . $row['menuitemid'] . "]' value='1' min='1'>";
                    echo "</div>";
                }
                ?>
            </div>
            
            <button type="submit">Place Order</button>
        </form>
        
        <?php
        }
        
        // Close connection
        mysqli_close($connection);
        ?>
        
        <a href="https://cs3319.gaul.csd.uwo.ca/vm227/a3turtle/mainmenu.php" class="back-button">Back to Main Menu</a>
    </div>
    </div>
    <script>
    // JavaScript for delivery specific fields if delivery is checked off
    function toggleDeliveryFields(isPickup) {
        const deliveryFields = document.getElementById('delivery-fields');
        
        if (isPickup) {
            deliveryFields.style.display = 'none';
            document.getElementById('driverid').removeAttribute('required');
            document.getElementById('timedelivered').removeAttribute('required');
            document.getElementById('deliveryrating').removeAttribute('required');
        } else {
            deliveryFields.style.display = 'block';
            document.getElementById('driverid').setAttribute('required', 'required');
            document.getElementById('timedelivered').setAttribute('required', 'required');
            document.getElementById('deliveryrating').setAttribute('required', 'required');
        }
    }
    
    </script>
</body>
</html>

