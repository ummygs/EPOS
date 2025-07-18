<?php 
	$sql=" select * from barang where stok <= 3";
	$row = $config -> prepare($sql);
	$row -> execute();
	$r = $row -> rowCount();
	if($r > 0){
?>
<?php
		echo "
		<div class='alert alert-warning'>
			<span class='glyphicon glyphicon-info-sign'></span> Ada <span style='color:red'>$r</span> barang yang Stok tersisa sudah kurang dari 3 items. silahkan pesan lagi !!
			<span class='pull-right'><a href='index.php?page=barang&stok=yes'>Tabel Barang <i class='fa fa-angle-double-right'></i></a></span>
		</div>
		";	
	}
?>
<?php $hasil_barang = $lihat -> barang_row();?>
<?php $hasil_kategori = $lihat -> kategori_row();?>
<?php $stok = $lihat -> barang_stok_row();?>
<?php $jual = $lihat -> jual_row();?>
<div class="row">
    
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
    <?php 
        // Koneksi dan query stok <= 3
        $sql = "SELECT * FROM barang WHERE stok <= 3";
        $row = $config->prepare($sql);
        $row->execute();
        $r = $row->rowCount();
        if($r > 0){
            echo "
            <div class='alert alert-warning'>
                <i class='fas fa-exclamation-triangle'></i> Ada <span style='color:red'>$r</span> barang yang stoknya kurang dari 3. Segera lakukan pemesanan ulang!
                <span class='float-right'><a href='index.php?page=barang&stok=yes'>Tabel Barang <i class='fa fa-angle-double-right'></i></a></span>
            </div>
            ";	
        }

        // Data ringkasan
        $hasil_barang = $lihat->barang_row();
        $hasil_kategori = $lihat->kategori_row();
        $stok = $lihat->barang_stok_row();
        $jual = $lihat->jual_row();
    ?>

    <!-- Ringkasan Card -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-header text-white" style="background-color:rgb(255, 193, 7);">
                    <h6 class="pt-2"><i class="fas fa-cubes"></i> Nama Barang</h6>
                </div>
                <div class="card-body text-center">
                    <h1><?php echo number_format($hasil_barang);?></h1>
                </div>
                <div class="card-footer">
                    <a href='index.php?page=barang'>Tabel Barang <i class='fa fa-angle-double-right'></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-header text-white" style="background-color:rgb(255, 193, 7);">
                    <h6 class="pt-2"><i class="fas fa-chart-bar"></i> Stok Barang</h6>
                </div>
                <div class="card-body text-center">
                    <h1><?php echo number_format($stok['jml']);?></h1>
                </div>
                <div class="card-footer">
                    <a href='index.php?page=barang'>Tabel Barang <i class='fa fa-angle-double-right'></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-header text-white" style="background-color:rgb(255, 193, 7);">
                    <h6 class="pt-2"><i class="fas fa-upload"></i> Telah Terjual</h6>
                </div>
                <div class="card-body text-center">
                    <h1><?php echo number_format($jual['stok']);?></h1>
                </div>
                <div class="card-footer">
                    <a href='index.php?page=laporan'>Tabel Laporan <i class='fa fa-angle-double-right'></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-header text-white" style="background-color:rgb(255, 193, 7);">
                    <h6 class="pt-2"><i class="fa fa-bookmark"></i> Kategori Barang</h6>
                </div>
                <div class="card-body text-center">
                    <h1><?php echo number_format($hasil_kategori);?></h1>
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
         <div class="card-header text-white" style="background-color:rgb(255, 193, 7);">
                <h5><i class="fas fa-chart-line"></i> Grafik Garis Penjualan Barang</h5>
            </div>
            <div class="card-body">
                <canvas id="grafikPenjualan"></canvas>
            </div>
        </div>
    </div>

    <!-- GRAFIK STOK -->
    <div class="grafik-container">
        <div class="card shadow">
             <div class="card-header text-white" style="background-color:rgb(255, 193, 7);">
                <h5><i class="fas fa-chart-line"></i> Grafik Garis Stok Barang</h5>
            </div>
            <div class="card-body">
                <canvas id="grafikStok"></canvas>
            </div>
        </div>
    </div>

</div> <!-- end container -->

<script>
    // Grafik Garis Penjualan
    const ctxPenjualan = document.getElementById('grafikPenjualan').getContext('2d');
    new Chart(ctxPenjualan, {
        type: 'line',
        data: {
            labels: ['Barang Terjual'],
            datasets: [{
                label: 'Jumlah Terjual',
                data: [<?php echo $jual['stok']; ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Grafik Garis Stok
    const ctxStok = document.getElementById('grafikStok').getContext('2d');
    new Chart(ctxStok, {
        type: 'line',
        data: {
            labels: ['Stok Barang'],
            datasets: [{
                label: 'Jumlah Stok',
                data: [<?php echo $stok['jml']; ?>],
               backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

</body>
</html>
