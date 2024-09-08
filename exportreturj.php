<?php
require 'functionkeluar.php';
require 'cek.php';
?>
<html>

<head>
    <title>Retur Penjualan</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
</head>

<body>
    <div class="container">
        <h2>Retur Penjualan</h2>
        <h4>(Inventory)</h4>
        <div class="data-tables datatable-dark">
            <table class="table table-bordered" id="mauexport" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>tanggal</th>
                        <th>Nama Customer</th>
                        <th>Nama Product</th>
                        <th>Qty Before</th>
                        <th>Qty Retur</th>
                        <th>Keterangan</th>
                        <th>Qty After</th>


                    </tr>
                </thead>
                <?php
                $ambilsemuadataretur = mysqli_query($conn, " SELECT j.idreturj, s.idproduct, c.idcustomer, j.tanggal, 
                        c.namacustomer, s.namaproduct, j.qtybefore_jual, j.qtyafter_jual, j.keterangan, j.qtyretur_jual
                    FROM 
                        returjual j
                    JOIN 
                        stock s ON s.idproduct = j.idproduct
                    JOIN 
                        customer c ON c.idcustomer = j.idcustomer");
                while ($data = mysqli_fetch_array($ambilsemuadataretur)) {
                    $idreturj = $data['idreturj'];
                    $idp = $data['idproduct'];
                    $idc = $data['idcustomer'];
                    $tanggal = $data['tanggal'];
                    $customer = $data['namacustomer'];
                    $namaproduct = $data['namaproduct'];
                    $qtyb = $data['qtybefore_jual'];
                    $qtya = $data['qtyafter_jual'];
                    $keterangan = $data['keterangan'];
                    $qtyr = $data['qtyretur_jual'];


                ?>
                    <tr>
                        <td><?php echo $tanggal; ?></td>
                        <td><?php echo $customer; ?></td>
                        <td><?php echo $namaproduct; ?></td>
                        <td><?php echo $qtyb; ?></td>
                        <td><?php echo $qtyr; ?></td>
                        <td><?php echo $keterangan; ?></td>
                        <td><?php echo $qtya; ?></td>

                    </tr>



                <?php
                };
                ?>

            </table>






        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#mauexport').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'excel', 'pdf', 'print'
                ]
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>



</body>

</html>