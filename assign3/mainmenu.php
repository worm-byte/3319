<?php
/*Programmer: 70
This is the main menu for the program that can lead you to all other pages
of the website. There is one photo on the main page, but the creator is 
unknown. I have done my best with a citation below:
"Chibi Solo Leveling Characters." Pinterest, https://ca.pinterest.com/pin/16677461111821523/.
*/
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="utf-8">
<!-- 
Font: Michroma
Source: Google Fonts (https://fonts.google.com/share?selection.family=Michroma)
License: Open Font License
-->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Michroma&display=swap" rel="stylesheet">
<link rel="stylesheet" href="solo_leveling.css">
<title>Jin-woo💙 </title>
</head>
<body>
<?php
include 'connectdb.php';
?>
<h1>🍝Shadow Monarch's Pizzeria🍕</h1>
<div class="container">
        <h2>Order Management System</h2>
        
        <div class="menu-options">
            <a href="view_menu_items.php" class="menu-item">View All Menu Items</a>
            <a href="new_order.php" class="menu-item">Place New Order</a>
            <a href="delete_menuitem.php" class="menu-item">Delete Menu Item</a>
            <a href="modify_menu_item.php" class="menu-item">Modify Menu Item</a>
            <a href="inactive_drivers.php" class="menu-item">View Inactive Drivers</a>
            <a href="view_order.php" class="menu-item">View Order Details</a>
        </div>
    </div>
<div class="footer-image">
    <img src="chibi_solo_leveling.png" alt="Chibi Solo Leveling">
</div>
</body>
</html>

