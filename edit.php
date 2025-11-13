<?php
include 'connect.php';
include 'session.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin(); // Only admin can edit

$postNamaMakanan = '';
$postHargaMakanan = '';
$postTotalPorsi = '';
$postTotalHarga = '';

$id = $_GET['id'];

// Get order data with joins
$query = "SELECT 
            o.id as order_id,
            p.nama_makanan,
            p.harga,
            oi.quantity as total_porsi,
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
    $postNamaMakanan = $_POST['nama_makanan'];
    $postTotalPorsi = $_POST['total_porsi'];
    
    // Get price based on food type
    switch ($postNamaMakanan){
        case 'ichiraku_ramen':
            $postHargaMakanan = 18000;
            break;
        case 'pasta_carbonara':
            $postHargaMakanan = 16000;
            break;
        case 'samyang_noodle':
            $postHargaMakanan = 12000;
            break;
        case 'mie_ayam':
            $postHargaMakanan = 12000;
            break;
    }
    
    $postTotalHarga = $postTotalPorsi * $postHargaMakanan;

    // Update product if changed
    if($data['nama_makanan'] != $postNamaMakanan) {
        $updateProduct = "UPDATE products SET nama_makanan = '$postNamaMakanan', harga = '$postHargaMakanan' WHERE id = '{$data['product_id']}'";
        mysqli_query($connect, $updateProduct);
    }

    // Update order and order items
    $updateOrder = "UPDATE orders SET total_harga = '$postTotalHarga' WHERE id = '$id'";
    mysqli_query($connect, $updateOrder);

    $updateOrderItem = "UPDATE order_items SET quantity = '$postTotalPorsi', subtotal = '$postTotalHarga' WHERE order_id = '$id'";
    mysqli_query($connect, $updateOrderItem);

    // Refresh data
    $query = "SELECT 
                o.id as order_id,
                p.nama_makanan,
                p.harga,
                oi.quantity as total_porsi,
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
        <label for="nama_makanan">Nama Makanan</label>
        <select name="nama_makanan" id="nama_makanan">
            <option value="ichiraku_ramen" <?= $data['nama_makanan'] == 'ichiraku_ramen' ? 'selected' : '' ?>>Ichiraku Ramen</option>
            <option value="pasta_carbonara" <?= $data['nama_makanan'] == 'pasta_carbonara' ? 'selected' : '' ?>>Pasta Carbonara</option>
            <option value="samyang_noodle" <?= $data['nama_makanan'] == 'samyang_noodle' ? 'selected' : '' ?>>Samyang Noodle</option>
            <option value="mie_ayam" <?= $data['nama_makanan'] == 'mie_ayam' ? 'selected' : '' ?>>Mie Ayam</option>
        </select>

        <label for="harga_makanan">Harga</label>
        <input type="number" name="harga_makanan" id="harga_makanan" value="<?=$data['harga']; ?>" readonly placeholder="Harga" >

        <label for="total_porsi">Total Porsi</label>
        <input type="number" name="total_porsi" id="total_porsi" value="<?=$data['total_porsi']; ?>" placeholder="Total Porsi" required>

        <label for="total_harga">Total Harga</label>
        <input type="number" name="total_harga" id="total_harga" value="<?=$data['total_harga']; ?>" readonly placeholder="Total Harga">

        <button>Edit</button>
    </form>

    <a href="index.php">To Index</a>
        </div>

    <script>
        // JavaScript to update prices dynamically
        document.getElementById('nama_makanan').addEventListener('change', function() {
            const foodType = this.value;
            let price = 0;
            
            switch(foodType) {
                case 'ichiraku_ramen':
                    price = 18000;
                    break;
                case 'pasta_carbonara':
                    price = 16000;
                    break;
                case 'samyang_noodle':
                    price = 12000;
                    break;
                case 'mie_ayam':
                    price = 12000;
                    break;
                default:
                    price = 0;
            }
            
            document.getElementById('harga_makanan').value = price;
            calculateTotal();
        });
        
        document.getElementById('total_porsi').addEventListener('input', calculateTotal);
        
        function calculateTotal() {
            const price = parseInt(document.getElementById('harga_makanan').value) || 0;
            const portions = parseInt(document.getElementById('total_porsi').value) || 0;
            const total = price * portions;
            
            document.getElementById('total_harga').value = total;
        }
    </script>
</body>
</html>