<?php
require 'functionkeluar.php';
require 'cek.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Data Product Keluar</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        .text-lunas {
            color: white;
        }

        .btn-status {
            width: 50px;
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
                    <h1 class="mt-4">Product Keluar</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <!-- Button to Open the Modal -->
                            <button type="button" class="btn btn-info" onclick="location.href='pemesanan.php'">
                                Tambah Stock
                            </button>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Nama Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Aksi</th>

                                        </tr>
                                    </thead>
                                    <?php {
                                        $ambilsemuadatacustomer = mysqli_query($conn, "SELECT p.idkeluar, p.idcustomer, p.tanggal, MAX(c.namacustomer) as namacustomer, d.totalamount, d.status 
                                                FROM productkeluar p
                                                JOIN customer c ON c.idcustomer = p.idcustomer 
                                                JOIN detail_productkeluar d ON d.idkeluar = p.idkeluar
                                                GROUP BY p.idkeluar");

                                        if (!$ambilsemuadatacustomer) {
                                            die("Error: " . mysqli_error($conn));
                                        }
                                    }
                                    while ($data = mysqli_fetch_assoc($ambilsemuadatacustomer)) {
                                        $idk = $data['idkeluar'];
                                        $idc = $data['idcustomer'];
                                        $tanggal = $data['tanggal'];
                                        $namacustomer = $data['namacustomer'];
                                        $totala = $data['totalamount'];
                                        $status = $data['status'];
                                    ?>
                                        <tr>
                                            <td><?= $tanggal; ?></td>
                                            <td><?= $namacustomer; ?></td>
                                            <td><?= "Rp " . number_format((int)str_replace('.', '', $totala), 0, ',', '.'); ?></td>
                                            <td>
                                                <?php if ($status == 'Lunas'): ?>
                                                    <span class="badge badge-success">Lunas</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Belum Lunas</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="cetaknota.php?id=<?= $idk; ?>" class="btn btn-success">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <button type="button" class="btn btn-warning" onclick="location.href='editkeluar.php?id=<?php echo $idk; ?>'">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete<?= $idk; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- delete Modal -->
                                        <div class="modal fade" id="delete<?= $idk; ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <!-- Modal Header -->
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Hapus Barang</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>

                                                    <!-- Modal body -->
                                                    <form method="post">
                                                        <div class="modal-body">
                                                            Apakah anda yakin ingin menghapus data ini: <?= $namacustomer; ?>?
                                                            <input type="hidden" name="idc" value="<?= $idc; ?>">
                                                            <input type="hidden" name="totalamount" value="<?= $totala; ?>">
                                                            <input type="hidden" name="idk" value="<?= $idk; ?>">
                                                            <br>
                                                            <br>
                                                            <button type="submit" class="btn btn-danger" name="hapusproductkeluar">Hapus</button>
                                                        </div>
                                                    </form>




                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    };
                                    ?>

                                </table>
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/datatables-demo.js"></script>
</body>

</html>