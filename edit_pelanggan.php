<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kodePelanggan = $_POST['KodePelanggan'];
    $namaPelanggan = $_POST['NamaPelanggan'];
    $alamatPelanggan = $_POST['AlamatPelanggan'];
    $noTelpPelanggan = $_POST['NoTelpPelanggan'];
    $idAdmin = $_POST['AdminID'];

    if($idAdmin != NULL){
        $query = "UPDATE pelanggan SET 
        NamaPelanggan = ?, 
        AlamatPelanggan = ?, 
        NoTelpPelanggan = ?,
        AdminID = ?
        WHERE KodePelanggan = ?";
    
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssii", $namaPelanggan, $alamatPelanggan, $noTelpPelanggan, $idAdmin ,$kodePelanggan);
    }else{

        $query = "UPDATE pelanggan SET 
                    NamaPelanggan = ?, 
                    AlamatPelanggan = ?, 
                    NoTelpPelanggan = ? 
                WHERE KodePelanggan = ?";
                
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $namaPelanggan, $alamatPelanggan, $noTelpPelanggan, $kodePelanggan);
    }

    if (mysqli_stmt_execute($stmt)) {
        header("Location: pelanggan.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
