<?php
session_start();
//membuat koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "stockproduct");

//menambah product keluar
if (isset($_POST['addnota'])) {
    // Mengambil data dari form
    $customer = $_POST['customer'];
    $namaitem = $_POST['namaproduct'];
    $qtys = $_POST['qty'];
    $satuans = $_POST['satuan'];
    $hargas = $_POST['harga'];
    $totals = $_POST['total'];
    $subtotal = $_POST['sub_total'];
    $shippingfee = $_POST['shipping_fee'];
    $totala = $_POST['grand_total'];
    $note = $_POST['notes'];
    $status = $_POST['status'];

    // Menyimpan data ke tabel_induk (productkeluar)
    $sql_keluar = "INSERT INTO productkeluar (idcustomer) VALUES ('$customer')";

    if ($conn->query($sql_keluar) === TRUE) {
        $idkeluar = $conn->insert_id; // Mendapatkan ID terakhir yang dimasukkan (idkeluar)

        // Menyiapkan query untuk menyimpan data ke tabel_anak (detail_productkeluar)
        $sql_details = "INSERT INTO detail_productkeluar (idkeluar, idproduct, qty, satuan, harga, total, subtotal, shippingfee, totalamount, notes, status) VALUES ";

        $values = array();

        // Memasukkan setiap barang sebagai nilai untuk query
        for ($i = 0; $i < count($namaitem); $i++) {
            $idproduct = $namaitem[$i];
            $qty = $qtys[$i];
            $satuan = $satuans[$i];
            $harga = $hargas[$i];
            $total = $totals[$i];

            $values[] = "('$idkeluar', '$idproduct', '$qty', '$satuan', '$harga', '$total', '$subtotal', '$shippingfee', '$totala', '$note', '$status')";
        }

        // Menggabungkan nilai-nilai ke dalam query utama
        $sql_details .= implode(", ", $values);

        // Menjalankan query untuk menyimpan data ke tabel_anak (detail_productkeluar)
        if ($conn->query($sql_details) === TRUE) {
            echo "Data berhasil disimpan.";

            // Update qty di tabel master_product
            for ($i = 0; $i < count($namaitem); $i++) {
                $idproduct = $namaitem[$i];
                $qty = $qtys[$i];

                // Query untuk mengurangi qty di tabel master_product
                $sql_update_qty = "UPDATE stock SET stockproduct = stockproduct - $qty WHERE idproduct = '$idproduct'";

                // Menjalankan query untuk mengupdate qty
                if ($conn->query($sql_update_qty) === TRUE) {
                }
            }
        }
    }
}

//mengubah data product keluar
if (isset($_POST['editnota'])) {
    $idkeluar = isset($_POST['idkeluar']) ? $_POST['idkeluar'] : null;
    $customer = $_POST['customer'];
    $namaproducts = $_POST['namaproduct'];
    $qtys = $_POST['qty'];
    $satuans = $_POST['satuan'];
    $hargas = $_POST['harga'];
    $totals = $_POST['total'];
    $subtotal = $_POST['sub_total'];
    $shippingfee = $_POST['shipping_fee'];
    $grandtotal = $_POST['grand_total'];
    $notes = $_POST['notes'];
    $status = $_POST['status'];

    // Mulai transaksi SQL
    mysqli_begin_transaction($conn);

    try {
        // Update data ke tabel productkeluar
        $update_keluar_query = "UPDATE productkeluar SET idcustomer = '$customer' WHERE idkeluar = $idkeluar";
        $result_keluar = mysqli_query($conn, $update_keluar_query);

        if (!$result_keluar) {
            throw new Exception("Update productkeluar failed: " . mysqli_error($conn));
        }

        // Loop untuk mengedit atau menambahkan detail baru ke tabel detail_productkeluar
        for ($i = 0; $i < count($namaproducts); $i++) {
            $idproduct = $namaproducts[$i];
            $qty = $qtys[$i];
            $satuan = $satuans[$i];
            $harga = $hargas[$i];
            $total = $totals[$i];

            // Cek apakah detail product sudah ada
            $check_detail_query = "SELECT qty FROM detail_productkeluar WHERE idkeluar = $idkeluar AND idproduct = $idproduct";
            $result_check = mysqli_query($conn, $check_detail_query);

            if (mysqli_num_rows($result_check) > 0) {
                // Jika ada, lakukan update
                $existing_qty = mysqli_fetch_assoc($result_check)['qty'];

                // Update detail produk
                $update_detail_query = "UPDATE detail_productkeluar 
                                        SET qty = $qty, satuan = '$satuan', harga = $harga, total = $total, 
                                            subtotal = $subtotal, shippingfee = $shippingfee, 
                                            totalamount = $grandtotal, notes = '$notes', status = '$status' 
                                        WHERE idkeluar = $idkeluar AND idproduct = $idproduct";
                $result_update_detail = mysqli_query($conn, $update_detail_query);

                if (!$result_update_detail) {
                    throw new Exception("Update detail_productkeluar failed for product ID: $idproduct");
                }

                // Kurangi stok hanya jika kuantitas diubah
                if ($qty != $existing_qty) {
                    $qty_difference = $qty - $existing_qty;
                    $update_stock_query = "UPDATE stock SET stockproduct = stockproduct - $qty_difference WHERE idproduct = $idproduct";
                    $result_stock_update = mysqli_query($conn, $update_stock_query);

                    if (!$result_stock_update) {
                        throw new Exception("Update stock failed for product ID: $idproduct");
                    }
                }
            } else {
                // Jika tidak ada, lakukan insert
                $insert_detail_query = "INSERT INTO detail_productkeluar 
                                        (idkeluar, idproduct, qty, satuan, harga, total, subtotal, shippingfee, totalamount, notes, status) 
                                        VALUES ($idkeluar, $idproduct, $qty, '$satuan', $harga, $total, $subtotal, $shippingfee, $grandtotal, '$notes', '$status')";
                $result_insert_detail = mysqli_query($conn, $insert_detail_query);

                if (!$result_insert_detail) {
                    throw new Exception("Insert detail_productkeluar failed for product ID: $idproduct");
                }

                // Kurangi stok untuk produk baru
                $update_stock_query = "UPDATE stock SET stockproduct = stockproduct - $qty WHERE idproduct = $idproduct";
                $result_stock_update = mysqli_query($conn, $update_stock_query);

                if (!$result_stock_update) {
                    throw new Exception("Update stock failed for product ID: $idproduct");
                }
            }
        }

        // Commit transaksi SQL
        mysqli_commit($conn);

        // Redirect atau tampilkan pesan sukses
        header("Location: keluar.php");
        exit();
    } catch (Exception $e) {
        // Rollback jika ada kesalahan
        mysqli_rollback($conn);
        die("Transaction failed: " . $e->getMessage());
    }
}


//menghapus barang keluar
if (isset($_POST['hapusproductkeluar'])) {
    $idk = $_POST['idk'];
    $idc = $_POST['idc'];
    $totalamount = $_POST['totalamount'];

    // Query untuk menghapus data dari tabel detail_productkeluar berdasarkan idkeluar
    $hapusdetail = mysqli_query($conn, "DELETE FROM detail_productkeluar WHERE idkeluar='$idk'");

    if ($hapusdetail) {
        // Query untuk menghapus data dari tabel productkeluar berdasarkan idkeluar
        $hapuskeluar = mysqli_query($conn, "DELETE FROM productkeluar WHERE idkeluar='$idk'");

        if ($hapuskeluar) {
            echo '<script>window.location.href="keluar.php";</script>';
        } else {
            echo 'Gagal menghapus data keluar!';
        }
    } else {
        echo 'Gagal menghapus detail product keluar!';
    }
}

//retur penjualan dari customer
if (isset($_POST['returj'])) {
    // Mengambil data dari form
    $productnya = $_POST['productnya'];
    $customer = $_POST['customer'];
    $keterangan = $_POST['keterangan'];
    $qty_rusak = $_POST['qty']; // jumlah barang rusak yang diretur

    // Mengambil stock saat ini dari tabel stock
    $query = mysqli_query($conn, "SELECT * FROM stock WHERE idproduct='$productnya'");
    if (!$query) {
        die("Query failed: " . mysqli_error($conn));
    }

    $data = mysqli_fetch_assoc($query);
    if (!$data) {
        die("No data found for product ID: " . mysqli_error($conn));
    }

    $stok_saat_ini = $data['stockproduct'];

    // Menghitung stock baru setelah retur
    $stok_baru = $stok_saat_ini + $qty_rusak;

    // Update stock di tabel stock
    $update_stock = mysqli_query($conn, "UPDATE stock SET stockproduct='$stok_baru' WHERE idproduct='$productnya'");
    if (!$update_stock) {
        die("Update stock failed: " . mysqli_error($conn));
    }

    // Menambahkan data retur ke tabel returjual
    $add_retur = mysqli_query($conn, "INSERT INTO returjual (idproduct, idcustomer, qtybefore_jual, qtyretur_jual, keterangan, qtyafter_jual) VALUES ('$productnya', '$customer', '$stok_saat_ini', '$qty_rusak', '$keterangan', '$stok_baru')");
    if (!$add_retur) {
        die("Insert retur failed: " . mysqli_error($conn));
    }

    // Redirect jika berhasil
    header('Location: returjual.php');
    exit();
}
