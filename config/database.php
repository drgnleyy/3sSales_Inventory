<?php

$conn = mysqli_connect("localhost", "root", "", "sales_inventory_db");

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}

?>