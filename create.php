<?php
include 'connect.php';
include 'session.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin(); // Only admin can create orders

$success = '';
$error = '';

$namaKopi = '';
$hargaKopi = '';
$totalCup = '';
$totalHarga = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $namaKopi = $_POST['nama_kopi'];
    $totalCup = $_POST['total_cup'];

    switch ($namaKopi){
        case 'americano':
            $hargaKopi = 8000;
            break;
        case 'cappucino':
            $hargaKopi = 10000;
            break;
        case 'brown':
            $hargaKopi = 12000;
            break;
        case 'caramel':
            $hargaKopi = 14000;
            break;
        default:
            $error = 'Invalid coffee selection';
            exit($error);
    }

    $totalHarga = $totalCup * $hargaKopi;

    try {
        // First, get or create the product
        $productQuery = "SELECT id FROM products WHERE nama_kopi = '$namaKopi'";
        $productResult = mysqli_query($connect, $productQuery);
        
        if(mysqli_num_rows($productResult) == 0) {
            // Insert new product if it doesn't exist
            $insertProduct = "INSERT INTO products (nama_kopi, harga) VALUES ('$namaKopi', '$hargaKopi')";
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
        $orderItemQuery = "INSERT INTO order_items (order_id, product_id, quantity, subtotal) VALUES ('$orderId', '$productId', '$totalCup', '$totalHarga')";
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
    <title>Create Order - Coffee Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>☕ Create New Order</h1>
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
                    <label for="nama_kopi">Nama Kopi</label>
                    <select name="nama_kopi" id="nama_kopi" required>
                        <option value="" disabled selected>Select Coffee Type</option>
                        <option value="americano" <?= $namaKopi == 'americano' ? 'selected' : '' ?>>Americano</option>
                        <option value="cappucino" <?= $namaKopi == 'cappucino' ? 'selected' : '' ?>>Cappucino</option>
                        <option value="brown" <?= $namaKopi == 'brown' ? 'selected' : '' ?>>Brown Sugar</option>
                        <option value="caramel" <?= $namaKopi == 'caramel' ? 'selected' : '' ?>>Caramel Latte</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="harga_kopi">Harga</label>
                    <input type="number" name="harga_kopi" id="harga_kopi" value="<?= $hargaKopi ?>" readonly placeholder="Harga">
                </div>

                <div class="form-group">
                    <label for="total_cup">Total Cup</label>
                    <input type="number" name="total_cup" id="total_cup" value="<?= $totalCup ?>" placeholder="Total Cup" required min="1">
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
        document.getElementById('nama_kopi').addEventListener('change', function() {
            const coffeeType = this.value;
            let price = 0;
            
            switch(coffeeType) {
                case 'americano':
                    price = 8000;
                    break;
                case 'cappucino':
                    price = 10000;
                    break;
                case 'brown':
                    price = 12000;
                    break;
                case 'caramel':
                    price = 14000;
                    break;
                default:
                    price = 0;
            }
            
            document.getElementById('harga_kopi').value = price;
            calculateTotal();
        });
        
        document.getElementById('total_cup').addEventListener('input', calculateTotal);
        
        function calculateTotal() {
            const price = parseInt(document.getElementById('harga_kopi').value) || 0;
            const cups = parseInt(document.getElementById('total_cup').value) || 0;
            const total = price * cups;
            
            document.getElementById('total_harga').value = total;
        }
    </script>
</body>
</html>