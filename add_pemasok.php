<?php

session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $KodePemasok = $_POST['KodePemasok'];
    $NamaPemasok = $_POST['NamaPemasok'];
    $Alamat = $_POST['Alamat'];
    $NoTelp = $_POST['NoTelp'];
    $Email = $_POST['Email'];
    $AdminID = $_POST['AdminID']; // Ambil AdminID dari input

    if($AdminID != NULL){
        $query = "INSERT INTO Pemasok (KodePemasok, NamaPemasok, Alamat, NoTelp, Email, AdminID) 
              VALUES ('$KodePemasok', '$NamaPemasok', '$Alamat', '$NoTelp', '$Email', '$AdminID')";
    
        if (mysqli_query($conn, $query)) {
        header("Location: pemasok.php");
        } else {
        echo "Error: " . mysqli_error($conn);
        }
    }else{
        $query = "INSERT INTO Pemasok (KodePemasok, NamaPemasok, Alamat, NoTelp, Email) 
              VALUES ('$KodePemasok', '$NamaPemasok', '$Alamat', '$NoTelp', '$Email')";
    
        if (mysqli_query($conn, $query)) {
            header("Location: pemasok.php");
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>
