<?php
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'kopi_db';

$connect = new mysqli ($host, $user, $password, $db);

if ($connect->connect_error){
    echo 'error sql';
}
?>