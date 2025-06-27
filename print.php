<?php 
@ob_start();
session_start();
if(empty($_SESSION['admin'])){
	echo '<script>window.location="login.php";</script>';
    exit;
}
require 'config.php';
include $view;
$lihat = new view($config);
$toko = $lihat->toko();
$hsl = $lihat->penjualan();
$hasil = $lihat->jumlah();
?>
<html>
<head>
	<title>Struk Pembelian</title>
	<link rel="stylesheet" href="assets/css/bootstrap.css">
	<style>
		body {
			font-family: 'Courier New', monospace;
			background: #fff;
			font-size: 14px;
		}
		.struk-container {
			max-width: 350px;
			margin: auto;
			border: 1px dashed #000;
			padding: 15px;
		}
		hr {
			border-top: 1px dashed #000;
			margin: 10px 0;
		}
		.table th, .table td {
			padding: 4px;
			font-size: 13px;
		}
		.total-box {
			margin-top: 10px;
			font-size: 14px;
		}
		.text-right {
			text-align: right;
		}
		.text-center {
			text-align: center;
		}
	</style>
</head>
<body onload="window.print()">
	<div class="struk-container">
		<div class="text-center">
			<strong><?php echo $toko['nama_toko'];?></strong><br>
			<small><?php echo $toko['alamat_toko'];?></small><br>
			<hr>
			Tanggal: <?php echo date("j F Y, G:i");?><br>
			Kasir: <?php echo htmlentities($_GET['nm_member']);?>
		</div>
		<hr>
		<table class="table table-sm">
			<thead>
				<tr>
					<th>No</th>
					<th>Barang</th>
					<th class="text-center">Qty</th>
					<th class="text-right">Total</th>
				</tr>
			</thead>
			<tbody>
			<?php $no=1; foreach($hsl as $isi){ ?>
				<tr>
					<td><?php echo $no++; ?></td>
					<td><?php echo $isi['nama_barang']; ?></td>
					<td class="text-center"><?php echo $isi['jumlah']; ?></td>
					<td class="text-right">Rp.<?php echo number_format($isi['total']); ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<hr>
		<div class="total-box">
			<table width="100%">
				<tr>
					<td><strong>Total</strong></td>
					<td class="text-right">Rp.<?php echo number_format($hasil['bayar']); ?></td>
				</tr>
				<tr>
					<td>Bayar</td>
					<td class="text-right">Rp.<?php echo number_format($_GET['bayar']); ?></td>
				</tr>
				<tr>
					<td>Kembali</td>
					<td class="text-right">Rp.<?php echo number_format($_GET['kembali']); ?></td>
				</tr>
			</table>
		</div>
		<hr>
		<div class="text-center">
			<p>*** Terima Kasih ***<br>Telah berbelanja di toko Zili.</p>
		</div>
	</div>
</body>
</html>
