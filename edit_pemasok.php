<?php
// Start session to manage user authentication
session_start();

// Include database connection file
include 'koneksi.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated supplier data from the form
    $kodePemasok = $_POST['KodePemasok'];  // Supplier code (from modal)
    $namaPemasok = $_POST['NamaPemasok'];  // Supplier name
    $alamatPemasok = $_POST['Alamat'];     // Supplier address
    $noTelpPemasok = $_POST['NoTelp'];     // Supplier phone number
    $emailPemasok = $_POST['Email'];       // Supplier email
    $idAdmin = $_POST['AdminID'];

    if($idAdmin != NULL){
        $query = "UPDATE pemasok SET 
        NamaPemasok = ?, 
        Alamat = ?, 
        NoTelp = ?, 
        Email = ?,
        AdminID = ?
      WHERE KodePemasok = ?";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $query);

    // Bind the parameters - note that KodePemasok is assumed to be an integer
    mysqli_stmt_bind_param($stmt, "ssssii", $namaPemasok, $alamatPemasok, $noTelpPemasok, $emailPemasok, $idAdmin ,$kodePemasok);
    }else{

    // Prepare the SQL UPDATE query
    $query = "UPDATE pemasok SET 
                NamaPemasok = ?, 
                Alamat = ?, 
                NoTelp = ?, 
                Email = ? 
              WHERE KodePemasok = ?";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $query);

    // Bind the parameters - note that KodePemasok is assumed to be an integer
    mysqli_stmt_bind_param($stmt, "ssssi", $namaPemasok, $alamatPemasok, $noTelpPemasok, $emailPemasok, $kodePemasok);
    
    }
    // Execute the query
    if (mysqli_stmt_execute($stmt)) {
        header("Location: pemasok.php"); // Redirect after successful update
        exit();
    } else {
        echo "Error: " . mysqli_error($conn); // Display error message if something goes wrong
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}
?>
