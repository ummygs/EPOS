<?php

$hasil_barang = $lihat->barang_row();
$hasil_kategori = $lihat->kategori_row();
$stok = $lihat->barang_stok_row();
$jual = $lihat->jual_row();
$penjualan_perbulan = $lihat->penjualan_per_bulan();
$stok_perbulan = $lihat->barang_stok_per_bulan();

$sql = "SELECT * FROM barang WHERE stok <= 3";
$row = $config->prepare($sql);
$row->execute();
$r = $row->rowCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Barang</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .grafik-container {
            max-width: 1000px;
            width: 90%;
            margin: 40px auto;
        }
        canvas {
            width: 100% !important;
            height: 400px !important;
        }
        .card.shadow {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
        }
        @media (max-width: 768px) {
            canvas {
                height: 300px !important;
            }
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h3>Dashboard</h3>
    <br/>

    <?php if($r > 0): ?>
        <div class='alert alert-warning'>
            <i class='fas fa-exclamation-triangle'></i> Ada 
            <span style='color:red'><?= $r ?></span> barang yang stoknya kurang dari 3. Segera lakukan pemesanan ulang!
            <span class='float-right'><a href='index.php?page=barang&stok=yes'>Tabel Barang <i class='fa fa-angle-double-right'></i></a></span>
        </div>
    <?php endif; ?>

    <!-- RINGKASAN -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-header text-white bg-warning">
                    <h6 class="pt-2"><i class="fas fa-cubes"></i> Nama Barang</h6>
                </div>
                <div class="card-body text-center">
                    <h1><?= number_format($hasil_barang); ?></h1>
                </div>
                <div class="card-footer">
                    <a href='index.php?page=barang'>Tabel Barang <i class='fa fa-angle-double-right'></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-header text-white bg-warning">
                    <h6 class="pt-2"><i class="fas fa-chart-bar"></i> Stok Barang</h6>
                </div>
                <div class="card-body text-center">
                    <h1><?= number_format($stok['jml']); ?></h1>
                </div>
                <div class="card-footer">
                    <a href='index.php?page=barang'>Tabel Barang <i class='fa fa-angle-double-right'></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-header text-white bg-warning">
                    <h6 class="pt-2"><i class="fas fa-upload"></i> Telah Terjual</h6>
                </div>
                <div class="card-body text-center">
                    <h1><?= number_format($jual['stok']); ?></h1>
                </div>
                <div class="card-footer">
                    <a href='index.php?page=laporan'>Tabel Laporan <i class='fa fa-angle-double-right'></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-header text-white bg-warning">
                    <h6 class="pt-2"><i class="fa fa-bookmark"></i> Kategori Barang</h6>
                </div>
                <div class="card-body text-center">
                    <h1><?= number_format($hasil_kategori); ?></h1>
                </div>
                <div class="card-footer">
                    <a href='index.php?page=kategori'>Tabel Kategori <i class='fa fa-angle-double-right'></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- GRAFIK PENJUALAN -->
    <div class="grafik-container">
        <div class="card shadow">
            <div class="card-header text-white bg-warning">
                <h5><i class="fas fa-chart-bar"></i> Grafik Bar Penjualan Barang per Bulan</h5>
            </div>
            <div class="card-body">
                <canvas id="grafikPenjualan"></canvas>
            </div>
        </div>
    </div>

    <!-- GRAFIK STOK SAAT INI -->
    <div class="grafik-container">
        <div class="card shadow">
            <div class="card-header text-white bg-warning">
                <h5><i class="fas fa-chart-bar"></i> Grafik Bar Stok Total Saat Ini</h5>
            </div>
            <div class="card-body">
                <canvas id="grafikStok"></canvas>
            </div>
        </div>
    </div>

    <!-- GRAFIK STOK PER BULAN -->
    <div class="grafik-container">
        <div class="card shadow">
            <div class="card-header text-white bg-warning">
                <h5><i class="fas fa-chart-bar"></i> Grafik Bar Stok Barang per Bulan</h5>
            </div>
            <div class="card-body">
                <canvas id="grafikStokPerBulan"></canvas>
            </div>
        </div>
    </div>
</div> <!-- end container -->

<script>
    const ctxPenjualan = document.getElementById('grafikPenjualan').getContext('2d');
    new Chart(ctxPenjualan, {
        type: 'bar',
        data: {
            labels: [<?= implode(",", array_map(fn($p) => "'".$p['bulan']."'", $penjualan_perbulan)); ?>],
            datasets: [{
                label: 'Jumlah Terjual per Bulan',
                data: [<?= implode(",", array_map(fn($p) => $p['total'], $penjualan_perbulan)); ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    const ctxStok = document.getElementById('grafikStok').getContext('2d');
    new Chart(ctxStok, {
        type: 'bar',
        data: {
            labels: ['Stok Barang'],
            datasets: [{
                label: 'Jumlah Stok Saat Ini',
                data: [<?= $stok['jml']; ?>],
                backgroundColor: 'rgba(255, 206, 86, 0.6)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    const ctxStokPerBulan = document.getElementById('grafikStokPerBulan').getContext('2d');
    new Chart(ctxStokPerBulan, {
        type: 'bar',
        data: {
            labels: [<?= implode(",", array_map(fn($s) => "'".$s['bulan']."'", $stok_perbulan)); ?>],
            datasets: [{
                label: 'Total Stok per Bulan',
                data: [<?= implode(",", array_map(fn($s) => $s['total_stok'], $stok_perbulan)); ?>],
                backgroundColor: 'rgba(153, 102, 255, 0.6)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
</script>

</body>
</html>
