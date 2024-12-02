<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ambil KodePelanggan (dapat disesuaikan dengan ID pelanggan yang sesuai)
$kodePelanggan = $_SESSION['ID']; // Misalnya kita simpan user_id di session saat login

if (isset($_POST['quantity'])) {
    foreach ($_POST['quantity'] as $kodeBarang => $quantity) {
        $queryBarang = "SELECT Jumlah FROM barang WHERE KodeBarang = '$kodeBarang'";
        $result = mysqli_query($conn, $queryBarang);
        $barang = mysqli_fetch_assoc($result);
        $maxQuantity = $barang['Jumlah'];

        // Validasi agar kuantitas tidak melebihi stok
        if ($quantity > $maxQuantity) {
            $quantity = $maxQuantity;
        }

        $_SESSION['cart'][$kodeBarang] = $quantity;
    }
}

// Proses checkout
if (isset($_POST['checkout'])) {
    $tanggal = date('Y-m-d');
    $nomorOrder = uniqid(''); // Nomor unik untuk setiap transaksi

    // Insert ke tabel transaksi
    $queryTransaksi = "INSERT INTO transaksi (TanggalOrder, KodePelanggan, TotalHarga) 
                       VALUES ('$tanggal', '$kodePelanggan', 0)";
    mysqli_query($conn, $queryTransaksi);

    // Ambil NomorOrder yang baru saja dimasukkan
    $nomorOrderQuery = "SELECT MAX(NomorOrder) AS LastOrder FROM transaksi";
    $result = mysqli_query($conn, $nomorOrderQuery);
    $row = mysqli_fetch_assoc($result);
    $nomorOrder = $row['LastOrder'];

    $totalTransaksi = 0;

    // Loop item di keranjang untuk insert ke transaksi_detail
    foreach ($_SESSION['cart'] as $kodeBarang => $quantity) {
        // Ambil detail barang dari database
        $queryBarang = "SELECT * FROM barang WHERE KodeBarang = '$kodeBarang'";
        $barang = mysqli_fetch_assoc(mysqli_query($conn, $queryBarang));
    
        $harga = $barang['HargaJual'];
        $subtotal = $harga * $quantity;
        $totalTransaksi += $subtotal;
    
        // Insert ke tabel detail_transaksi
        $queryDetail = "INSERT INTO detail_transaksi (NomorOrder, KodeBarang, Quantity, SubTotal) 
                        VALUES ('$nomorOrder', '$kodeBarang', $quantity, $subtotal)";
        mysqli_query($conn, $queryDetail);
    
        // Kurangi stok barang
        $newStock = $barang['Jumlah'] - $quantity;
        mysqli_query($conn, "UPDATE barang SET Jumlah = $newStock WHERE KodeBarang = '$kodeBarang'");
    }    

    // Update total harga transaksi di tabel transaksi
    $updateTotalQuery = "UPDATE transaksi SET TotalHarga = $totalTransaksi WHERE NomorOrder = '$nomorOrder'";
    mysqli_query($conn, $updateTotalQuery);

    // Kosongkan keranjang
    $_SESSION['cart'] = [];

    header("Location: cart.php?order=$nomorOrder");
    exit();
}else if (isset($_POST['back'])) {
    header("Location: productlist.php");
}else if (isset($_POST['remove'])) {
        $kodeBarang = $_POST['remove'];
        unset($_SESSION['cart'][$kodeBarang]);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-buy { width: 100%; }
    </style>
    <title>Shopping Cart</title>
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
    <br><br>
    <div class="container mt-4">
        <h1 class="text-center my-4">Shopping Cart</h1>
        <form action="cart.php" method="POST">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandTotal = 0;
                    foreach ($_SESSION['cart'] as $kodeBarang => $quantity): 
                        $queryBarang = "SELECT * FROM barang WHERE KodeBarang = '$kodeBarang'";
                        $barang = mysqli_fetch_assoc(mysqli_query($conn, $queryBarang));
                        $subtotal = $barang['HargaJual'] * $quantity;
                        $grandTotal += $subtotal;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($barang['NamaBarang']) ?></td>
                            <td>
                                <input type="number" name="quantity[<?= $kodeBarang ?>]" value="<?= $quantity ?>" min="1"  max="<?= $barang['Jumlah'] ?>" class="form-control" style="width: 70px;">
                            </td>
                            <td>Rp <?= number_format($barang['HargaJual'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                            <td>
                                <button type="submit" name="remove" value="<?= $kodeBarang ?>" class="btn btn-danger btn-sm">Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                        <td><strong>Rp <?= number_format($grandTotal, 0, ',', '.') ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <button type="submit" name="checkout" class="btn btn-primary">Checkout</button>
            <button type="submit" name="back" class="btn btn-primary">Kembali</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
