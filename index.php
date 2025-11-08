<?php
include 'connect.php';

$query = "SELECT 
            o.id as order_id,
            p.nama_kopi,
            p.harga,
            oi.quantity as total_cup,
            o.total_harga,
            o.waktu_terjual
          FROM orders o
          JOIN order_items oi ON o.id = oi.order_id
          JOIN products p ON oi.product_id = p.id
          ORDER BY o.waktu_terjual DESC";
$result = mysqli_query($connect, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link rel="stylesheet" href="style.css">
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
            <th>EDIT</th>
            <th>DELETE</th>
        </tr>
        
        <?php 
            $id = 0;
            while($row = mysqli_fetch_assoc($result)): $id++
            ?>
        <tr>
            <td><?=$id; ?></td>
            <td><?=$row['nama_kopi']; ?></td>
            <td><?=number_format($row['harga']); ?></td>
            <td><?=$row['total_cup']; ?></td>
            <td><?=number_format($row['total_harga']); ?></td>
            <td><?=$row['waktu_terjual']; ?></td>

            
            <td><a href="edit.php?id=<?=$row['order_id'];?>">Edit</a></td>
            <td><a href="delete.php?id=<?=$row['order_id'];?>" onclick="return confirm('Yakin ingin menghapus?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="create.php">To Create</a>
</body>
</html>