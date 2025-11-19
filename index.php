<?php
include 'connect.php';
include 'session.php';
redirectIfNotLoggedIn();

// Handle export to Excel for orders
if (isset($_GET['export_orders'])) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="orders_export_' . date('Y-m-d') . '.xls"');
    
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
    
    echo "ID\tNama Kopi\tHarga\tTotal Cup\tTotal Harga\tWaktu Terjual\n";
    
    $id = 0;
    while($row = mysqli_fetch_assoc($result)) {
        $id++;
        echo $id . "\t";
        echo $row['nama_kopi'] . "\t";
        // Remove "Rp " and commas for clean numeric values
        echo $row['harga'] . "\t"; // Raw number without formatting
        echo $row['total_cup'] . "\t";
        echo $row['total_harga'] . "\t"; // Raw number without formatting
        echo $row['waktu_terjual'] . "\n";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page - Coffee Shop</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleDashboard.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>☕ Cafe Ngoding</h1>
            <div class="user-info">
                Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <div class="navigation">
            <a href="dashboard.php">Dashboard</a>
            <a href="order.php">Order</a>
            
            <?php if (isUser()): ?>
                <a href="contact.php">Contact</a>
                <?php endif; ?>
                
                <?php if (isAdmin()): ?>
                <a href="index.php">Manage Orders</a>
                <a href="users.php">Manage Users</a>
                <a href="create.php">Create Order</a>
            <?php endif; ?>
        </div>

        <?php if (isAdmin()): ?>
        <!-- Export Button for Orders -->
        <div style="margin: 20px 0;">
            <a href="index.php?export_orders=1" class="export-btn">
                Export to Excel
            </a>
        </div>

        <table>
            <thead>
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
            </thead>
            <tbody>
                <?php 
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
                $id = 0;
                while($row = mysqli_fetch_assoc($result)): $id++
                ?>
                <tr>
                    <td><?=$id; ?></td>
                    <td><?=$row['nama_kopi']; ?></td>
                    <td>Rp <?=number_format($row['harga']); ?></td>
                    <td><?=$row['total_cup']; ?></td>
                    <td>Rp <?=number_format($row['total_harga']); ?></td>
                    <td><?=$row['waktu_terjual']; ?></td>
                    <td><a href="edit.php?id=<?=$row['order_id'];?>">Edit</a></td>
                    <td><a href="delete.php?id=<?=$row['order_id'];?>" onclick="return confirm('Yakin ingin menghapus?')">Delete</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <?php else: ?>
        <div class="user-welcome">
            <h2>Welcome to Coffee Shop! ☕</h2>
            <p>You are logged in as a regular user. Only administrators can manage orders.</p>
            <p>Feel free to browse around, but you won't be able to modify any data.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>