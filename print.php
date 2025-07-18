<?php 
@ob_start();
session_start();
if (!empty($_SESSION['admin'])) {
    // ok
} else {
    echo '<script>window.location="login.php";</script>';
    exit;
}
require 'config.php';
include $view;
$lihat = new view($config);
$toko = $lihat->toko();
$hsl = $lihat->penjualan();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak Bukti Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background: #f7f7f7;
        }
        .receipt {
            background: #fff;
            padding: 20px;
            margin: 30px auto;
            width: 350px;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
            border-radius: 5px;
        }
        .receipt h2 {
            margin: 0;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .receipt p {
            margin: 2px 0;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border-bottom: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .total {
            text-align: right;
            margin-top: 15px;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-style: italic;
        }
        @media print {
            body {
                background: #fff;
            }
            .receipt {
                box-shadow: none;
                border: none;
                margin: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <h2><?php echo $toko['nama_toko']; ?></h2>
        <p><?php echo $toko['alamat_toko']; ?></p>
        <p>Tanggal: <?php echo date("j F Y, G:i"); ?></p>
        <p>Kasir: <?php echo htmlentities($_GET['nm_member']); ?></p>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($hsl as $isi): ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $isi['nama_barang']; ?></td>
                    <td><?php echo $isi['jumlah']; ?></td>
                    <td>Rp <?php echo number_format($isi['total']); ?></td>
                </tr>
                <?php $no++; endforeach; ?>
            </tbody>
        </table>
        <div class="total">
            <?php $hasil = $lihat->jumlah(); ?>
            <p><strong>Total:</strong> Rp <?php echo number_format($hasil['bayar']); ?></p>
            <p><strong>Bayar:</strong> Rp <?php echo number_format(htmlentities($_GET['bayar'])); ?></p>
            <p><strong>Kembali:</strong> Rp <?php echo number_format(htmlentities($_GET['kembali'])); ?></p>
        </div>
        <div class="footer">
            <p>Terima Kasih Telah Berbelanja di <?php echo $toko['nama_toko']; ?>!</p>
        </div>
    </div>
</body>
</html>
