<?php
include 'connect.php';

$success = '';
$error = '';
$setValueTotal = 0;

$namaKopi = '';
$hargaKopi = '';
$totalCup = '';
$totalHarga = '';
$waktuTerjual = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $namaKopi = $_POST['nama_kopi'];
    $hargaKopi = $_POST['harga_kopi'];
    $totalCup = $_POST['total_cup'];
    $totalHarga = $_POST['total_harga'];
    $waktuTerjual = date('Y-m-d H:i:s');

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
        $query = "INSERT INTO tabel_kopi (nama_kopi, harga, total_cup, total_harga, waktu_terjual) VALUES ('$namaKopi', '$hargaKopi', '$totalCup', '$totalHarga', '$waktuTerjual')";

        $result = mysqli_query($connect, $query);

        $success = 'berhasil menambah data';

    } catch (mysqli_sql_exception $e){
        echo "eror pokoknya";

        $error = 'gagal deh pokoknya, tanya atmin suruh benerin';
    }
} 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Page</title>
</head>
<body>
    <form method="post">
        <label for="nama_kopi">Nama Kopi</label>
        <select name="nama_kopi" id="nama_kopi">
            <option value="americano">Americano</option>
            <option value="cappucino">Cappucino</option>
            <option value="brown">Brown Sugar</option>
            <option value="caramel">Caramel Latte</option>
        </select>

        <label for="harga_kopi">Harga</label>
        <input type="number" name="harga_kopi" id="harga_kopi" value="<?=$hargaKopi ?>" readonly placeholder="Harga">

        <label for="total_cup">Total Cup</label>
        <input type="number" name="total_cup" id="total_cup" placeholder="Total Cup">

        <label for="total_harga">Total Harga</label>
        <input type="number" name="total_harga" id="total_harga" value="<?=$totalHarga ?>" readonly placeholder="Total Harga">

        <button>Submit</button>
    </form>

    <a href="index.php">To Index</a>

    <script src="script.js"></script>
</body>
</html>