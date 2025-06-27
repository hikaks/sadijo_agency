<?php 
	$id = $_SESSION['admin']['id_member'];
	$hasil = $lihat -> member_edit($id);
?>
<h4>Keranjang Penjualan</h4>
<br>
<?php if(isset($_GET['success'])){?>
<div class="alert alert-success"><p>Edit Data Berhasil !</p></div>
<?php }?>
<?php if(isset($_GET['remove'])){?>
<div class="alert alert-danger"><p>Hapus Data Berhasil !</p></div>
<?php }?>

<div class="row">
	<!-- Form Pencarian -->
	<div class="col-sm-4">
		<div class="card card-primary mb-3">
			<div class="card-header bg-primary text-white">
				<h5><i class="fa fa-search"></i> Cari Barang</h5>
			</div>
			<div class="card-body">
				<input type="text" id="cari" class="form-control" name="cari" placeholder="Masukan : Kode / Nama Barang  [ENTER]">
			</div>
		</div>
	</div>

	<div class="col-sm-8">
		<div class="card card-primary mb-3">
			<div class="card-header bg-primary text-white">
				<h5><i class="fa fa-list"></i> Hasil Pencarian</h5>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<div id="hasil_cari"></div>
					<div id="tunggu"></div>
				</div>
			</div>
		</div>
	</div>

	<!-- Keranjang dan Kasir -->
	<div class="col-sm-12">
		<div class="card card-primary">
			<div class="card-header bg-primary text-white">
				<h5><i class="fa fa-shopping-cart"></i> KASIR
					<a class="btn btn-danger float-right" onclick="return confirm('Apakah anda ingin reset keranjang ?');" href="fungsi/hapus/hapus.php?penjualan=jual"><b>RESET KERANJANG</b></a>
				</h5>
			</div>
			<div class="card-body">
				<div id="keranjang" class="table-responsive">
					<table class="table table-bordered">
						<tr>
							<td><b>Tanggal</b></td>
							<td><input type="text" readonly class="form-control" value="<?php echo date("Y-m-d H:i:s"); ?>" name="tgl"></td>
						</tr>
					</table>

					<table class="table table-bordered w-100" id="example1">
						<thead>
							<tr>
								<td>No</td>
								<td>Nama Barang</td>
								<td style="width:10%;">Jumlah</td>
								<td style="width:20%;">Total</td>
								<td>Kasir</td>
								<td>Aksi</td>
							</tr>
						</thead>
						<tbody>
							<?php $total_bayar=0; $no=1; $hasil_penjualan = $lihat->penjualan(); ?>
							<?php foreach($hasil_penjualan as $isi){ ?>
							<tr>
								<td><?= $no++;?></td>
								<td><?= $isi['nama_barang'];?></td>
								<td>
									<form method="POST" action="fungsi/edit/edit.php?jual=jual">
										<input type="number" name="jumlah" value="<?= $isi['jumlah'];?>" class="form-control">
										<input type="hidden" name="id" value="<?= $isi['id_penjualan'];?>"/>
										<input type="hidden" name="id_barang" value="<?= $isi['id_barang'];?>"/>
								</td>
								<td>Rp.<?= number_format($isi['total']); ?>,-</td>
								<td><?= $isi['nm_member'];?></td>
								<td>
									<button type="submit" class="btn btn-warning">Update</button>
									</form>
									<a href="fungsi/hapus/hapus.php?jual=jual&id=<?= $isi['id_penjualan'];?>&brg=<?= $isi['id_barang'];?>&jml=<?= $isi['jumlah'];?>" class="btn btn-danger"><i class="fa fa-times"></i></a>
								</td>
							</tr>
							<?php $total_bayar += $isi['total']; } ?>
						</tbody>
					</table>
					<br/>

					<div id="kasirnya">
						<form method="POST" action="index.php?page=jual&nota=yes#kasirnya">
							<?php foreach($hasil_penjualan as $isi){ ?>
								<input type="hidden" name="id_barang[]" value="<?= $isi['id_barang'];?>"/>
								<input type="hidden" name="id_member[]" value="<?= $isi['id_member'];?>"/>
								<input type="hidden" name="jumlah[]" value="<?= $isi['jumlah'];?>"/>
								<input type="hidden" name="total1[]" value="<?= $isi['total'];?>"/>
								<input type="hidden" name="tgl_input[]" value="<?= date('Y-m-d H:i:s'); ?>"/>
								<input type="hidden" name="periode[]" value="<?= date('m-Y');?>"/>
							<?php } ?>

							<table class="table table-stripped">
								<tr>
									<td>Nama Pelanggan</td>
									<td colspan="2">
										<select class="form-control" name="id_pelanggan" required>
											<option value="">-- Pilih Pelanggan --</option>
											<?php
											$pelanggan = $config->prepare("SELECT * FROM pelanggan ORDER BY nama_pelanggan ASC");
											$pelanggan->execute();
											while($p = $pelanggan->fetch()){
												echo "<option value='{$p['id_pelanggan']}'>{$p['nama_pelanggan']}</option>";
											}
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td>Total Semua</td>
									<td><input type="text" class="form-control" name="total" value="<?= $total_bayar;?>" readonly></td>
									<td>Bayar</td>
									<td><input type="number" class="form-control" name="bayar" required></td>
									<td><button class="btn btn-success"><i class="fa fa-shopping-cart"></i> Bayar</button></td>
								</tr>
							</table>
						</form>
						<?php
						if(!empty($_GET['nota'] == 'yes')) {
							$total = $_POST['total'];
							$bayar = $_POST['bayar'];
							$id_pelanggan = $_POST['id_pelanggan'];
							if(!empty($bayar)) {
								$hitung = $bayar - $total;
								if($bayar >= $total) {
									$id_barang = $_POST['id_barang'];
									$id_member = $_POST['id_member'];
									$jumlah = $_POST['jumlah'];
									$total1 = $_POST['total1'];
									$tgl_input = $_POST['tgl_input'];
									$periode = $_POST['periode'];
									$jumlah_dipilih = count($id_barang);

									for($x=0;$x<$jumlah_dipilih;$x++){
										$d = array(
											$id_barang[$x],
											$id_member[$x],
											$jumlah[$x],
											$total1[$x],
											$tgl_input[$x],
											$periode[$x],
											$id_pelanggan
										);
										$sql = "INSERT INTO nota (id_barang, id_member, jumlah, total, tanggal_input, periode, id_pelanggan) VALUES (?,?,?,?,?,?,?)";
										$row = $config->prepare($sql);
										$row->execute($d);

										// update stok
										$sql_barang = "SELECT stok FROM barang WHERE id_barang = ?";
										$row_barang = $config->prepare($sql_barang);
										$row_barang->execute([$id_barang[$x]]);
										$stok = $row_barang->fetch()['stok'];
										$total_stok = $stok - $jumlah[$x];
										$sql_stok = "UPDATE barang SET stok = ? WHERE id_barang = ?";
										$row_stok = $config->prepare($sql_stok);
										$row_stok->execute([$total_stok, $id_barang[$x]]);
									}
									echo '<script>alert("Transaksi Berhasil!");</script>';
								}else{
									echo '<script>alert("Uang Kurang! Kekurangan: Rp.'.$hitung.'");</script>';
								}
							}
						}
						?>
						<?php if(isset($hitung)): ?>
						<table class="table">
							<tr>
								<td>Kembali</td>
								<td><input type="text" class="form-control" value="<?= $hitung; ?>" readonly></td>
								<td>
									<a href="print.php?nm_member=<?= $_SESSION['admin']['nm_member'];?>&bayar=<?= $bayar;?>&kembali=<?= $hitung;?>&id_pelanggan=<?= $id_pelanggan; ?>" target="_blank">
										<button class="btn btn-secondary"><i class="fa fa-print"></i> Print</button>
									</a>
								</td>
							</tr>
						</table>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$("#cari").on('keyup', function(){
		$.ajax({
			type: "POST",
			url: "fungsi/edit/edit.php?cari_barang=yes",
			data: { keyword: $(this).val() },
			beforeSend: function(){
				$("#hasil_cari").hide();
				$("#tunggu").html('<p style="color:green">Tunggu sebentar...</p>');
			},
			success: function(html){
				$("#tunggu").html('');
				$("#hasil_cari").show().html(html);
			}
		});
	});
});
</script>
