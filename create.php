<?php
include 'connect.php';
include 'session.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin(); // Only admin can create orders

$success = '';
$error = '';

$namaMakanan = '';
$hargaMakanan = '';
$totalPorsi = '';
$totalHarga = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $namaMakanan = $_POST['nama_makanan'];
    $totalPorsi = $_POST['total_porsi'];

    switch ($namaMakanan){
        case 'ichiraku_ramen':
            $hargaMakanan = 18000;
            break;
        case 'pasta_carbonara':
            $hargaMakanan = 16000;
            break;
        case 'samyang_noodle':
            $hargaMakanan = 12000;
            break;
        case 'mie_ayam':
            $hargaMakanan = 12000;
            break;
        default:
            $error = 'Invalid food selection';
            exit($error);
    }

    $totalHarga = $totalPorsi * $hargaMakanan;

    try {
        // First, get or create the product
        $productQuery = "SELECT id FROM products WHERE nama_makanan = '$namaMakanan'";
        $productResult = mysqli_query($connect, $productQuery);
        
        if(mysqli_num_rows($productResult) == 0) {
            // Insert new product if it doesn't exist
            $insertProduct = "INSERT INTO products (nama_makanan, harga) VALUES ('$namaMakanan', '$hargaMakanan')";
            mysqli_query($connect, $insertProduct);
            $productId = mysqli_insert_id($connect);
        } else {
            $productData = mysqli_fetch_assoc($productResult);
            $productId = $productData['id'];
        }

        // Get the default customer (walk-in)
        $customerQuery = "SELECT id FROM customers WHERE nama_customer = 'Walk-in Customer'";
        $customerResult = mysqli_query($connect, $customerQuery);
        $customerData = mysqli_fetch_assoc($customerResult);
        $customerId = $customerData['id'];

        // Create order
        $orderQuery = "INSERT INTO orders (customer_id, total_harga) VALUES ('$customerId', '$totalHarga')";
        mysqli_query($connect, $orderQuery);
        $orderId = mysqli_insert_id($connect);

        // Create order item
        $orderItemQuery = "INSERT INTO order_items (order_id, product_id, quantity, subtotal) VALUES ('$orderId', '$productId', '$totalPorsi', '$totalHarga')";
        mysqli_query($connect, $orderItemQuery);

        $success = 'Order created successfully!';

    } catch (mysqli_sql_exception $e){
        $error = 'Failed to create order: ' . $e->getMessage();
    }
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order - Food Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍜 Create New Order</h1>
            <div class="user-info">
                Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <div class="navigation">
            <a href="dashboard.php">Dashboard</a>
            <a href="index.php">Orders</a>
            <?php if (isAdmin()): ?>
                <a href="users.php">Manage Users</a>
                <a href="create.php">Create Order</a>
            <?php endif; ?>
        </div>

        <div class="wrapper">
            <?php if ($success): ?>
                <div class="success"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label for="nama_makanan">Nama Makanan</label>
                    <select name="nama_makanan" id="nama_makanan" required>
                        <option value="" disabled selected>Select Food Type</option>
                        <option value="ichiraku_ramen" <?= $namaMakanan == 'ichiraku_ramen' ? 'selected' : '' ?>>Ichiraku Ramen</option>
                        <option value="pasta_carbonara" <?= $namaMakanan == 'pasta_carbonara' ? 'selected' : '' ?>>Pasta Carbonara</option>
                        <option value="samyang_noodle" <?= $namaMakanan == 'samyang_noodle' ? 'selected' : '' ?>>Samyang Noodle</option>
                        <option value="mie_ayam" <?= $namaMakanan == 'mie_ayam' ? 'selected' : '' ?>>Mie Ayam</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="harga_makanan">Harga</label>
                    <input type="number" name="harga_makanan" id="harga_makanan" value="<?= $hargaMakanan ?>" readonly placeholder="Harga">
                </div>

                <div class="form-group">
                    <label for="total_porsi">Total Porsi</label>
                    <input type="number" name="total_porsi" id="total_porsi" value="<?= $totalPorsi ?>" placeholder="Total Porsi" required min="1">
                </div>

                <div class="form-group">
                    <label for="total_harga">Total Harga</label>
                    <input type="number" name="total_harga" id="total_harga" value="<?= $totalHarga ?>" readonly placeholder="Total Harga">
                </div>

                <button type="submit">Create Order</button>
            </form>
            <a class="a-go" href="index.php">Back to Orders</a>
        </div>
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