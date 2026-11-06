<?php
require 'koneksi.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    exit('ID produk tidak valid.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama_produk'] ?? '');
    $kategori = (int)($_POST['id_kategori'] ?? 0);
    $harga = (float)($_POST['harga'] ?? 0);
    $stok = (int)($_POST['stok'] ?? -1);

    if ($nama === '' || $kategori < 1 || $harga <= 0 || $stok < 0) {
        exit('Data produk tidak valid.');
    }

    $sql = "UPDATE produk
            SET id_kategori = ?,
                nama_produk = ?,
                harga = ?,
                stok = ?
            WHERE id_produk = ?";

    $stmt = mysqli_prepare($koneksi, $sql);

    mysqli_stmt_bind_param(
        $stmt, 'isdii',
        $kategori, $nama, $harga, $stok, $id
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header('Location: index.php');
    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM produk WHERE id_produk = ?"
);

mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($hasil);

mysqli_stmt_close($stmt);

if (!$produk) {
    exit('Produk tidak ditemukan.');
}

$kategori = mysqli_query(
    $koneksi,
    "SELECT * FROM kategori ORDER BY nama_kategori"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
</head>
<body>

<h1>Edit Produk</h1>

<form method="post">

    <p>
        Nama Produk:<br>
        <input type="text" name="nama_produk"
        value="<?= e($produk['nama_produk']) ?>" required>
    </p>

    <p>
        Kategori:<br>
        <select name="id_kategori" required>
            <?php while ($k = mysqli_fetch_assoc($kategori)): ?>
                <option value="<?= e($k['id_kategori']) ?>"
                    <?= $produk['id_kategori'] == $k['id_kategori']
                    ? 'selected' : '' ?>>
                    <?= e($k['nama_kategori']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </p>

    <p>
        Harga:<br>
        <input type="number" name="harga"
        value="<?= e($produk['harga']) ?>"
        min="0.01" step="0.01" required>
    </p>

    <p>
        Stok:<br>
        <input type="number" name="stok"
        value="<?= e($produk['stok']) ?>"
        min="0" required>
    </p>

    <button type="submit">Simpan Perubahan</button>

</form>

<p><a href="index.php">Kembali</a></p>

</body>
</html>