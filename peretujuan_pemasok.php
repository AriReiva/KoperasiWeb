<?php
include 'koneksi.php';

session_start();

$id = $_SESSION['ID']; // Pastikan ID ada dalam session

// Query untuk mengambil kodePemasok berdasarkan AdminId
$query_pemasok = "SELECT kodePemasok FROM pemasok WHERE AdminId = '$id'";
$result_pemasok = mysqli_query($conn, $query_pemasok);

// Memeriksa apakah query berhasil dijalankan
if ($result_pemasok) {
    $supplierData = mysqli_fetch_assoc($result_pemasok);
    if ($supplierData) {
        $supplierID = $supplierData['kodePemasok']; // Mengambil kodePemasok
    } else {
        echo "Data pemasok tidak ditemukan.";
        exit;
    }
} else {
    echo "Query gagal dijalankan: " . mysqli_error($conn);
    exit;
}

if (isset($_POST['setujui'])) {
    $id_pesan = $_POST['id_pesan'];
    $jumlah_kirim = $_POST['jumlah_kirim'];

    // Update status pesan ke 'approved'
    $query = "UPDATE pesan SET status = 'confirm' WHERE id_pesan = ? AND id_pemasok = ?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt === false) {
        die('Query preparation failed: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "ii", $id_pesan, $supplierID);
    mysqli_stmt_execute($stmt);

    // Ambil data barang dari pesan
    $query_pesan = "SELECT id_barang FROM pesan WHERE id_pesan = ? AND id_pemasok = ?";
    $stmt_pesan = mysqli_prepare($conn, $query_pesan);
    mysqli_stmt_bind_param($stmt_pesan, "ii", $id_pesan, $supplierID);
    mysqli_stmt_execute($stmt_pesan);
    $result_pesan = mysqli_stmt_get_result($stmt_pesan);

    if ($pesan = mysqli_fetch_assoc($result_pesan)) {
        $produk_id = $pesan['id_barang'];

        // Ambil harga beli dari tabel barang
        $query_harga = "SELECT HargaBeli, Jumlah FROM barang WHERE KodeBarang = ? AND id_pemasok = ?";
        $stmt_harga = mysqli_prepare($conn, $query_harga);
        mysqli_stmt_bind_param($stmt_harga, "ii", $produk_id, $supplierID);
        mysqli_stmt_execute($stmt_harga);
        $result_harga = mysqli_stmt_get_result($stmt_harga);

        if ($barang = mysqli_fetch_assoc($result_harga)) {
            $harga_beli = $barang['HargaBeli'];
            $stok_sekarang = $barang['Jumlah'];

            // Hitung total harga
            $total_harga = $jumlah_kirim * $harga_beli;

            // Update stok barang
            $new_stock = $stok_sekarang + $jumlah_kirim;
            $update_stock_query = "UPDATE barang SET Jumlah = ? WHERE KodeBarang = ? AND id_pemasok = ?";
            $stmt_stock = mysqli_prepare($conn, $update_stock_query);
            mysqli_stmt_bind_param($stmt_stock, "iii", $new_stock, $produk_id, $supplierID);
            mysqli_stmt_execute($stmt_stock);

            // Ambil nomor order terakhir sebagai integer
            $query_last_order = "SELECT MAX(NomorOrder) AS LastOrder FROM transaksipemasok";
            $result_last_order = mysqli_query($conn, $query_last_order);

            if ($result_last_order) {
                $row = mysqli_fetch_assoc($result_last_order);
                $last_order = $row['LastOrder'] ? $row['LastOrder'] : 0; // Jika NULL, mulai dari 0
                $next_order = $last_order + 1; // Tambahkan 1 untuk nomor order berikutnya
                $nomor_order = $next_order; // Nomor order tanpa format "TRX" atau padding
            } else {
                die("Gagal mengambil nomor order terakhir: " . mysqli_error($conn));
            }

            $query_last_order = "SELECT MAX(NomorPO) AS LastPO FROM transaksipemasok";
            $result_last_order = mysqli_query($conn, $query_last_order);

            $nomor_PO = $nomor_order;

            // Masukkan data ke tabel transaksi
            $tanggal_order = date("Y-m-d");
            $insert_transaksi_query = "INSERT INTO transaksipemasok
                (NomorOrder, TanggalPO, NomorPO ,KodeSupplier, KodeBarang, Jumlah, TotalHarga) 
                VALUES (?, ?, ?, ?, ?, ?,?)";
            $stmt_transaksi = mysqli_prepare($conn, $insert_transaksi_query);
            mysqli_stmt_bind_param(
                $stmt_transaksi, 
                "isiiiii", 
                $nomor_order, 
                $tanggal_order, 
                $nomor_PO,
                $supplierID, 
                $produk_id, 
                $jumlah_kirim, 
                $total_harga
            );
            mysqli_stmt_execute($stmt_transaksi);
        } else {
            echo "Data harga beli barang tidak ditemukan.";
        }
        mysqli_stmt_close($stmt_harga);
    }

    mysqli_stmt_close($stmt);
    mysqli_stmt_close($stmt_pesan);
}

// Query untuk mengambil pesan yang menunggu persetujuan untuk pemasok tertentu
$query_pesan = "SELECT id_pesan, pesan FROM pesan WHERE id_pemasok = ? AND status = 'approved'";
$stmt_pesan = mysqli_prepare($conn, $query_pesan);
mysqli_stmt_bind_param($stmt_pesan, "i", $supplierID);
mysqli_stmt_execute($stmt_pesan);
$result_pesan = mysqli_stmt_get_result($stmt_pesan);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Pemasok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #343a40;
            color: white;
        }
        .card-body {
            background-color: #f8f9fa;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">SISTEM INFORMASI KOPERASI</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel"><?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo htmlspecialchars($_SESSION['level']); ?>)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                <?php if ($_SESSION['level'] == 'Admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Master
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="pelanggan.php">Pelanggan</a></li>
                        <li><a class="dropdown-item" href="pemasok.php">Pemasok</a></li>
                        <li><a class="dropdown-item" href="barang.php">Barang</a></li>
                        <li><a class="dropdown-item" href="admin.php">Admin</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['level'] == 'Pelanggan'||$_SESSION['level'] == 'Pemasok'||$_SESSION['level'] == 'Admin'): ?>
                    <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            History Transaksi
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                            <?php if ($_SESSION['level'] == 'Pelanggan'): ?>
                                <li><a class="dropdown-item" href="transaksi_pelanggan.php">Data Transaksi</a></li>
                            <?php endif; ?>
                            <?php if ($_SESSION['level'] == 'Pemasok'): ?>
                                <li><a class="dropdown-item" href="transaksi_pemasok.php">Data Transaksi</a></li>
                            <?php endif; ?>
                            <?php if ($_SESSION['level'] == 'Admin'): ?>
                                <li><a class="dropdown-item" href="transaksi_admin.php">Data Transaksi</a></li>
                            <?php endif; ?>
                            </ul>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['level'] == 'Pelanggan'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Product
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="productlist.php">Product List</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['level'] == 'Manajer'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Income
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="PengeluaranDanPemasukan.php">Pengeluaran dan Pemasukan</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['level'] == 'Manajer'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Manajemen
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="stockBarang.php">Stock Barang</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['level'] == 'Pemasok'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Data Pesanan
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="peretujuan_pemasok.php">Permintaan Barang </a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['level'] == 'Admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Data Pesanan
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="persetujuan_admin.php">Permintaan Barang </a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Account
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                    <?php if ($_SESSION['level'] == 'Admin'): ?>
                        <li><a class="dropdown-item" href="register.php">Register</a></li>
                    <?php endif; ?>
                    <li><a class="dropdown-item" href="login.php">Login</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
                </ul>
            </div>
            </div>
        </div>
    </nav>  

    <div class="container mt-5 pt-5">
        <h2 class="mb-4">Persetujuan Pesanan Pemasok</h2>

        <!-- Loop untuk menampilkan Card View berdasarkan hasil query -->
        <?php while ($pesan = mysqli_fetch_assoc($result_pesan)) { ?>
            <div class="card">
                <div class="card-header">
                    <strong>ID Pesan:</strong> <?= $pesan['id_pesan']; ?>
                </div>
                <div class="card-body">
                    <p><strong>Pesan:</strong> <?= $pesan['pesan']; ?></p>
                    <form action="" method="POST">
                        <input type="hidden" name="id_pesan" value="<?= $pesan['id_pesan']; ?>">
                        <div class="mb-3">
                            <label for="jumlah_kirim" class="form-label">Jumlah Kirim</label>
                            <input type="number" class="form-control" id="jumlah_kirim" name="jumlah_kirim" required>
                        </div>
                        <button type="submit" name="setujui" class="btn btn-custom">Setujui</button>
                    </form>
                </div>
            </div>
        <?php } ?>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
