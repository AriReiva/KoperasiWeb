<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $kodePemasok = $_GET['id'];
    $query = "DELETE FROM pemasok WHERE KodePemasok = '$kodePemasok'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='pemasok.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.location='pemasok.php';</script>";
    }
} else {
    header("Location: pemasok.php");
    exit();
}
?>
