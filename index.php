<?php
require 'function.php';
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
    <title>Stock Product</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        .zoomable {
            width: 50px;
            height: 50px;
        }

        .zoomable:hover {
            transform: scale(4);
            transition: 0.3s ease;
        }

        a {
            text-decoration: none;
            color: black;
        }

        h4 {
            color: white;
        }

        .confirmation-text {
            color: white;
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
                    <h1 class="mt-4">Stock Product</h1>

                    <div class="card mb-4">
                        <div class="card-header">
                            <!-- Button to Open the Modal -->
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">
                                Tambah Product
                            </button>
                            <a href="export.php" class="btn btn-info">Export Data</a>
                        </div>
                        <div class="card-body">
                            <?php
                            // Menampilkan alert jika stok habis dan produk aktif
                            $ambildatastock = mysqli_query($conn, "SELECT * FROM stock WHERE stockproduct < 1");

                            while ($fetch = mysqli_fetch_array($ambildatastock)) {
                                $product = $fetch['namaproduct'];

                            ?>
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <strong>Perhatian!</strong> Stock <?= $product; ?> Telah Habis.
                                </div>
                            <?php

                            }
                            ?>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Kode Product</th>
                                            <th>Gambar</th>
                                            <th>Nama Product</th>
                                            <th>Deskripsi</th>
                                            <th>Stock</th>
                                            <th>Satuan</th>
                                            <th>Harga</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        // Menampilkan hanya produk yang berstatus 'aktif'
                                        $ambilsemuadatastock = mysqli_query($conn, "SELECT * FROM stock WHERE status='aktif'");
                                        $i = 1;
                                        while ($data = mysqli_fetch_array($ambilsemuadatastock)) {

                                            $namaproduct = $data['namaproduct'];
                                            $deskripsi = $data['deskripsi'];
                                            $stockproduct = $data['stockproduct'];
                                            $satuan = $data['satuan'];
                                            $harga = $data['harga'];
                                            $idp = $data['idproduct'];

                                            // ada gambar atau tidak
                                            $gambar = $data['image']; // ambil gambar
                                            if ($gambar == null) {
                                                // jika tidak ada gambar
                                                $img = 'No Photo';
                                            } else {
                                                // jika ada gambar
                                                $img = '<img src="images/' . $gambar . '" class="zoomable">';
                                            };
                                        ?>
                                            <tr>
                                                <td align="center"><?= $i++; ?></td>
                                                <td align="center"><?= $img; ?></td>
                                                <td><strong><a href="detail.php?id=<?= $idp; ?>"><?= $namaproduct; ?></a></strong></td>
                                                <td><?= $deskripsi; ?></td>
                                                <td align="center"><?= $stockproduct; ?></td>
                                                <td align="center"><?= $satuan; ?></td>
                                                <td>
                                                    <?= "Rp " . number_format((int)str_replace('.', '', $harga), 0, ',', '.'); ?>
                                                </td>
                                                <td class="d-flex align-items-center justify-content-center">
                                                    <button type="button" class="btn btn-warning mr-2" data-toggle="modal" data-target="#edit<?= $idp; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete<?= $idp; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="edit<?= $idp; ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">

                                                        <!-- Modal Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Edit Product</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>

                                                        <!-- Modal body -->
                                                        <form method="post" enctype="multipart/form-data">
                                                            <div class="modal-body">
                                                                <input type="text" name="namaproduct" value="<?= $namaproduct; ?>" class="form-control" required>
                                                                <br>
                                                                <input type="text" name="deskripsi" value="<?= $deskripsi; ?>" class="form-control" required>
                                                                <br>
                                                                <input type="text" name="satuan" value="<?= $satuan; ?>" class="form-control" required>
                                                                <br>
                                                                <input type="number" name="harga" value="<?= $harga; ?>" class="form-control" required>
                                                                <br>
                                                                <input type="file" name="file" class="form-control">
                                                                <br>
                                                                <input type="hidden" name="idp" value="<?= $idp ?>">
                                                                <button type="submit" class="btn btn-info" name="updateproduct">Submit</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- delete Modal -->
                                            <div class="modal fade" id="delete<?= $idp; ?>">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">

                                                        <!-- Modal Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Update Status Product</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>

                                                        <!-- Modal body -->
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                <span class="confirmation-text">Apakah Anda yakin ingin mengnonaktifkan barang ini <?= $namaproduct; ?>?</span>
                                                                <input type="hidden" name="idp" value="<?= $idp; ?>">
                                                                <br>
                                                                <br>
                                                                <button type="submit" class="btn btn-danger" name="hapusproduct">Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

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
<!-- The Modal -->
<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Tambah Product</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <form method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="text" name="namaproduct" placeholder="Nama Product" class="form-control" require>
                    <br>
                    <input type="text" name="deskripsi" placeholder="Deskripsi Product" class="form-control" require>
                    <br>
                    <input type="text" name="satuan" placeholder="Satuan" class="form-control" require>
                    <br>
                    <input type="number" name="harga" placeholder="Harga" class="form-control" step="0.001" min="0" require>
                    <br>
                    <input type="file" name="file" class="form-control">
                    <br>
                    <button type="submit" class="btn btn-info" name="addnewproduct">Submit</button>
                </div>
            </form>



        </div>
    </div>
</div>

</html>