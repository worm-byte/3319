<?php
/*Programmer 70: This is the code to connected the database
in the php pages. I have also added a time zone in this 
so that when placing a new order, it will automatically pick
the current date and time.
*/
date_default_timezone_set('America/New_York');

$dbhost = "localhost";
$dbuser= "root";
$dbpass = "cs3319";
$dbname = "assign2db";
$connection = mysqli_connect($dbhost, $dbuser,$dbpass,$dbname);
if (mysqli_connect_errno()) {
     die("database connection failed :" .
     mysqli_connect_error() .
     "(" . mysqli_connect_errno() . ")"
         );
    }
?>
