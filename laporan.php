<?php
require 'functionkeluar.php';
require 'cek.php';

$mulaiTanggal = '';
$sampaiTanggal = '';
$status = '';

// Proses form ketika tombol Tampilkan diklik
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['mulaiTanggal']) && isset($_GET['sampaiTanggal'])) {
    $mulaiTanggal = mysqli_real_escape_string($conn, $_GET['mulaiTanggal']);
    $sampaiTanggal = mysqli_real_escape_string($conn, $_GET['sampaiTanggal']);
    $status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

    // Query untuk mengambil data laporan
    $query = "SELECT p.idkeluar, p.idcustomer, p.tanggal, c.namacustomer, SUM(d.totalamount) AS totalamount, d.status 
              FROM productkeluar p
              JOIN customer c ON c.idcustomer = p.idcustomer 
              JOIN detail_productkeluar d ON d.idkeluar = p.idkeluar
              WHERE p.tanggal BETWEEN '$mulaiTanggal' AND '$sampaiTanggal'";

    if (!empty($status)) {
        $query .= " AND d.status = '$status'";
    }

    $query .= " GROUP BY p.idkeluar";

    $ambilsemuadatacustomer = mysqli_query($conn, $query);

    if (!$ambilsemuadatacustomer) {
        die("Error: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Laporan</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <style>
        .text-right {
            text-align: right;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">UD.AULIA</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
        <!-- Navbar-->
        <ul class="navbar-nav ml-auto ml-md-6">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Notes</div>
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Stock Product
                        </a>
                        <a class="nav-link" href="customer.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                            Customer
                        </a>
                        <div class="sb-sidenav-menu-heading">Transaksi Masuk</div>
                        <a class="nav-link" href="masuk.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                            Product Masuk
                        </a>
                        <a class="nav-link" href="returkembali.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-arrow-right"></i></div>
                            Retur Pembelian
                        </a>
                        <div class="sb-sidenav-menu-heading">Transaksi Keluar</div>
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                            Penjualan
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="pemesanan.php"> + Add New</a>
                                <a class="nav-link" href="keluar.php">Data Keluar</a>
                            </nav>
                        </div>
                        <a class="nav-link" href="returjual.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-arrow-left"></i></div>
                            Retur Penjualan
                        </a>
                        <a class="nav-link" href="laporan.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                            Laporan
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h1 class="mt-4">Laporan</h1>
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title">Data Laporan</h5>
                            <a href="cetaklaporan.php?mulaiTanggal=<?php echo $mulaiTanggal; ?>&sampaiTanggal=<?php echo $sampaiTanggal; ?>&status=<?php echo $status; ?>" target="_blank" class="btn btn-success"><i class="fas fa-print"></i> cetak</a>

                        </div>
                        <div class="card-body">
                            <form method="GET" action="">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="mulaiTanggal">Mulai Tanggal</label>
                                        <input type="date" class="form-control" id="mulaiTanggal" name="mulaiTanggal" value="<?php echo $mulaiTanggal; ?>" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="sampaiTanggal">Sampai Tanggal</label>
                                        <input type="date" class="form-control" id="sampaiTanggal" name="sampaiTanggal" value="<?php echo $sampaiTanggal; ?>" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="status">Status</label>
                                        <select id="status" name="status" class="form-control">
                                            <option value="">Semua</option>
                                            <option value="Belum Lunas" <?php echo ($status === 'Belum Lunas') ? 'selected' : ''; ?>>Belum Lunas</option>
                                            <option value="Lunas" <?php echo ($status === 'Lunas') ? 'selected' : ''; ?>>Lunas</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary mr-2">Tampilkan</button>
                                        <button type="button" class="btn btn-danger" id="resetBtn">Reset</button>
                                    </div>
                                </div>
                            </form>

                            <?php if (isset($ambilsemuadatacustomer) && mysqli_num_rows($ambilsemuadatacustomer) > 0) : ?>
                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Nama Customer</th>
                                                <th>Total Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $grandtotal = 0;
                                            while ($data = mysqli_fetch_assoc($ambilsemuadatacustomer)) {
                                                $tanggal = $data['tanggal'];
                                                $namacustomer = $data['namacustomer'];
                                                $totala = $data['totalamount'];
                                                $status = $data['status'];
                                                $grandtotal += $totala;

                                                // Menyediakan badge berdasarkan status
                                                $badgeClass = $status === 'Lunas' ? 'badge-success' : 'badge-danger';
                                                echo "
                                                    <tr>
                                                        <td>{$tanggal}</td>
                                                        <td>{$namacustomer}</td>
                                                        <td>{$totala}</td>
                                                        <td><span class='badge {$badgeClass}'>{$status}</span></td>
                                                    </tr>";
                                            }
                                            ?>
                                            <tr>
                                                <td colspan="2" class="text-right"><strong>Total</strong></td>
                                                <td class="text-center"><?php echo $grandtotal; ?></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            <?php elseif (isset($_GET['mulaiTanggal']) && isset($_GET['sampaiTanggal'])) : ?>
                                <div class="alert alert-warning mt-4">Data tidak ditemukan.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; UD.AULIA</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script>
        document.getElementById('resetBtn').addEventListener('click', function() {
            // Reset ulang masukan 
            document.getElementById('mulaiTanggal').value = '';
            document.getElementById('sampaiTanggal').value = '';
            document.getElementById('status').selectedIndex = 0;

            // hapus data di tabel
            var tableBody = document.querySelector('tbody');
            tableBody.innerHTML = '';
        });
    </script>
</body>

</html>