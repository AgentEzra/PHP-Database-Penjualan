<?php
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'makanan_sukas';

$connect = new mysqli ($host, $user, $password, $db);

if ($connect->connect_error){
    echo 'error sql';
}
?>