<?php
require 'function.php';

//dapatin ID product yang dipassing di halaman sebelumnya
$idproduct = $_GET['id']; //get id product

//get informasi product berdasarkan database
$get = mysqli_query($conn, "select * from stock where idproduct='$idproduct'");
$fetch = mysqli_fetch_assoc($get);

//set bariable
$namaproduct = $fetch['namaproduct'];
$deskripsi = $fetch['deskripsi'];
$stockproduct = $fetch['stockproduct'];
$image = $fetch['image'];
//ada gambar atau tidak
$gambar = $fetch['image']; // ambil gambar
if ($gambar == null) {
    //jika tidak ada gambar
    $img = 'No Photo';
} else {
    //jika ada gambar
    $img = '<img src="images/' . $gambar . '" class="zoomable">';
};

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Stock - Detail Product</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        .zoomable {
            width: 200px;
            height: 200px;
        }

        .zoomable:hover {
            transform: scale(1.5);
            transition: 0.3s ease;
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
                                <a class="nav-link" href="pemesanan.php"> + Add New </a>
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
                    <h1 class="mt-4">Detail Product</h1>

                    <div class="card mb-4 md-4">
                        <div class="card-header">
                            <h2><?= $namaproduct; ?></h2>
                            <?= $img; ?>
                            <div class="card-body">
                                <h5>
                                    <div class="row">
                                        <div class="col-md-3">Deskripsi</div>
                                        <div class="col-md-9">: <?= $deskripsi; ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">Stock Product</div>
                                        <div class="col-md-9">: <?= $stockproduct; ?></div>
                                    </div>
                                </h5>

                                <br>
                                <hr>

                                <h3>Product Masuk</h3>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="productmasuk" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Kode Product</th>
                                                <th>Tanggal</th>
                                                <th>keterangan</th>
                                                <th>Quantity</th>

                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $ambildatamasuk = mysqli_query($conn, "Select * from productmasuk where idproduct='$idproduct'");
                                            $i = 1;
                                            while ($fetch = mysqli_fetch_array($ambildatamasuk)) {
                                                $tanggal = $fetch['tanggal'];
                                                $keterangan = $fetch['keterangan'];
                                                $quantity = $fetch['qty'];

                                            ?>
                                                <tr>
                                                    <td><?= $i++; ?></td>
                                                    <td><?= $tanggal; ?></td>
                                                    <td><?= $keterangan; ?></td>
                                                    <td><?= $quantity; ?></td>
                                                </tr>

                                            <?php
                                            };
                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                                <br>

                                <h3>Product Keluar</h3>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="productkeluar" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Kode Product</th>
                                                <th>Tanggal</th>
                                                <th>Satuan</th>
                                                <th>Quantity</th>
                                                <th>Total</th>

                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            $ambildatakeluar = mysqli_query($conn, "SELECT productkeluar.tanggal, detail_productkeluar.satuan, detail_productkeluar.qty, detail_productkeluar.total
                                                                            FROM productkeluar
                                                                            INNER JOIN detail_productkeluar ON productkeluar.idkeluar= detail_productkeluar.idkeluar
                                                                            WHERE detail_productkeluar.idproduct = $idproduct");
                                            $i = 1;
                                            while ($fetch = mysqli_fetch_array($ambildatakeluar)) {
                                                // Menggunakan data dari hasil query
                                                $tanggal = isset($fetch['tanggal']) ? $fetch['tanggal'] : '';
                                                $satuan = isset($fetch['satuan']) ? $fetch['satuan'] : '';
                                                $qty = isset($fetch['qty']) ? $fetch['qty'] : 0;
                                                $total = isset($fetch['total']) ? $fetch['total'] : 0;

                                            ?>
                                                <tr>
                                                    <td><?= $i++; ?></td>
                                                    <td><?= $tanggal; ?></td>
                                                    <td><?= $satuan; ?></td>
                                                    <td><?= $qty; ?></td>
                                                    <td><?= $total; ?></td>
                                                </tr>

                                            <?php
                                            };
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
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