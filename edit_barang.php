<?php
session_start();
include 'koneksi.php'; // Ensure this is your database connection

// Redirect to login if the session does not contain 'username'
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if form data is received
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect form data
    $kodeBarang = $_POST['KodeBarang'];
    $namaBarang = $_POST['NamaBarang'];
    $jenisBarang = $_POST['JenisBarang'];
    $satuan = $_POST['Satuan'];
    $hargaBeli = $_POST['HargaBeli'];
    $hargaJual = $_POST['HargaJual'];
    $pemasokId = $_POST['Pemasok']; // Fetch selected pemasok ID

    // Prepare the SQL statement
    $editQuery = "UPDATE barang 
                  SET NamaBarang = ?, JenisBarang = ?, Satuan = ?, 
                      HargaBeli = ?, HargaJual = ?, id_pemasok = ?
                  WHERE KodeBarang = ?";

    // Prepare the statement
    if ($stmt = mysqli_prepare($conn, $editQuery)) {
        
        // Bind the parameters to the prepared statement
        mysqli_stmt_bind_param($stmt, 'sssssss', $namaBarang, $jenisBarang, $satuan, $hargaBeli, $hargaJual, $pemasokId, $kodeBarang);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            header("Location: barang.php"); // Redirect on success
            exit();
        } else {
            // If execution fails, output the error
            echo "Error executing query: " . mysqli_stmt_error($stmt); 
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);
    } else {
        // If prepare statement fails, output the error
        echo "Error preparing query: " . mysqli_error($conn); 
    }
}

// Close the connection
mysqli_close($conn);
?>
