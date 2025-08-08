<?php
include 'connect.php';

$postNamaKopi = '';
$postHargaKopi = '';
$postTotalCup = '';
$postTotalHarga = '';

$id = $_GET['id'];
$query = "SELECT * FROM tabel_kopi WHERE id = '$id'";
$result = mysqli_query($connect, $query);
$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $postNamaKopi = $_POST['nama_kopi'];
    $postHargaKopi = $_POST['harga_kopi'];
    $postTotalCup = $_POST['total_cup'];
    $postTotalHarga = $_POST['total_harga'];

    $postQuery = "UPDATE tabel_kopi SET nama_kopi = '$postNamaKopi', harga = '$postHargaKopi', total_cup = '$postTotalCup', total_harga = '$postTotalHarga' WHERE id = '$id'";
    $result = mysqli_query($connect, $postQuery);

    $query = "SELECT * FROM tabel_kopi WHERE id = '$id'";
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
        <select name="nama_kopi" id="nama_kopi" value="$namaKopi">
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