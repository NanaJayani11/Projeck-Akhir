<?php
require_once(__DIR__ . '/tcpdf/tcpdf.php');

// Ambil idkeluar dari parameter GET
if (isset($_GET['id'])) {
    $idkeluar = $_GET['id'];
} else {
    die("ID Keluar tidak ditemukan.");
}

// Database connection parameters
$host = 'localhost';
$dbname = 'stockproduct';
$username = 'root';
$password = '';

try {
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query untuk mendapatkan data nota berdasarkan idkeluar
    $query = "
        SELECT c.namacustomer AS namacustomer, c.alamat, c.notelphon AS notelphon,
               pk.tanggal,
               dpk.idproduct, dpk.qty, dpk.satuan, dpk.harga, dpk.total, dpk.subtotal, dpk.shippingfee, dpk.totalamount, dpk.notes, dpk.status,
               s.namaproduct
        FROM customer c
        INNER JOIN productkeluar pk ON c.idcustomer = pk.idcustomer
        INNER JOIN detail_productkeluar dpk ON pk.idkeluar = dpk.idkeluar
        LEFT JOIN stock s ON dpk.idproduct = s.idproduct
        WHERE pk.idkeluar = :idkeluar
    ";

    // Prepare statement
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':idkeluar', $idkeluar, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch data
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Jika data tidak ditemukan
    if (!$result) {
        die("Data tidak ditemukan atau kosong.");
    }

    // Create TCPDF object
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('UD.Aulia');
    $pdf->SetTitle('Cetak Nota');

    // Add a page
    $pdf->AddPage();

    // Logo Perusahaan (disesuaikan dengan path dan ukuran gambar)
    $image_file = 'assets/images/logo.jpeg';
    $pdf->Image($image_file, 15, 15, 30, '', 'JPEG', '', 'T', false, 300, '', false, false, 0, false, false, false);

    // Nama Perusahaan
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'UD.AULIA', 0, 1, 'L');

    // Alamat Perusahaan
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetX(45); // Atur posisi horizontal
    $pdf->Cell(0, 10, 'JL.WARASIA RT 008/091 Batumerah Ambon 97128', 0, 1, 'L');

    // Nomor Telepon Perusahaan
    $pdf->SetX(45); // Atur posisi horizontal
    $pdf->Cell(0, 10, '082198964314', 0, 1, 'L');

    // Garis horizontal di bawah nomor telepon perusahaan
    $pdf->SetX(10); // Atur posisi horizontal
    $pdf->Cell(0, 0, '', 'T');

    // Informasi Customer dan Tanggal
    $pdf->Ln(10); // Spasi
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(35, 10, 'Nama Customer   :', 0, 0);
    $pdf->Cell(0, 10, $result[0]['namacustomer'], 0, 1);

    // Atur posisi untuk label "Tanggal Keluar:"
    $pdf->SetXY(120, $pdf->GetY() - 10);
    $pdf->Cell(35, 10, 'Tanggal :', 0, 0);

    // Atur posisi untuk nilai tanggal keluar, di sebelah kanan
    $pdf->SetXY(140, $pdf->GetY());
    $pdf->Cell(0, 10, date('d F Y', strtotime($result[0]['tanggal'])), 0, 1);

    $pdf->Cell(35, 10, 'Alamat                  :', 0, 0);
    $pdf->Cell(0, 10, $result[0]['alamat'], 0, 1);
    $pdf->Cell(35, 10, 'Telepon                :', 0, 0);
    $pdf->Cell(0, 10, $result[0]['notelphon'], 0, 1);

    // Tambahkan spasi setelah informasi alamat customer
    $pdf->Ln(20); // Atur spasi tambahan di sini

    // Tabel Item
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 10, 'Daftar Item', 1, 1, 'C', 1);
    $pdf->Cell(80, 10, 'Nama Item', 1, 0, 'C', 1); // Ubah lebar kolom jika diperlukan
    $pdf->Cell(20, 10, 'Qty', 1, 0, 'C', 1); // Ubah lebar kolom jika diperlukan
    $pdf->Cell(20, 10, 'Satuan', 1, 0, 'C', 1); // Ubah lebar kolom jika diperlukan
    $pdf->Cell(35, 10, 'Harga', 1, 0, 'C', 1); // Ubah lebar kolom jika diperlukan
    $pdf->Cell(35, 10, 'Total', 1, 1, 'C', 1); // Ubah lebar kolom jika diperlukan

    foreach ($result as $row) {
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(80, 10, $row['namaproduct'], 1, 0); // Sesuaikan lebar kolom jika diperlukan
        $pdf->Cell(20, 10, $row['qty'], 1, 0, 'C'); // Sesuaikan lebar kolom jika diperlukan
        $pdf->Cell(20, 10, $row['satuan'], 1, 0, 'C'); // Sesuaikan lebar kolom jika diperlukan
        $pdf->Cell(35, 10, 'Rp ' . number_format($row['harga'], 0, ',', '.'), 1, 0, 'R'); // Tambahkan "Rp" di depan harga
        $pdf->Cell(35, 10, 'Rp ' . number_format($row['total'], 0, ',', '.'), 1, 1, 'R'); // Tambahkan "Rp" di depan total
    }

    // Hitung subtotal, shipping fee, dan total amount
    $subtotal = array_sum(array_column($result, 'total'));
    $shippingFee = $result[0]['shippingfee']; // Sesuaikan dengan nama kolom yang ada di tabel
    $totalAmount = $subtotal + $shippingFee;

    // Subtotal, Shipping Fee, Total Amount
    $pdf->Ln(10); // Spasi
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(140, 10, 'Subtotal       :', 0, 0, 'R');
    $pdf->Cell(40, 10, 'Rp ' . number_format($subtotal, 0, ',', '.'), 0, 1, 'R'); // Tambahkan "Rp" di depan subtotal
    $pdf->Cell(140, 10, 'Shipping Fee   :', 0, 0, 'R');
    $pdf->Cell(40, 10, 'Rp ' . number_format($shippingFee, 0, ',', '.'), 0, 1, 'R'); // Tambahkan "Rp" di depan shipping fee
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(140, 10, 'Total Amount   :', 0, 0, 'R');
    $pdf->Cell(40, 10, 'Rp ' . number_format($totalAmount, 0, ',', '.'), 0, 1, 'R'); // Tambahkan "Rp" di depan total amount

    // Notes dan Status
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Ln(10); // Spasi
    $pdf->Cell(35, 10, 'Notes   :', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(0, 10, $result[0]['notes'], 0, 'L');
    $pdf->Ln(5); // Spasi

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(35, 10, 'Status  :', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 5, $result[0]['status'], 0, 1, 'L');

    // Output PDF ke browser atau simpan ke file
    $pdf->Output('cetak_nota.pdf', 'I');

    // Clean the output buffer
    ob_end_clean();

    // Close database connection
    $conn = null;
} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}
