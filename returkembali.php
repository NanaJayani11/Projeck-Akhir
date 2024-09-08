<?php
require 'functionmasuk.php';
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
    <title>Retur Pembelian</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        .zoomable {
            width: 50px;
        }

        .zoomable:hover {
            transform: scale(3.5);
            transition: 0.3s ease;
        }

        h4 {
            color: white;
        }

        .modal-body {
            background-color: #3c3539;
            color: #fff;
        }

        .modal-body .form-control {
            width: 100%;
            padding: 10px;
            /* Tambahkan padding untuk memberi ruang lebih di dalam input */
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fff;
            color: #000;
            margin-bottom: 5px !important;
            /* Tambahkan lebih banyak jarak antara input */
        }

        /* Khusus untuk select2 */
        .select2-container--default .select2-selection--single {
            width: 100% !important;
            height: 40px !important;
            /* Menyesuaikan tinggi dengan input lain */
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fff;
            color: #000;
            display: flex;
            align-items: center;

        }

        .select2-container {
            width: 100% !important;
            margin-bottom: 25px !important;
            /* Tambahkan lebih banyak jarak di bawah select2 */
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
                    <h1 class="mt-4">Retur Pembelian </h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <!-- Button to Open the Modal -->
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">
                                Retur
                            </button>
                            <a href="exportreturk.php" class="btn btn-info">Export Data</a>
                            <br>
                            <div class="row mt-4">
                                <div class="col">
                                    <form method="post" class="form-inline">
                                        <input type="date" name="tgl_mulai" class="form-control">
                                        <input type="date" name="tgl_selesai" class="form-control ml-3">
                                        <button type="submit" name="filter_tgl" class="btn btn-info ml-3">Filter</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Gambar</th>
                                            <th>Nama Product</th>
                                            <th>Qty before</th>
                                            <th>Qty retur</th>
                                            <th>Keterangan</th>
                                            <th>Qty After</th>


                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php

                                        if (isset($_POST['filter_tgl'])) {
                                            $mulai = $_POST['tgl_mulai'];
                                            $selesai = $_POST['tgl_selesai'];

                                            if ($mulai != null || $selesai != null) {

                                                $ambilsemuadatastock = mysqli_query($conn, "select * from returkembali r, stock s where s.idproduct = r.idproduct and tanggal BETWEEN '$mulai' and DATE_ADD('$selesai',INTERVAL 1 DAY)order by idreturk DESC");
                                            } else {
                                                $ambilsemuadatastock = mysqli_query($conn, "select * from returkembali r, stock s where s.idproduct = r.idproduct");
                                            }
                                        } else {
                                            $ambilsemuadatastock = mysqli_query($conn, "select * from  returkembali r, stock s where s.idproduct = r.idproduct");
                                        }
                                        while ($data = mysqli_fetch_array($ambilsemuadatastock)) {
                                            $idrk = $data['idreturk'];
                                            $idp = $data['idproduct'];
                                            $tanggal = $data['tanggal'];
                                            $namaproduct = $data['namaproduct'];
                                            $keterangan = $data['keterangan'];
                                            $qtyb = $data['qtybefore'];
                                            $qtya = $data['qtyafter'];
                                            $qtyr = $data['qtyretur'];




                                            // ada gambar atau tidak 
                                            $gambar = $data['image']; //ambil gambar
                                            if ($gambar == null) {
                                                //jika tidak ada gambar
                                                $img = 'No Photo';
                                            } else {
                                                //jika ada gambar
                                                $img = '<img src="images/' . $gambar . '" class="zoomable">';
                                            };


                                        ?>
                                            <tr>
                                                <td><?= $tanggal; ?></td>
                                                <td><?= $img; ?></td>
                                                <td><?= $namaproduct; ?></td>
                                                <td><?= $qtyb; ?></td>
                                                <td><?= $qtyr; ?></td>
                                                <td><?= $keterangan; ?></td>
                                                <td><?= $qtya; ?></td>




                                            </tr>

                                        <?php
                                        };
                                        ?>

                                    </tbody>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Pilih Produk",
                allowClear: true
            });
        });
    </script>
</body>
<!-- the modal -->
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Tambah Product Retur</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <form method="post">
                <div class="modal-body">

                    <select class="form-control select2" name="productnya" required>
                        <option value="" selected disabled>Pilih Produk</option>
                        <?php
                        $ambilsemuadatanya = mysqli_query($conn, "SELECT * FROM stock WHERE status='aktif'");
                        while ($fetcharray = mysqli_fetch_array($ambilsemuadatanya)) {
                            $namaproduct = $fetcharray['namaproduct'];
                            $idproduct = $fetcharray['idproduct'];
                        ?>
                            <option value="<?= $idproduct; ?>"><?= $namaproduct; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <br>
                    <input type="number" name="qty" class="form-control" placeholder="Quantity" required>
                    <br>
                    <input type="text" name="keterangan" placeholder="Keterangan" class="form-control" required>
                    <br>
                    <button type="submit" class="btn btn-primary" name="returk">Submit</button>
                </div>
            </form>

        </div>
    </div>
</div>

</html>