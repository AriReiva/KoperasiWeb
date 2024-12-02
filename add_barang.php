<?php
// Include koneksi database
include 'koneksi.php';

// Cek apakah form disubmit dengan metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $kodeBarang = mysqli_real_escape_string($conn, $_POST['KodeBarang']);
    $namaBarang = mysqli_real_escape_string($conn, $_POST['NamaBarang']);
    $jenisBarang = mysqli_real_escape_string($conn, $_POST['JenisBarang']);
    $satuan = mysqli_real_escape_string($conn, $_POST['Satuan']);
    $hargaBeli = mysqli_real_escape_string($conn, $_POST['HargaBeli']);
    $hargaJual = mysqli_real_escape_string($conn, $_POST['HargaJual']);
    $pemasokId = $_POST['Pemasok']; // Fetch selected pemasok ID

    // Query untuk insert data barang
    $query = "INSERT INTO barang (KodeBarang, NamaBarang, JenisBarang, Satuan, HargaBeli, HargaJual, Jumlah,id_pemasok)
              VALUES ('$kodeBarang', '$namaBarang', '$jenisBarang', '$satuan', '$hargaBeli', '$hargaJual', 0, '$pemasokId')";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        // Jika insert berhasil, redirect ke halaman barang
        header("Location: barang.php");
        exit();
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}

// Menutup koneksi database
mysqli_close($conn);
?>
