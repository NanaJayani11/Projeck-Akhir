<?php
session_start();
//membuat koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "stockproduct");

//menambah product baru di stock
if (isset($_POST['addnewproduct'])) {
    // Mengambil data dari form
    $namaproduct = $_POST['namaproduct'];
    $deskripsi = $_POST['deskripsi'];
    $satuan = $_POST['satuan'];
    $harga = $_POST['harga'];

    // Periksa apakah ada gambar yang diunggah
    if (!empty($_FILES['file']['name'])) {
        $allowed_extension = array('png', 'jpg');
        $nama = $_FILES['file']['name']; // Ambil nama file gambar
        $dot = explode('.', $nama);
        $ekstensi = strtolower(end($dot)); // Ambil ekstensi
        $ukuran = $_FILES['file']['size']; // Ambil ukuran file
        $file_tmp = $_FILES['file']['tmp_name']; // Ambil lokasi file sementara

        // Penamaan file -> enkripsi
        $image = md5(uniqid($nama, true) . time()) . '.' . $ekstensi; // Gabungkan nama file yang dienkripsi dengan ekstensi

        // Validasi apakah produk sudah ada atau belum
        $cek = mysqli_query($conn, "SELECT * FROM stock WHERE namaproduct='$namaproduct'");
        $hitung = mysqli_num_rows($cek);

        if ($hitung < 1) {
            // Jika produk belum ada
            // Proses upload gambar
            if (in_array($ekstensi, $allowed_extension) === true) {
                // Validasi ukuran file
                if ($ukuran < 15000000) {
                    move_uploaded_file($file_tmp, 'images/' . $image);

                    $addtotable = mysqli_query($conn, "INSERT INTO stock (namaproduct, deskripsi, satuan, image, harga) VALUES ('$namaproduct', '$deskripsi', '$satuan', '$image', '$harga')");
                    if ($addtotable) {
                        header('location:index.php');
                    } else {
                        echo 'Gagal';
                        header('location:index.php');
                    }
                } else {
                    echo '
                    <script>
                        alert("Ukuran terlalu besar");
                        window.location.href="index.php";
                    </script>';
                }
            } else {
                echo '
                <script>
                    alert("File harus png/jpg");
                    window.location.href="index.php";
                </script>';
            }
        } else {
            echo '
            <script>
                alert("Nama produk sudah terdaftar");
                window.location.href="index.php";
            </script>';
        }
    } else {
        // Jika tidak ada gambar yang diunggah
        $addtotable = mysqli_query($conn, "INSERT INTO stock (namaproduct, deskripsi, satuan, harga) VALUES ('$namaproduct', '$deskripsi', '$satuan', '$harga')");
        if ($addtotable) {
            header('location:index.php');
        } else {
            echo 'Gagal';
            header('location:index.php');
        }
    }
}

//update product dari stock
if (isset($_POST['updateproduct'])) {
    $idp = $_POST['idp'];
    $namaproduct = $_POST['namaproduct'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $satuan = $_POST['satuan'];

    //soal gambar
    $allowed_extention = array('png', 'jpg');
    $nama = $_FILES['file']['name']; //ambil nama file gambar
    $dot = explode('.', $nama);
    $ekstensi = strtolower(end($dot)); // ambil extensinya
    $ukuran = $_FILES['file']['size']; //ambil size filenya
    $file_tmp = $_FILES['file']['tmp_name']; // ambil lokasi filenya

    //penamaan file -> enkripsi
    $image = md5(uniqid($nama, true) . time()) . '.' . $ekstensi; //menggabungkan name file yang dienkripsi dengan ekstensinya

    if ($ukuran == 0) {
        //jika tidak ingin upload
        $update = mysqli_query($conn, "update stock set namaproduct='$namaproduct', deskripsi='$deskripsi', satuan='$satuan', harga='$harga' where idproduct='$idp'");
        if ($update) {
            header('location:index.php');
        } else {
            echo 'Gagal';
            header('location:index.php');
        };
    } else {
        //jika ingin
        move_uploaded_file($file_tmp, 'images/' . $image);
        $update = mysqli_query($conn, "update stock set namaproduct='$namaproduct', deskripsi='$deskripsi', satuan='$satuan', harga='$harga',image='$image' where idproduct='$idp'");
        if ($update) {
            header('location:index.php');
        } else {
            echo 'Gagal';
            header('location:index.php');
        };
    }
}


//menghapus product dari stock, Proses update status menjadi nonaktif
if (isset($_POST['hapusproduct'])) {
    $idp = $_POST['idp'];

    // Ubah status produk menjadi nonaktif
    $ubahstatus = mysqli_query($conn, "UPDATE stock SET status='nonaktif' WHERE idproduct='$idp'");

    if ($ubahstatus) {
        echo "<script>alert('Product telah dinonaktifkan.');window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menonaktifkan produk.');window.location.href='index.php';</script>";
    }
}
