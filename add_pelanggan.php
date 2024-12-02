<?php
// Include your database connection file
include 'koneksi.php'; // Make sure you adjust this to the actual location of your database connection file

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get data from POST
    $kodePelanggan = $_POST['KodePelanggan'];
    $namaPelanggan = $_POST['NamaPelanggan'];
    $alamatPelanggan = $_POST['AlamatPelanggan'];
    $noTelpPelanggan = $_POST['NoTelpPelanggan'];
    $adminID = $_POST['AdminID'];

    // Validate if all fields are filled (optional)
    if (empty($kodePelanggan) || empty($namaPelanggan) || empty($alamatPelanggan) || empty($noTelpPelanggan)) {
        echo "All fields are required!";
        exit;
    }

    if($adminID != NULL){
        // SQL query to insert data into pelanggan table
        $sql = "INSERT INTO pelanggan (KodePelanggan, NamaPelanggan, AlamatPelanggan, NoTelpPelanggan, AdminId) 
        VALUES ('$kodePelanggan', '$namaPelanggan', '$alamatPelanggan', '$noTelpPelanggan', '$adminID')";

        // Execute the query
        if (mysqli_query($conn, $sql)) {
        echo "New customer added successfully!";
        // You can redirect back to the list page or show a success message
        header('Location: pelanggan.php'); // Adjust this as needed
        } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }else{
        // SQL query to insert data into pelanggan table
        $sql = "INSERT INTO pelanggan (KodePelanggan, NamaPelanggan, AlamatPelanggan, NoTelpPelanggan) 
        VALUES ('$kodePelanggan', '$namaPelanggan', '$alamatPelanggan', '$noTelpPelanggan')";
 
        // Execute the query
        if (mysqli_query($conn, $sql)) {
        echo "New customer added successfully!";
        // You can redirect back to the list page or show a success message
        header('Location: pelanggan.php'); // Adjust this as needed
        } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
    
}

// Close the database connection
mysqli_close($conn);
?>
