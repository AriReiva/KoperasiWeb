<?php
include 'koneksi.php';
// Cek koneksi
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['id']) && isset($_GET['jumlah'])) {
    $id_barang = $_GET['id'];
    $jumlah = $_GET['jumlah'];

    // Ambil data barang berdasarkan id_barang menggunakan prepared statement
    $query = "SELECT * FROM barang WHERE KodeBarang = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $id_barang); // 's' untuk string
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $barang = mysqli_fetch_assoc($result);

    if ($barang) {
        $id_pemasok = $barang['id_pemasok'];
        $pesan = "Stok {$barang['NamaBarang']} telah habis. Mohon kirimkan sebanyak {$jumlah}.";

        // Simpan pesan ke dalam tabel pesan menggunakan prepared statement
        $query_pesan = "INSERT INTO pesan (id_barang, id_pemasok, pesan, status, tanggal) VALUES (?, ?, ?, 'pending', NOW())";
        $stmt_pesan = mysqli_prepare($conn, $query_pesan);
        mysqli_stmt_bind_param($stmt_pesan, "iis", $id_barang, $id_pemasok, $pesan); // 'iis': id_barang (int), id_pemasok (int), pesan (string)
        mysqli_stmt_execute($stmt_pesan);

        // Redirect ke halaman utama
        header("Location: stockBarang.php");
        exit();
    }

    mysqli_stmt_close($stmt);
    mysqli_stmt_close($stmt_pesan);
}

// Tutup koneksi
mysqli_close($conn);
?>
