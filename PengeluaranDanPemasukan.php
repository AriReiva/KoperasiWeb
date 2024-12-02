<?php
session_start();
include 'koneksi.php'; // Include the database connection file

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Get the selected year and month from the form
$selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');
$selectedMonth = isset($_POST['month']) && $_POST['month'] !== '' ? $_POST['month'] : null;

// Construct the SQL query
$sql = "
    SELECT 'Pemasukan' AS Tipe, t.TanggalOrder AS Tanggal, t.TotalHarga AS Total
    FROM transaksipelanggan t
    WHERE YEAR(t.TanggalOrder) = ? " . ($selectedMonth ? "AND MONTH(t.TanggalOrder) = ?" : "") . "
    UNION ALL
    SELECT 'Pengeluaran' AS Tipe, t.TanggalPO AS Tanggal, t.TotalHarga AS Total
    FROM transaksipemasok t
    WHERE YEAR(t.TanggalPO) = ? " . ($selectedMonth ? "AND MONTH(t.TanggalPO) = ?" : "") . "
    ORDER BY Tanggal DESC";

// Prepare the statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

// Bind parameters dynamically based on whether the month is selected
if ($selectedMonth) {
    $stmt->bind_param("iiii", $selectedYear, $selectedMonth, $selectedYear, $selectedMonth);
} else {
    $stmt->bind_param("ii", $selectedYear, $selectedYear);
}

// Execute the statement
if (!$stmt->execute()) {
    die("Query execution failed: " . $stmt->error);
}

// Get the result set
$result = $stmt->get_result();

// Initialize arrays for daily income and expenses
$dailyIncome = [];
$dailyExpenses = [];

while ($row = $result->fetch_assoc()) {
    $date = $row['Tanggal'];
    $total = $row['Total'];

    if ($row['Tipe'] === 'Pemasukan') {
        if (!isset($dailyIncome[$date])) {
            $dailyIncome[$date] = 0;
        }
        $dailyIncome[$date] += $total;
    } elseif ($row['Tipe'] === 'Pengeluaran') {
        if (!isset($dailyExpenses[$date])) {
            $dailyExpenses[$date] = 0;
        }
        $dailyExpenses[$date] += $total;
    }
}

// Calculate totals
$totalPemasukan = array_sum($dailyIncome);
$totalPengeluaran = array_sum($dailyExpenses);

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Transaction History</title>
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


<div class="container mt-4">
    <br><br>
    <h3>Transaction History</h3>
    <form method="POST" class="mb-3">
        <div class="row">
            <label for="year" class="form-label">Filter by Year</label>
            <div class="col-md-4">
                <select name="year" id="year" class="form-select">
                    <?php
                    $currentYear = date('Y');
                    for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                        $selected = $year == $selectedYear ? 'selected' : '';
                        echo "<option value='$year' $selected>$year</option>";
                    }
                    ?>
                </select>
            </div>
            <label for="month" class="form-label">Filter by Month</label>

            <div class="col-md-4">
            <select name="month" id="month" class="form-select">
                <option value="">All</option> <!-- Opsi null -->
                <?php
                for ($month = 1; $month <= 12; $month++) {
                    $monthName = date('F', mktime(0, 0, 0, $month, 1)); // Nama bulan
                    $selected = isset($_POST['month']) && $_POST['month'] == $month ? 'selected' : '';
                    echo "<option value='$month' $selected>$monthName</option>";
                }
                ?>
            </select>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </div>
    </form>

    <h5>Total Income    : Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></h5>
    <h5>Total Expense   : Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></h5>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pemasukan</th>
                <th>Pengeluaran</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Gabungkan tanggal dari pemasukan dan pengeluaran
        $allDates = array_unique(array_merge(array_keys($dailyIncome), array_keys($dailyExpenses)));
        sort($allDates); // Urutkan tanggal

        foreach ($allDates as $date) {
            $income = isset($dailyIncome[$date]) ? $dailyIncome[$date] : 0;
            $expense = isset($dailyExpenses[$date]) ? $dailyExpenses[$date] : 0;
            echo "<tr>
                    <td>$date</td>
                    <td>Rp " . number_format($income, 0, ',', '.') . "</td>
                    <td>Rp " . number_format($expense, 0, ',', '.') . "</td>
                  </tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
