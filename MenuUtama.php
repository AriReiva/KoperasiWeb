<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
echo $_SESSION['level'];
$username = $_SESSION['username'];
$level = $_SESSION['level'];

if ($level == 'Admin') {
    // Total pemasukan
    $query_total_pemasukan = "SELECT SUM(TotalHarga) AS total_pemasukan FROM transaksi";
    $result_total_pemasukan = mysqli_query($conn, $query_total_pemasukan);

    if ($result_total_pemasukan) {
        $data['pemasukan'] = mysqli_fetch_assoc($result_total_pemasukan)['total_pemasukan'];
    } else {
        die("Error pada query pemasukan: " . mysqli_error($conn));
    }

    // Total pengeluaran
    $query_total_pengeluaran = "SELECT SUM(TotalHarga) AS total_pengeluaran FROM transaksipemasok";
    $result_total_pengeluaran = mysqli_query($conn, $query_total_pengeluaran);

    if ($result_total_pengeluaran) {
        $data['pengeluaran'] = mysqli_fetch_assoc($result_total_pengeluaran)['total_pengeluaran'];
    } else {
        die("Error pada query pengeluaran: " . mysqli_error($conn));
    }

    // Jumlah pelanggan
    $query_jumlah_pelanggan = "SELECT COUNT(*) AS jumlah_pelanggan FROM pelanggan";
    $result_jumlah_pelanggan = mysqli_query($conn, $query_jumlah_pelanggan);

    if ($result_jumlah_pelanggan) {
        $data['jumlah_pelanggan'] = mysqli_fetch_assoc($result_jumlah_pelanggan)['jumlah_pelanggan'];
    } else {
        die("Error pada query pelanggan: " . mysqli_error($conn));
    }

    // Jumlah pemasok
    $query_jumlah_pemasok = "SELECT COUNT(*) AS jumlah_pemasok FROM pemasok";
    $result_jumlah_pemasok = mysqli_query($conn, $query_jumlah_pemasok);

    if ($result_jumlah_pemasok) {
        $data['jumlah_pemasok'] = mysqli_fetch_assoc($result_jumlah_pemasok)['jumlah_pemasok'];
    } else {
        die("Error pada query pemasok: " . mysqli_error($conn));
    }
}else if($level == 'Manajer'){
    // Menghitung barang yang stoknya kurang dari 5
    $query_barang_tipis = "SELECT COUNT(*) AS jumlah_barang_tipis FROM barang WHERE Jumlah < 5";
    $result_barang_tipis = mysqli_query($conn, $query_barang_tipis);

    if ($result_barang_tipis) {
        $data['stok_tipis'] = mysqli_fetch_assoc($result_barang_tipis)['jumlah_barang_tipis'];
    } else {
        die("Error pada query barang kosong: " . mysqli_error($conn));
    }

    // Menghitung barang yang stoknya kurang dari kosong
    $query_barang_kosong = "SELECT COUNT(*) AS jumlah_barang_kosong FROM barang WHERE Jumlah = 0";
    $result_barang_kosong = mysqli_query($conn, $query_barang_kosong);

    if ($result_barang_kosong) {
        $data['stok_kosong'] = mysqli_fetch_assoc($result_barang_kosong)['jumlah_barang_kosong'];
    } else {
        die("Error pada query barang kosong: " . mysqli_error($conn));
    }

    // Total pemasukan
    $query_total_pemasukan = "SELECT SUM(TotalHarga) AS total_pemasukan FROM transaksi";
    $result_total_pemasukan = mysqli_query($conn, $query_total_pemasukan);

    if ($result_total_pemasukan) {
        $data['pemasukan'] = mysqli_fetch_assoc($result_total_pemasukan)['total_pemasukan'];
    } else {
        die("Error pada query pemasukan: " . mysqli_error($conn));
    }

    // Total pengeluaran
    $query_total_pengeluaran = "SELECT SUM(TotalHarga) AS total_pengeluaran FROM transaksipemasok";
    $result_total_pengeluaran = mysqli_query($conn, $query_total_pengeluaran);

    if ($result_total_pengeluaran) {
        $data['pengeluaran'] = mysqli_fetch_assoc($result_total_pengeluaran)['total_pengeluaran'];
    } else {
        die("Error pada query pengeluaran: " . mysqli_error($conn));
    }

}

if ($_SESSION['level'] == 'Manajer') {
    $query = "SELECT * FROM barang WHERE Jumlah < 5";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        echo "<br><br>";
        echo "<div id='alertBox' style='padding: 20px; border: 1px solid red; background-color: #ffecec; margin:20px'>";
        echo "<h3 style='color: red;'>⚠ Ada stok barang yang menipis:</h3>";
        echo "<ul>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<li>" . $row['NamaBarang'] . " (Jumlah: " . $row['Jumlah'] . ")</li>";
        }
        echo "</ul>";
        echo "<button onclick='closeAlert()' style='padding: 10px 20px; background-color: red; color: white; border: none; cursor: pointer;'>OK</button>";
        echo "</div>";
    }   
}else if ($_SESSION['level'] == 'Admin') {
    // Query untuk memeriksa pesanan yang perlu dikirim ke pemasok
    $queryPesanan = "SELECT * FROM pesan WHERE status = 'pending'";
    $resultPesanan = mysqli_query($conn, $queryPesanan);

    if (mysqli_num_rows($resultPesanan) > 0) {
        echo "<br><br>";
        echo "<div id='alertBox' style='padding: 20px; border: 1px solid orange; background-color: #fff3cd; margin:20px;'>";
        echo "<h3 style='color: orange;'>⚠ Ada pesanan yang perlu dikirim ke pemasok:</h3>";
        echo "<ul>";
        while ($row = mysqli_fetch_assoc($resultPesanan)) {
            echo "<li>Pesanan ID: " . $row['id_pesan'] . " - Barang ID: " . $row['id_barang'] . " (Jumlah: " . $row['pesan'] . ")</li>";
        }
        echo "</ul>";
        echo "<button onclick='closeAlert()' style='padding: 10px 20px; background-color: orange; color: white; border: none; cursor: pointer;'>OK</button>";
        echo "</div>";
    }
}else if ($_SESSION['level'] == 'Pemasok') {

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
    // Query untuk memeriksa pesanan yang perlu dikirim ke pemasok
    $queryPesanan = "SELECT * FROM pesan WHERE status = 'approved' AND id_pemasok = '$supplierID'";
    $resultPesanan = mysqli_query($conn, $queryPesanan);

    if (mysqli_num_rows($resultPesanan) > 0) {
        echo "<br><br>";
        echo "<div id='alertBox' style='padding: 20px; border: 1px solid orange; background-color: #fff3cd; margin:20px;'>";
        echo "<h3 style='color: orange;'>⚠ Ada pesanan yang harus di kirim:</h3>";
        echo "<ul>";
        while ($row = mysqli_fetch_assoc($resultPesanan)) {
            echo "<li>Pesanan ID: " . $row['id_pesan'] . " - Barang ID: " . $row['id_barang'] . " (Jumlah: " . $row['pesan'] . ")</li>";
        }
        echo "</ul>";
        echo "<button onclick='closeAlert()' style='padding: 10px 20px; background-color: orange; color: white; border: none; cursor: pointer;'>OK</button>";
        echo "</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <title>Document</title>
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
                    <a class="nav-link active" aria-current="page" href="MenuUtama.php">Home</a>
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

    <div class="container mt-5">
        <div class="row">
            <?php if ($_SESSION['level'] == 'Admin'): ?>
                <!-- Card Pemasukan -->
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header d-flex align-items-center">
                            <i class="bi bi-cash-stack me-2" style="font-size: 1.5rem;"></i>
                            Pemasukan
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Rp. <?= number_format($data['pemasukan']); ?></h5>
                        </div>
                    </div>
                </div>
                <!-- Card Pengeluaran -->
                <div class="col-md-3">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header d-flex align-items-center">
                            <i class="bi bi-wallet2 me-2" style="font-size: 1.5rem;"></i>
                            Pengeluaran
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Rp. <?= number_format($data['pengeluaran']); ?></h5>
                        </div>
                    </div>
                </div>
                <!-- Card Jumlah Pelanggan -->
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header d-flex align-items-center">
                            <i class="bi bi-people-fill me-2" style="font-size: 1.5rem;"></i>
                            Jumlah Pelanggan
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= $data['jumlah_pelanggan']; ?> pelanggan</h5>
                        </div>
                    </div>
                </div>
                <!-- Card Jumlah Pemasok -->
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header d-flex align-items-center">
                            <i class="bi bi-people-fill me-2" style="font-size: 1.5rem;"></i>
                            Jumlah Pemasok
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= $data['jumlah_pemasok']; ?> pemasok</h5>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($_SESSION['level'] == 'Manajer'): ?>
                 <!-- Card Pemasukan -->
                 <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header d-flex align-items-center">
                            <i class="bi bi-cash-stack me-2" style="font-size: 1.5rem;"></i>
                            Pemasukan
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Rp. <?= number_format($data['pemasukan']); ?></h5>
                        </div>
                    </div>
                </div>
                <!-- Card Pengeluaran -->
                <div class="col-md-3">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header d-flex align-items-center">
                            <i class="bi bi-wallet2 me-2" style="font-size: 1.5rem;"></i>
                            Pengeluaran
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Rp. <?= number_format($data['pengeluaran']); ?></h5>
                        </div>
                    </div>
                </div>
                <!-- Card Stock Barang Kosong -->
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header d-flex align-items-center">
                            <i class="bi bi-bag-check me-2" style="font-size: 1.5rem;"></i>
                            Stock Menipis
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= $data['stok_tipis']; ?></h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header d-flex align-items-center">
                            <i class="bi bi-bag-check me-2" style="font-size: 1.5rem;"></i>
                            Stock Kosong
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= $data['stok_kosong']; ?></h5>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
    function closeAlert() {
        // Menyembunyikan elemen dengan id 'alertBox'
        document.getElementById('alertBox').style.display = 'none';
    }
    </script>
</body>
</html>
