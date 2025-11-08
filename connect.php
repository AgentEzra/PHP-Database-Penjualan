<?php
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'sukas_kopi';

$connect = new mysqli ($host, $user, $password, $db);

if ($connect->connect_error){
    echo 'error sql';
}
?>