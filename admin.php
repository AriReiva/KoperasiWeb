<?php
session_start();
include 'koneksi.php';

// Periksa sesi login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM admin ORDER BY ID ASC";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Kelola Admin</title>
    <style>
        body{
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
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

    <br><br>
    <div class="container mt-4">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Username</th>
                        <th scope="col">Password</th>
                        <th scope="col">Level</th>
                        <th scope="col">Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['ID'] ?></td>
                            <td><?= htmlspecialchars($row['Username']) ?></td>
                            <td><?= htmlspecialchars($row['Password']) ?></td>
                            <td><?= htmlspecialchars($row['Level']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit" 
                                    data-id="<?= $row['ID'] ?>" 
                                    data-username="<?= htmlspecialchars($row['Username']) ?>" 
                                    data-password="<?= htmlspecialchars($row['Password']) ?>" 
                                    data-level="<?= htmlspecialchars($row['Level']) ?>">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Tidak ada data admin.</p>
        <?php endif; ?>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="edit_admin.php" method="POST">
                        <input type="hidden" id="editID" name="ID">
                        <div class="mb-3">
                            <label for="editUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="editUsername" name="Username" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPassword" class="form-label">Password</label>
                            <input type="text" class="form-control" id="editPassword" name="Password" required>
                        </div>
                        <div class="mb-3">
                            <label for="editLevel" class="form-label">Level</label>
                            <select class="form-select" id="editLevel" name="Level" required>
                                <option value="Admin">Admin</option>
                                <option value="Pelanggan">Pelanggan</option>
                                <option value="Pemasok">Pemasok</option>
                                <option value="Manajer">Manajer</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Script Modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Event listener untuk tombol edit
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const username = this.getAttribute('data-username');
                    const password = this.getAttribute('data-password');
                    const level = this.getAttribute('data-level');

                    document.getElementById('editID').value = id;
                    document.getElementById('editUsername').value = username;
                    document.getElementById('editPassword').value = password;
                    document.getElementById('editLevel').value = level;

                    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                    editModal.show();
                });
            });
        });
    </script>
</body>
</html>
