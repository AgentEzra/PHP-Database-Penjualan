<?php
include 'connect.php';

$id = $_GET['id'];

$query = "DELETE FROM tabel_kopi WHERE id = '$id'";
$result = mysqli_query($connect, $query);

header ('location: index.php');
?>