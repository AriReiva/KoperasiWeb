<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $kodeBarang = $_GET['id'];
    $query = "DELETE FROM barang WHERE KodeBarang = '$kodeBarang'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='barang.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.location='barang.php';</script>";
    }
} else {
    header("Location: barang.php");
    exit();
}
?>
