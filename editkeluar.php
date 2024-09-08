<?php
require 'functionkeluar.php';
require 'cek.php';

// Inisialisasi variabel
$idkeluar = isset($_GET['id']) ? $_GET['id'] : null;
$isEdit = !empty($idkeluar); // Cek apakah mode edit

// Ambil data dari database jika mode edit
if ($isEdit) {
    // Query untuk mengambil data transaksi dan detailnya
    $query = "SELECT p.idcustomer, d.idproduct, d.qty, d.satuan, d.harga, d.total, d.subtotal, d.shippingfee, d.totalamount, d.notes, d.status
              FROM productkeluar p
              LEFT JOIN detail_productkeluar d ON p.idkeluar = d.idkeluar
              WHERE p.idkeluar = $idkeluar";
    $result = mysqli_query($conn, $query);

    // Periksa jika query berhasil dieksekusi
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    // Inisialisasi variabel untuk data yang akan ditampilkan dalam form
    $idcustomer = null;
    $details = array(); // Array untuk menyimpan detail dari setiap barang

    // Periksa apakah data ditemukan
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $idcustomer = $row['idcustomer'];

        // Memproses setiap baris hasil query
        do {
            // Menyimpan detail dari setiap barang ke dalam array $details
            $details[] = array(
                'idproduct' => $row['idproduct'],
                'qty' => $row['qty'],
                'satuan' => $row['satuan'],
                'harga' => $row['harga'],
                'total' => $row['total'],
                'subtotal' => $row['subtotal'],
                'shippingfee' => $row['shippingfee'],
                'totalamount' => $row['totalamount'],
                'notes' => $row['notes'],
                'status' => $row['status']
            );
        } while ($row = mysqli_fetch_assoc($result));
    } else {
        die("Data not found for ID: " . $idkeluar);
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
    <title>Edit Data Product Keluar</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        table {
            width: 100% !important;
            border-collapse: collapse;
            border: 1px solid black;
            /* Mengatur border pada tabel */
            margin-bottom: 20px;
            /* Memberikan jarak bawah tabel jika diperlukan */
        }

        /* Mengatur border pada header dan sel tabel */
        th,
        td {
            border: 1px solid black;
            padding: 8px;
            /* Mengatur padding di dalam sel */
            text-align: left;
            /* Menyelaraskan teks ke kiri */
            vertical-align: middle;
            /* Menyelaraskan konten secara vertikal */
        }

        /* Mengatur style untuk header tabel */
        th {
            background-color: #f4f4f4;
            font-weight: bold;
            text-align: center;
            /* Menyelaraskan teks header ke tengah */
        }

        /* Mengatur lebar kolom Nama Item */
        table th:first-child,
        table td:first-child {
            width: 50px;
            /* Lebar tetap untuk kolom Nama Item */
            overflow: hidden;
            /* Menyembunyikan teks yang melebihi batas */
            text-overflow: ellipsis;
            /* Menambahkan elipsis jika teks terlalu panjang */
        }

        /* Mengatur lebar kolom Qty, Satuan, dan Harga */
        table th:nth-child(2),
        table th:nth-child(3),
        table th:nth-child(4),
        table td:nth-child(2),
        table td:nth-child(3),
        table td:nth-child(4) {
            width: 5%;
            /* Lebar untuk kolom Qty, Satuan, dan Harga */
            text-align: center;
            /* Menyelaraskan isi sel ke tengah */
            vertical-align: middle;
            /* Menyelaraskan konten secara vertikal */
        }

        /* Mengatur lebar kolom Total dan Aksi jika diperlukan */
        table th:nth-child(5),
        table th:nth-child(6),
        table td:nth-child(5),
        table td:nth-child(6) {
            width: 5%;
            /* Lebar untuk kolom Total dan Aksi */
            text-align: center;
            /* Menyelaraskan isi sel ke tengah */
            vertical-align: middle;
            /* Menyelaraskan konten secara vertikal */
        }


        /* Mengatur elemen input dan select di dalam tabel agar sesuai dengan lebar kolom */
        table td input[type="text"],
        table td select {
            width: 100%;
            /* Mengatur elemen input dan select agar sesuai dengan lebar kolom */
            padding: 5px;
            /* Memberikan padding di dalam elemen */
            box-sizing: border-box;
            /* Memastikan padding tidak menambah lebar elemen */
            font-size: 14px;
            /* Mengatur ukuran font */
            margin: 0;
            /* Menghilangkan margin default */
            display: block;
            /* Mengatur display ke block agar tidak ada jarak di samping */
        }

        .right-align {
            text-align: right;
        }

        /* Khusus untuk select2 */
        .select2-container--default .select2-selection--single {
            width: 100% !important;
            height: 30px !important;
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
            width: 5 0% !important;
            margin-bottom: 5px !important;
            /* Tambahkan lebih banyak jarak di bawah select2 */
        }

        input[type="number"].qty,
        input[type="number"].harga,
        input[type="number"].total {
            width: 80px;
            /* Mengatur lebar input */
            padding: 5px;
            /* Mengatur jarak dalam input */
            font-size: 14px;
            /* Mengatur ukuran font */
            text-align: center;
            /* Mengatur teks agar berada di tengah */
            border: 1px solid #ccc;
            /* Mengatur border */
            border-radius: 4px;
            /* Mengatur bentuk sudut */
            box-sizing: border-box;
            /* Memastikan padding tidak menambah lebar total */
        }

        input[type="number"].harga {
            width: 120px;
            /* Lebar lebih besar untuk harga */
        }

        input[type="number"].total {
            width: 120px;
            /* Lebar lebih besar untuk total */
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
                    <h1>Product keluar</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="col-xs-3">
                                <h6><strong><?php echo $isEdit ? 'Edit' : 'Tambah'; ?> Data Product Keluar</strong></h6>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="keluar.php" method="POST">
                                <div class="fields">
                                    <div class="row50">
                                        <div class="inputbox">
                                            <span><strong>Customer</strong></span>
                                            <br>
                                            <select name="customer" class="form-control select2" required>
                                                <option value="" selected disabled>--Pilih--</option>
                                                <?php
                                                $query_customer = mysqli_query($conn, "SELECT * FROM customer") or die(mysqli_error($conn));
                                                while ($data_customer = mysqli_fetch_array($query_customer)) {
                                                    $selected = ($isEdit && $data_customer['idcustomer'] == $idcustomer) ? 'selected' : '';
                                                    echo '<option value="' . $data_customer['idcustomer'] . '" ' . $selected . '>' . $data_customer['namacustomer'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <br>
                                <h5><strong>Daftar Pembelian</strong></h5>
                                <div class="field">
                                    <table id="invoiceTable">
                                        <thead>
                                            <tr>
                                                <th>Nama Item</th>
                                                <th>Qty</th>
                                                <th>Satuan</th>
                                                <th>Harga</th>
                                                <th>Total</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($details as $index => $detail) : ?>
                                                <tr>
                                                    <td>
                                                        <select name="namaproduct[]" class="form-control select2" onchange="updateHarga(this)" required>
                                                            <option value="">Select Item</option>
                                                            <?php
                                                            $query_product = mysqli_query($conn, "SELECT * FROM stock") or die(mysqli_error($conn));
                                                            while ($data_product = mysqli_fetch_array($query_product)) {
                                                                $selected = ($detail['idproduct'] == $data_product['idproduct']) ? 'selected' : '';
                                                                echo '<option value="' . $data_product['idproduct'] . '" data-harga="' . $data_product['harga'] . '" data-satuan="' . $data_product['satuan'] . '" data-stock="' . $data_product['stockproduct'] . '" ' . $selected . '>' . $data_product['namaproduct'] . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="qty[]" class="qty" value="<?php echo $detail['qty']; ?>" oninput="calculateTotal(this)" required></td>
                                                    <td><input type="text" name="satuan[]" value="<?php echo $detail['satuan']; ?>" required readonly></td>
                                                    <td><input type="number" name="harga[]" class="harga" value="<?php echo $detail['harga']; ?>" readonly></td>
                                                    <td><input type="number" name="total[]" class="total" value="<?php echo $detail['total']; ?>" readonly></td>
                                                    <td><button type="button" class="btn btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <br>
                                    <button type="button" class="btn btn-info" onclick="addRow()"> + Tambah Baris</button>
                                    <br>
                                    <br>
                                    <div style="float: right;">
                                        <p>Sub Total: <input type="number" id="subTotal" name="sub_total" value="<?php echo $detail['subtotal']; ?>" readonly></p>
                                        <p>Shipping Fee: <input type="number" id="shippingFee" name="shipping_fee" oninput="calculateGrandTotal()" value="<?php echo $detail['shippingfee']; ?>" required></p>
                                        <p>Total: <input type="number" id="grandTotal" name="grand_total" value="<?php echo $detail['totalamount']; ?>" readonly></p>
                                    </div>
                                    <div style="clear: both;">
                                        <p>Notes: <textarea name="notes" rows="4" cols="50"><?php echo $detail['notes']; ?></textarea></p>
                                        <p>Status:
                                            <select name="status">
                                                <option value="Belum Lunas" <?php echo ($detail['status'] == 'Belum Lunas') ? 'selected' : ''; ?>>Belum Lunas</option>
                                                <option value="Lunas" <?php echo ($detail['status'] == 'Lunas') ? 'selected' : ''; ?>>Lunas</option>
                                            </select>
                                        </p>
                                        <br><br>
                                        <?php if ($isEdit) : ?>
                                            <input type="hidden" name="idkeluar" value="<?php echo $idkeluar; ?>">
                                        <?php endif; ?>
                                        <button type="submit" class="btn btn-success" name="<?php echo $isEdit ? 'editnota' : 'updatenota'; ?>">
                                            <?php echo $isEdit ? 'Update' : 'Submit'; ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                // Memuat data produk
                                fetch('index.php')
                                    .then(response => response.json())
                                    .then(data => {
                                        const selects = document.querySelectorAll('select[name="namaproduct[]"]');
                                        selects.forEach(select => {
                                            data.forEach(item => {
                                                const option = document.createElement('option');
                                                option.value = item.idproduct; // Updated to match option value in PHP
                                                option.dataset.harga = item.harga;
                                                option.dataset.satuan = item.satuan;
                                                option.dataset.stockproduct = item.stockproduct; // Menambahkan stok pada option
                                                option.textContent = item.namaproduct;
                                                select.appendChild(option);
                                            });
                                        });
                                        // Inisialisasi Select2 setelah data dimuat
                                        $('.select2').select2({
                                            placeholder: "Pilih Produk",
                                            allowClear: true
                                        });
                                    });
                            });

                            function addRow() {
                                const table = document.getElementById("invoiceTable").getElementsByTagName('tbody')[0];
                                const newRow = table.insertRow();
                                newRow.innerHTML = `
            <td>
                <select name="namaproduct[]" class="form-control select2" onchange="updateHarga(this)" required>
                    <option value="">Select Item</option>
                    <?php
                    // Data produk dari database
                    $query_product = mysqli_query($conn, "SELECT * FROM stock") or die(mysqli_error($conn));
                    while ($data_product = mysqli_fetch_array($query_product)) {
                        echo '<option value="' . $data_product['idproduct'] . '" data-harga="' . $data_product['harga'] . '" data-satuan="' . $data_product['satuan'] . '" data-stock="' . $data_product['stockproduct'] . '">' . $data_product['namaproduct'] . '</option>';
                    }
                    ?>
                </select>
            </td>
            <td><input type="number" name="qty[]" class="qty" oninput="calculateTotal(this)" required></td>
            <td><input type="text" name="satuan[]" readonly required></td> <!-- Satuan otomatis -->
            <td><input type="number" name="harga[]" class="harga" readonly></td> <!-- Harga otomatis -->
            <td><input type="number" name="total[]" class="total" readonly></td>
            <td><button type="button" class="btn btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
            `;
                                // Inisialisasi Select2 hanya untuk produk baru setelah menambahkan baris
                                $('select[name="namaproduct[]"]').select2({
                                    placeholder: "Pilih Produk",
                                    allowClear: true
                                });
                            }

                            function updateHarga(select) {
                                var harga = select.options[select.selectedIndex].getAttribute('data-harga');
                                var satuan = select.options[select.selectedIndex].getAttribute('data-satuan');
                                var row = select.parentNode.parentNode;

                                // Mengisi kolom harga dan satuan otomatis
                                row.querySelector('input[name="harga[]"]').value = harga;
                                row.querySelector('input[name="satuan[]"]').value = satuan;

                                // Hitung total jika qty sudah diisi
                                calculateTotal(row.querySelector('input[name="qty[]"]'));
                            }

                            function calculateTotal(input) {
                                var row = input.parentNode.parentNode;
                                var qty = parseFloat(row.querySelector('input[name="qty[]"]').value);
                                var harga = parseFloat(row.querySelector('input[name="harga[]"]').value);
                                var stockproduct = parseFloat(row.querySelector('select[name="namaproduct[]"]').options[row.querySelector('select[name="namaproduct[]"]').selectedIndex].getAttribute('data-stock'));
                                var total = row.querySelector('input[name="total[]"]');

                                // Validasi jika qty melebihi stok
                                if (qty > stockproduct) {
                                    alert("Qty melebihi stok yang tersedia! Stock tersedia: " + stockproduct);
                                    row.querySelector('input[name="qty[]"]').value = stockproduct; // Setel qty ke stok maksimal
                                    qty = stockproduct;
                                }

                                // Menghitung total harga per produk
                                total.value = qty * harga;

                                // Hitung subtotal dan grand total
                                calculateSubTotal();
                                calculateGrandTotal();
                            }

                            function calculateSubTotal() {
                                var totals = document.querySelectorAll('input[name="total[]"]');
                                var subTotal = 0;
                                totals.forEach(function(total) {
                                    subTotal += parseFloat(total.value) || 0;
                                });
                                document.getElementById("subTotal").value = subTotal;
                            }

                            function calculateGrandTotal() {
                                var subTotal = parseFloat(document.getElementById("subTotal").value) || 0;
                                var shippingFee = parseFloat(document.getElementById("shippingFee").value) || 0;
                                document.getElementById("grandTotal").value = subTotal + shippingFee;
                            }

                            function removeRow(button) {
                                var row = button.parentNode.parentNode;
                                row.parentNode.removeChild(row);
                                calculateSubTotal();
                                calculateGrandTotal();
                            }

                            $(document).ready(function() {
                                $('.select2[name="customer"]').select2({
                                    placeholder: "Pilih Customer",
                                    allowClear: true
                                });
                                $('.select2[name="namaproduct[]"]').select2({
                                    placeholder: "Pilih Produk",
                                    allowClear: true
                                });
                            });
                        </script>
</body>

</html>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
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
        $('.select2[name="customer"]').select2({
            placeholder: "Pilih Customer",
            allowClear: true
        });
        $('.select2[name="namaproduct[]"]').select2({
            placeholder: "Pilih Produk",
            allowClear: true
        });
    });
</script>

</body>

</html>