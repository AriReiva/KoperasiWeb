<?php
session_start();
include 'koneksi.php'; // Ganti dengan path ke koneksi database Anda

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Query untuk transaksi pemasukan (dari pelanggan)
$queryPemasukan = "
    SELECT 
        t.NomorOrder,
        d.IDDetail,
        t.TanggalOrder, 
        d.KodeBarang, 
        b.NamaBarang, 
        b.HargaJual, 
        d.Quantity, 
        d.SubTotal, 
        t.KodePelanggan 
    FROM 
        transaksi t
    JOIN 
        detail_transaksi d ON t.NomorOrder = d.NomorOrder
    JOIN 
        barang b ON d.KodeBarang = b.KodeBarang
    ORDER BY 
        t.NomorOrder DESC
";


// Query untuk transaksi pengeluaran (ke pemasok)
$queryPengeluaran = "SELECT t.NomorOrder, t.TanggalPO, b.NamaBarang, b.HargaBeli, t.Jumlah, t.TotalHarga, t.KodeSupplier 
                     FROM transaksipemasok t 
                     JOIN barang b ON t.KodeBarang = b.KodeBarang 
                     WHERE t.KodeSupplier IS NOT NULL 
                     ORDER BY t.NomorPO DESC";

// Eksekusi query
$resultPemasukan = mysqli_query($conn, $queryPemasukan);
$resultPengeluaran = mysqli_query($conn, $queryPengeluaran);

// Perhitungan total pemasukan dan pengeluaran
$totalPemasukan = 0;
$totalPengeluaran = 0;

// Hitung total pemasukan
if ($resultPemasukan) {
    while ($row = mysqli_fetch_assoc($resultPemasukan)) {
        $totalPemasukan += $row['HargaJual'] * $row['Quantity'];
    }
    mysqli_data_seek($resultPemasukan, 0); // Reset pointer
}

// Hitung total pengeluaran
if ($resultPengeluaran) {
    while ($row = mysqli_fetch_assoc($resultPengeluaran)) {
        $totalPengeluaran += $row['HargaBeli'] * $row['Jumlah'];
    }
    mysqli_data_seek($resultPengeluaran, 0); // Reset pointer
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Transaction Overview</title>
    <style>
        body{
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
        }
        /* Table Hover Effect */
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Navbar and Dropdown hover */
        .navbar-nav .nav-link:hover {
            background-color: #575757;
            color: white !important;
        }

        /* Button Hover Effect */
        .btn:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        /* Summary Box Styling */
        .summary-box {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            background-color: #f1f3f5;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: 600;
        }

        td {
            background-color: white;
        }

        .container {
            margin-top: 100px;
        }

        h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }

        .card {
            margin-bottom: 30px;
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

    <br>
    <div class="container mt-5">
    <!-- Pemasukan Table -->
    <h2>Pemasukan</h2>
    <?php if (mysqli_num_rows($resultPemasukan) > 0): ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>ID Detail</th>
                <th>Order Date</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Sub Total</th>
                <th>Kode Pelanggan</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($resultPemasukan)): ?>
                <tr>
                    <td><?= $row['NomorOrder'] ?></td>
                    <td><?= $row['IDDetail'] ?></td>
                    <td><?= $row['TanggalOrder'] ?></td>
                    <td><?= htmlspecialchars($row['NamaBarang']) ?></td>
                    <td>Rp <?= number_format($row['HargaJual'], 0, ',', '.') ?></td>
                    <td><?= $row['Quantity'] ?></td>
                    <td>Rp <?= number_format($row['SubTotal'], 0, ',', '.') ?></td>
                    <td><?= $row['KodePelanggan'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No data available.</p>
    <?php endif; ?>


    <!-- Pengeluaran Table -->
    <h2>Pengeluaran</h2>
    <?php if (mysqli_num_rows($resultPengeluaran) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order Number</th>
                    <th>Order Date</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Kode Supplier</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultPengeluaran)): ?>
                    <tr>
                        <td><?= $row['NomorOrder'] ?></td>
                        <td><?= $row['TanggalPO'] ?></td>
                        <td><?= htmlspecialchars($row['NamaBarang']) ?></td>
                        <td>Rp <?= number_format($row['HargaBeli'], 0, ',', '.') ?></td>
                        <td><?= $row['Jumlah'] ?></td>
                        <td>Rp <?= number_format($row['TotalHarga'], 0, ',', '.') ?></td>
                        <td><?= $row['KodeSupplier'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No expense transactions found.</p>
    <?php endif; ?>

    <!-- Total Summary -->
    <div class="summary-box mt-4">
        <h5>Total Pemasukan: Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></h5>
        <h5>Total Pengeluaran: Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></h5>
    </div>
    </div>
    <br>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

<?php
// Close database connection
mysqli_close($conn);
?>
