<?php
include 'connect.php';

$id = $_GET['id'];

// First delete order items
$deleteItems = "DELETE FROM order_items WHERE order_id = '$id'";
mysqli_query($connect, $deleteItems);

// Then delete the order
$deleteOrder = "DELETE FROM orders WHERE id = '$id'";
mysqli_query($connect, $deleteOrder);

header('location: index.php');
?>