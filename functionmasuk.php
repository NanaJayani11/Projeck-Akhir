<?php
session_start();
//membuat koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "stockproduct");

//menambah product masuk
if (isset($_POST['productmasuk'])) {
    // Mengambil data dari form
    $productnya = $_POST['productnya'];
    $keterangan = $_POST['keterangan'];
    $qty = $_POST['qty'];


    // Cek stok saat ini
    $cekstocksekarang = mysqli_query($conn, "SELECT * FROM stock WHERE idproduct = '$productnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stockproduct'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang + $qty;

    // Masukkan ke productmasuk 
    $addtoproductmasuk = mysqli_query($conn, "INSERT INTO productmasuk (idproduct, keterangan, qty) VALUES ('$productnya', '$keterangan', '$qty')");
    $updatestockproductmasuk = mysqli_query($conn, "UPDATE stock SET stockproduct = '$tambahkanstocksekarangdenganquantity' WHERE idproduct = '$productnya'");

    if ($addtoproductmasuk && $updatestockproductmasuk) {
        header('Location: masuk.php');
    } else {
        echo 'Gagal';
        header('Location: masuk.php');
    }
}

//mengubah data product masuk
if (isset($_POST['updateproductmasuk'])) {
    $idp = $_POST['idp'];
    $idm = $_POST['idm'];
    $deskripsi = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "select * from stock where idproduct='$idp'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stockproduct'];

    $qtyskrg = mysqli_query($conn, "select * from productmasuk where idmasuk='$idm'");
    $qtynya = mysqli_fetch_array($qtyskrg);
    $qtyskrg = $qtynya['qty'];

    if ($qty > $qtyskrg) {
        $selisih = $qty - $qtyskrg;
        $kurangin = $stockskrg + $selisih;
        $kurangistocknya = mysqli_query($conn, "update stock set stockproduct='$kurangin' where idproduct='$idp'");
        $updatenya = mysqli_query($conn, "update productmasuk set qty='$qty',keterangan='$deskripsi' where idmasuk='$idm'");
        if ($kurangistocknya && $updatenya) {
            header('location:masuk.php');
        } else {
            echo 'Gagal';
            header('location:masuk.php');
        };
    } else {
        $selisih = $qtyskrg - $qty;
        $kurangin = $stockskrg - $selisih;
        $kurangistocknya = mysqli_query($conn, "update stock set stockproduct='$kurangin' where idproduct='$idp'");
        $updatenya = mysqli_query($conn, "update productmasuk set qty='$qty',keterangan='$deskripsi' where idmasuk='$idm'");
        if ($kurangistocknya && $updatenya) {
            header('location:masuk.php');
        } else {
            echo 'Gagal';
            header('location:masuk.php');
        };
    };
};

//menghapus barang masuk
if (isset($_POST['hapusproductmasuk'])) {
    $idp = $_POST['idp'];
    $qty = $_POST['kty'];
    $idm = $_POST['idm'];

    $getdatastock = mysqli_query($conn, "select * from stock where idproduct='$idp'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stockproduct'];

    $selisih = $stock - $qty;

    $update = mysqli_query($conn, "update stock set stockproduct='$selisih' where idproduct='$idp'");
    $hapusdata = mysqli_query($conn, "delete from productmasuk where idmasuk='$idm'");

    if ($update && $hapusdata) {
        header('location:masuk.php');
    } else {
        header('location:masuk.php');
    };
};

//pengembalian barang kesuplier
if (isset($_POST['returk'])) {
    // Mengambil data dari form 
    $productnya = $_POST['productnya'];
    $keterangan = $_POST['keterangan'];
    $qty_rusak = $_POST['qty']; // jumlah barang rusak yang diretur

    //mengambil stock saat ini dari tabel stock
    $query = mysqli_query($conn, "SELECT * FROM stock WHERE idproduct='$productnya'");
    $data = mysqli_fetch_assoc($query);
    $stok_saat_ini = $data['stockproduct'];

    //menghitung stock baru setelah retur
    $stok_baru = $stok_saat_ini - $qty_rusak;

    //update stock di tabel stock
    $update_stock = mysqli_query($conn, "UPDATE stock SET stockproduct='$stok_baru' WHERE idproduct='$productnya'");

    // menambahkan data retur ke tabel retur
    $add_retur = mysqli_query($conn, "INSERT INTO returkembali (idproduct, qtybefore, qtyretur, keterangan, qtyafter) VALUES ('$productnya', '$stok_saat_ini', '$qty_rusak', '$keterangan', '$stok_baru')");

    if ($update_stock && $add_retur) {
        header('location:returkembali.php');
    } else {
        echo 'Gagal melakukan retur';
        header('location:returkembali.php');
    }
}

// menambah customer
if (isset($_POST['addnewcustomer'])) {
    $namacustomer = $_POST['namacustomer'];
    $not = $_POST['not'];
    $alamat = $_POST['alamat'];

    $addtotable = mysqli_query($conn, "insert into customer ( namacustomer, notelphon, alamat) values ( '$namacustomer', '$not', '$alamat')");
    if ($addtotable) {
        header('location:customer.php');
    } else {
        echo 'Gagal';
        header('location:customer.php');
    };
};

//edit customer
if (isset($_POST['updatecustomer'])) {
    $idc = $_POST['idcustomer'];
    $namacustomer = $_POST['namacustomer'];
    $not = $_POST['notelphon'];
    $alamat = $_POST['alamat'];

    $update = mysqli_query($conn, "update customer set namacustomer='$namacustomer', notelphon='$not', alamat='$alamat' where idcustomer='$idc'");
    if ($update) {
        header('location:customer.php');
    } else {
        echo 'gagal';
        header('location:customer.php');
    }
}


// Hapus customer
if (isset($_POST['hapuscustomer'])) {
    $idc = $_POST['idc'];

    // Update status customer menjadi nonaktif
    $nonaktifkan = mysqli_query($conn, "UPDATE customer SET status_customer = 0 WHERE idcustomer='$idc'");
    if ($nonaktifkan) {
        header('location:customer.php');
    } else {
        echo 'Gagal menonaktifkan customer';
        header('location:customer.php');
    }
}
