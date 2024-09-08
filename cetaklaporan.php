<?php
ob_start(); // Mulai output buffering
require_once 'tcpdf/tcpdf.php';
require 'functionkeluar.php';

// Mendapatkan parameter dari URL
$mulaiTanggal = isset($_GET['mulaiTanggal']) ? $_GET['mulaiTanggal'] : '';
$sampaiTanggal = isset($_GET['sampaiTanggal']) ? $_GET['sampaiTanggal'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Membuat query untuk mendapatkan data sesuai dengan parameter
$query = "SELECT p.idkeluar, p.tanggal, c.namacustomer, SUM(d.totalamount) AS totalamount, d.status 
          FROM productkeluar p
          JOIN customer c ON c.idcustomer = p.idcustomer 
          JOIN detail_productkeluar d ON d.idkeluar = p.idkeluar
          WHERE p.tanggal BETWEEN '$mulaiTanggal' AND '$sampaiTanggal'";

if (!empty($status)) {
    $query .= " AND d.status = '$status'";
}

$query .= " GROUP BY p.idkeluar";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error: " . mysqli_error($conn));
}

// Membuat PDF
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('UD.AULIA');
$pdf->SetTitle('Laporan Penjualan');
$pdf->SetHeaderData('', '', 'Laporan Penjualan', 'UD.AULIA');
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFont('dejavusans', '', 10);
$pdf->AddPage();

// Judul dan periode laporan
$pdf->SetFont('dejavusans', 'B', 14);
$pdf->Cell(0, 10, 'Laporan Tagihan Penjualan', 0, 1, 'C');
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(0, 10, 'Periode: ' . $mulaiTanggal . ' s/d ' . $sampaiTanggal, 0, 1, 'C');

// Tabel data
$pdf->SetFont('dejavusans', '', 10);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(128, 128, 128);
$pdf->SetLineWidth(0.3);

// Header tabel
$pdf->SetFont('', 'B');
$pdf->SetFillColor(220, 220, 220); // Warna latar belakang untuk header
$pdf->Cell(10, 10, 'No', 1, 0, 'C', 1);
$pdf->Cell(40, 10, 'Tanggal', 1, 0, 'C', 1);
$pdf->Cell(60, 10, 'Customer', 1, 0, 'C', 1);
$pdf->Cell(40, 10, 'Total Amount', 1, 0, 'C', 1);
$pdf->Cell(40, 10, 'Status', 1, 1, 'C', 1);

$pdf->SetFont('dejavusans', '');

$no = 1;
$grandtotal = 0;
$totalRows = mysqli_num_rows($result);
$currentRow = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $statusClass = $row['status'] == 'Lunas' ? 'badge-success' : 'badge-danger';

    $pdf->Cell(10, 10, $no, 1, 0, 'C');
    $pdf->Cell(40, 10, $row['tanggal'], 1, 0, 'C');
    $pdf->Cell(60, 10, $row['namacustomer'], 1, 0, 'L');
    $pdf->Cell(40, 10, number_format($row['totalamount'], 2), 1, 0, 'R');
    $pdf->Cell(40, 10, $row['status'], 1, 1, 'C');

    $grandtotal += $row['totalamount'];
    $no++;
    $currentRow++;

    // Jika ini adalah baris terakhir, tambahkan total keseluruhan dengan colspan
    if ($currentRow == $totalRows) {
        // Gabungkan baris Total Keseluruhan dengan menggunakan colspan
        $pdf->SetFont('dejavusans', 'B');
        $pdf->Cell(110, 10, 'Total Keseluruhan', 1, 0, 'R');
        $pdf->Cell(40, 10, number_format($grandtotal, 2), 1, 0, 'R');
        $pdf->Cell(40, 10, '', 1, 1, 'R');
        $pdf->SetFont('dejavusans', '', 10);
    }
}

ob_end_clean(); // Menghilangkan output buffering
$pdf->Output('Laporan_Penjualan.pdf', 'I');
