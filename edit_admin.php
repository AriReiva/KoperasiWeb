<?php
session_start();
include 'koneksi.php';

// Periksa apakah data POST diterima
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id = $_POST['ID'];
    $username = mysqli_real_escape_string($conn, $_POST['Username']);
    $password = mysqli_real_escape_string($conn, $_POST['Password']);
    $level = mysqli_real_escape_string($conn, $_POST['Level']);

    // Validasi input kosong
    if (empty($id) || empty($username) || empty($password) || empty($level)) {
        $_SESSION['error'] = "Semua field harus diisi.";
        header("Location: admin.php"); // Ganti `admin.php` dengan halaman data admin Anda
        exit();
    }

    // Update data di database
    $query = "UPDATE admin 
              SET Username = '$username', Password = '$password', Level = '$level'
              WHERE ID = '$id'";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Data admin berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Gagal memperbarui data admin: " . mysqli_error($conn);
    }

    // Redirect kembali ke halaman data admin
    header("Location: admin.php"); // Ganti `admin.php` dengan halaman data admin Anda
    exit();
} else {
    // Jika file diakses langsung tanpa POST, redirect ke halaman admin
    header("Location: admin.php");
    exit();
}
?>
