<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM barang
          ORDER BY KodeBarang ASC";

$result = mysqli_query($conn, $query);

$query_admin = "SELECT KodePemasok FROM pemasok";
$result_admin = mysqli_query($conn, $query_admin);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Document</title>
    <style>
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

        body{
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
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

    <br><br><br>
    <div class="ms-3">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
            Add Barang
        </button>
    </div>

    <div class="container mt-4">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Kode</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Jenis</th>
                        <th scope="col">Satuan</th>
                        <th scope="col">Harga Beli</th>
                        <th scope="col">Harga Jual</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">ID Pemasok</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['KodeBarang'] ?></td>
                            <td><?= $row['NamaBarang'] ?></td>
                            <td><?= $row['JenisBarang'] ?></td>
                            <td><?= $row['Satuan'] ?></td>
                            <td>Rp <?= number_format($row['HargaBeli'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($row['HargaJual'], 0, ',', '.') ?></td>
                            <td><?= $row['Jumlah'] ?></td>
                            <td><?= $row['id_pemasok'] ?></td>
                            <td><button class="btn btn-sm btn-warning btn-edit" 
                                data-kode="<?= $row['KodeBarang'] ?>" 
                                data-nama="<?= $row['NamaBarang'] ?>" 
                                data-jenis="<?= $row['JenisBarang'] ?>" 
                                data-satuan="<?= $row['Satuan'] ?>" 
                                data-harga-beli="<?= $row['HargaBeli'] ?>" 
                                data-harga-jual="<?= $row['HargaJual'] ?>" 
                                data-jumlah="<?= $row['Jumlah'] ?>">
                                Edit
                            </button></td>

                            <td><a href="delete_barang.php?id=<?= $row['KodeBarang'] ?>" class="btn btn-sm btn-danger">Delete</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No Pemasok found.</p>
        <?php endif; ?>
    </div>

    <!-- Modal Edit Barang -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="edit_barang.php" method="POST">
                <input type="hidden" id="editKodeBarang" name="KodeBarang">
                <div class="mb-3">
                    <label for="editNamaBarang" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" id="editNamaBarang" name="NamaBarang" required>
                </div>
                <div class="mb-3">
                    <label for="editJenisBarang" class="form-label">Jenis Barang</label>
                    <input type="text" class="form-control" id="editJenisBarang" name="JenisBarang">
                </div>
                <div class="mb-3">
                    <label for="editSatuan" class="form-label">Satuan</label>
                    <input type="text" class="form-control" id="editSatuan" name="Satuan">
                </div>
                <div class="mb-3">
                    <label for="editHargaBeli" class="form-label">Harga Beli</label>
                    <input type="number" class="form-control" id="editHargaBeli" name="HargaBeli">
                </div>
                <div class="mb-3">
                    <label for="editHargaJual" class="form-label">Harga Jual</label>
                    <input type="number" class="form-control" id="editHargaJual" name="HargaJual">
                </div>
                <div class="mb-3">
                    <label for="editPemasok" class="form-label">Pemasok</label>
                    <select class="form-select" id="editPemasok" name="Pemasok">
                        <option value="">Pilih ID Pemasok</option>
                        <?php 
                        $result_admin_edit = mysqli_query($conn, $query_admin);
                        while ($pemasok = mysqli_fetch_assoc($result_admin_edit)): 
                        ?>
                        <option value="<?= $pemasok['KodePemasok'] ?>"><?= $pemasok['KodePemasok'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                </form>
            </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Barang -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="add_barang.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="addKodeBarang" class="form-label">Kode Barang</label>
                            <input type="text" class="form-control" id="addKodeBarang" name="KodeBarang" required>
                        </div>
                        <div class="mb-3">
                            <label for="addNamaBarang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="addNamaBarang" name="NamaBarang" required>
                        </div>
                        <div class="mb-3">
                            <label for="addJenisBarang" class="form-label">Jenis Barang</label>
                            <input type="text" class="form-control" id="addJenisBarang" name="JenisBarang" required>
                        </div>
                        <div class="mb-3">
                            <label for="addSatuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="addSatuan" name="Satuan" required>
                        </div>
                        <div class="mb-3">
                            <label for="addHargaBeli" class="form-label">Harga Beli</label>
                            <input type="number" class="form-control" id="addHargaBeli" name="HargaBeli" required>
                        </div>
                        <div class="mb-3">
                            <label for="addHargaJual" class="form-label">Harga Jual</label>
                            <input type="number" class="form-control" id="addHargaJual" name="HargaJual" required>
                        </div>
                        <div class="mb-3">
                            <label for="addPemasok" class="form-label">Pemasok</label>
                            <select class="form-select" id="editPemasok" name="Pemasok">
                                <option value="">Pilih ID Pemasok</option>
                                <?php 
                                $result_admin_edit = mysqli_query($conn, $query_admin);
                                while ($pemasok = mysqli_fetch_assoc($result_admin_edit)): 
                                ?>
                                <option value="<?= $pemasok['KodePemasok'] ?>"><?= $pemasok['KodePemasok'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Event listener untuk tombol edit
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function() {
                    // Ambil data dari atribut data-* pada tombol
                    const kode = this.getAttribute('data-kode');
                    const nama = this.getAttribute('data-nama');
                    const jenis = this.getAttribute('data-jenis');
                    const satuan = this.getAttribute('data-satuan');
                    const hargaBeli = this.getAttribute('data-harga-beli');
                    const hargaJual = this.getAttribute('data-harga-jual');

                    // Set nilai input di modal
                    document.getElementById('editKodeBarang').value = kode;
                    document.getElementById('editNamaBarang').value = nama;
                    document.getElementById('editJenisBarang').value = jenis;
                    document.getElementById('editSatuan').value = satuan;
                    document.getElementById('editHargaBeli').value = hargaBeli;
                    document.getElementById('editHargaJual').value = hargaJual;
                    // Buka modal
                    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                    editModal.show();
                });
            });
        });
    </script>

</body>
</html>