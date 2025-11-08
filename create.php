<?php
include 'connect.php';

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
            $error = 'pokoke error, tanyain atmin aja';
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

        $success = 'berhasil menambah data';

    } catch (mysqli_sql_exception $e){
        echo "eror pokoknya";
        $error = 'gagal deh pokoknya, tanya atmin suruh benerin: ' . $e->getMessage();
    }
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
    <form method="post">
        <label for="nama_kopi">Nama Kopi</label>
        <select name="nama_kopi" id="nama_kopi">
            <option value="option" disabled selected>Option</option>
            <option value="americano">Americano</option>
            <option value="cappucino">Cappucino</option>
            <option value="brown">Brown Sugar</option>
            <option value="caramel">Caramel Latte</option>
        </select>

        <label for="harga_kopi">Harga</label>
        <input type="number" name="harga_kopi" id="harga_kopi" value="<?=$hargaKopi ?>" readonly placeholder="Harga" >

        <label for="total_cup">Total Cup</label>
        <input type="number" name="total_cup" id="total_cup" placeholder="Total Cup" required>

        <label for="total_harga">Total Harga</label>
        <input type="number" name="total_harga" id="total_harga" value="<?=$totalHarga ?>" readonly placeholder="Total Harga">

        <button>Submit</button>
    </form>

        <a href="index.php">To Index</a>
    </div>

    <script src="script.js"></script>
</body>
</html>