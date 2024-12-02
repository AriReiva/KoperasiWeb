<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $kodePelanggan = $_GET['id'];
    $query = "DELETE FROM pelanggan WHERE KodePelanggan = '$kodePelanggan'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='pelanggan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.location='pelanggan.php';</script>";
    }
} else {
    header("Location: pelanggan.php");
    exit();
}
?>
