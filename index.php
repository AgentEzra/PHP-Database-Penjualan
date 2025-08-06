<?php
include 'connect.php';

$query = "SELECT * FROM tabel_kopi";
$result = mysqli_query($connect, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
</head>
<body>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>NAMA KOPI</th>
            <th>HARGA</th>
            <th>TOTAL CUP</th>
            <th>TOTAL HARGA</th>
            <th>WAKTU</th>
        </tr>
        
        <?php 
            $id = 0;
            while($row = mysqli_fetch_assoc($result)): $id++
            ?>
        <tr>
            <td><?=$id; ?></td>
            <td><?=$row['nama_kopi']; ?></td>
            <td><?=$row['harga']; ?></td>
            <td><?=$row['total_cup']; ?></td>
            <td><?=$row['total_harga']; ?></td>
            <td><?=$row['waktu_terjual']; ?></td>

            
            <td><a href="edit.php?id=<?=$row['id'];?>">Edit</a></td>
            <td><a href="delete.php?id=<?=$row['id'];?>">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="create.php">To Create</a>
</body>
</html>