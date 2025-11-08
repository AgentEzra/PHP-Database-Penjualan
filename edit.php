<?php
include 'connect.php';

$postNamaKopi = '';
$postHargaKopi = '';
$postTotalCup = '';
$postTotalHarga = '';

$id = $_GET['id'];

// Get order data with joins
$query = "SELECT 
            o.id as order_id,
            p.nama_kopi,
            p.harga,
            oi.quantity as total_cup,
            o.total_harga,
            oi.id as item_id,
            p.id as product_id
          FROM orders o
          JOIN order_items oi ON o.id = oi.order_id
          JOIN products p ON oi.product_id = p.id
          WHERE o.id = '$id'";
$result = mysqli_query($connect, $query);
$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $postNamaKopi = $_POST['nama_kopi'];
    $postTotalCup = $_POST['total_cup'];
    
    // Get price based on coffee type
    switch ($postNamaKopi){
        case 'americano':
            $postHargaKopi = 8000;
            break;
        case 'cappucino':
            $postHargaKopi = 10000;
            break;
        case 'brown':
            $postHargaKopi = 12000;
            break;
        case 'caramel':
            $postHargaKopi = 14000;
            break;
    }
    
    $postTotalHarga = $postTotalCup * $postHargaKopi;

    // Update product if changed
    if($data['nama_kopi'] != $postNamaKopi) {
        $updateProduct = "UPDATE products SET nama_kopi = '$postNamaKopi', harga = '$postHargaKopi' WHERE id = '{$data['product_id']}'";
        mysqli_query($connect, $updateProduct);
    }

    // Update order and order items
    $updateOrder = "UPDATE orders SET total_harga = '$postTotalHarga' WHERE id = '$id'";
    mysqli_query($connect, $updateOrder);

    $updateOrderItem = "UPDATE order_items SET quantity = '$postTotalCup', subtotal = '$postTotalHarga' WHERE order_id = '$id'";
    mysqli_query($connect, $updateOrderItem);

    // Refresh data
    $query = "SELECT 
                o.id as order_id,
                p.nama_kopi,
                p.harga,
                oi.quantity as total_cup,
                o.total_harga,
                oi.id as item_id,
                p.id as product_id
              FROM orders o
              JOIN order_items oi ON o.id = oi.order_id
              JOIN products p ON oi.product_id = p.id
              WHERE o.id = '$id'";
    $result = mysqli_query($connect, $query);
    $data = mysqli_fetch_assoc($result);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
    <form method="post">
        <label for="nama_kopi">Nama Kopi</label>
        <select name="nama_kopi" id="nama_kopi">
            <option value="americano" <?= $data['nama_kopi'] == 'americano' ? 'selected' : '' ?>>Americano</option>
            <option value="cappucino" <?= $data['nama_kopi'] == 'cappucino' ? 'selected' : '' ?>>Cappucino</option>
            <option value="brown" <?= $data['nama_kopi'] == 'brown' ? 'selected' : '' ?>>Brown Sugar</option>
            <option value="caramel" <?= $data['nama_kopi'] == 'caramel' ? 'selected' : '' ?>>Caramel Latte</option>
        </select>

        <label for="harga_kopi">Harga</label>
        <input type="number" name="harga_kopi" id="harga_kopi" value="<?=$data['harga']; ?>" readonly placeholder="Harga" >

        <label for="total_cup">Total Cup</label>
        <input type="number" name="total_cup" id="total_cup" value="<?=$data['total_cup']; ?>" placeholder="Total Cup" required>

        <label for="total_harga">Total Harga</label>
        <input type="number" name="total_harga" id="total_harga" value="<?=$data['total_harga']; ?>" readonly placeholder="Total Harga">

        <button>Edit</button>
    </form>

    <a href="index.php">To Index</a>
        </div>

    <script src="script.js"></script>
</body>
</html>