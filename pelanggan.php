<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM pelanggan ORDER BY KodePelanggan ASC";
$result = mysqli_query($conn, $query);

$query_admin = "SELECT ID FROM Admin WHERE Level = 'Pelanggan' AND ID NOT IN (SELECT AdminID FROM pelanggan) ";
$result_admin = mysqli_query($conn, $query_admin);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Data Pelanggan</title>
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
            Add Pelanggan
        </button>
    </div>

    <div class="container mt-4">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Kode</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">No Telpon</th>
                        <th scope="col">AdminID</th>
                        <th scope="col">Edit</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['KodePelanggan'] ?></td>
                            <td><?= $row['NamaPelanggan'] ?></td>
                            <td><?= $row['AlamatPelanggan'] ?></td>
                            <td><?= $row['NoTelpPelanggan'] ?></td>
                            <td><?= $row['AdminID'] ?></td>
                            <td>
                                <!-- Add data attributes to the Edit button for JavaScript access -->
                                <button class="btn btn-sm btn-warning btn-edit"
                                        data-kode="<?= $row['KodePelanggan'] ?>" 
                                        data-nama="<?= $row['NamaPelanggan'] ?>" 
                                        data-alamat="<?= $row['AlamatPelanggan'] ?>" 
                                        data-telp="<?= $row['NoTelpPelanggan'] ?>" 
                                        data-bs-toggle="modal" data-bs-target="#editModal">
                                    Edit
                                </button>
                            </td>
                            <td><a href="delete_pelanggan.php?id=<?= $row['KodePelanggan'] ?>" class="btn btn-sm btn-danger">Delete</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No Pelanggan found.</p>
        <?php endif; ?>
    </div>

    <!-- Modal Edit Pelanggan -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="edit_pelanggan.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="editKodePelanggan" name="KodePelanggan">
                        <div class="mb-3">
                            <label for="editNamaPelanggan" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="editNamaPelanggan" name="NamaPelanggan" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAlamatPelanggan" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="editAlamatPelanggan" name="AlamatPelanggan" required>
                        </div>
                        <div class="mb-3">
                            <label for="editNoTelpPelanggan" class="form-label">No Telpon</label>
                            <input type="text" class="form-control" id="editNoTelpPelanggan" name="NoTelpPelanggan" required>
                        </div>                        
                        <div class="mb-3">
                            <label for="editAdminID" class="form-label">Admin ID</label>
                            <select class="form-control" id="editAdminID" name="AdminID">
                                <option value="" selected>Pilih Admin</option>
                                <?php 
                                $result_admin_edit = mysqli_query($conn, $query_admin);
                                while ($admin = mysqli_fetch_assoc($result_admin_edit)): 
                                ?>
                                    <option value="<?= $admin['ID'] ?>"><?= $admin['ID'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Add Pelanggan -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="add_pelanggan.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="addKodePelanggan" class="form-label">Kode Pelanggan</label>
                            <input type="text" class="form-control" id="addKodePelanggan" name="KodePelanggan" required>
                        </div>
                        <div class="mb-3">
                            <label for="addNamaPelanggan" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="addNamaPelanggan" name="NamaPelanggan" required>
                        </div>
                        <div class="mb-3">
                            <label for="addAlamatPelanggan" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="addAlamatPelanggan" name="AlamatPelanggan" required>
                        </div>
                        <div class="mb-3">
                            <label for="addNoTelpPelanggan" class="form-label">No Telpon</label>
                            <input type="text" class="form-control" id="addNoTelpPelanggan" name="NoTelpPelanggan" required>
                        </div>
                        <div class="mb-3">
                            <label for="addAdminID" class="form-label">Admin ID</label>
                            <select class="form-control" id="addAdminID" name="AdminID" required>
                                <option value="" selected>Pilih Admin</option>
                                <?php while ($admin = mysqli_fetch_assoc($result_admin)): ?>
                                    <option value="<?= $admin['ID'] ?>"><?= $admin['ID'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>   
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Pelanggan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
       document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function() {
                    const kode = this.getAttribute('data-kode');
                    const nama = this.getAttribute('data-nama');
                    const alamat = this.getAttribute('data-alamat');
                    const telp = this.getAttribute('data-telp');
                    const adminId = this.getAttribute('data-admin');

                    document.getElementById('editKodePelanggan').value = kode;
                    document.getElementById('editNamaPelanggan').value = nama;
                    document.getElementById('editAlamatPelanggan').value = alamat;
                    document.getElementById('editNoTelpPelanggan').value = telp;

                    // Preselect the Admin ID
                    const editAdminSelect = document.getElementById('editAdminID');
                    Array.from(editAdminSelect.options).forEach(option => {
                        option.selected = option.value === adminId;
                    });
                });
            });
        });
    </script>

</body>
</html>
