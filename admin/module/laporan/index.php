<?php 
$bulan_tes = [
    '01'=>"Januari", '02'=>"Februari", '03'=>"Maret", '04'=>"April",
    '05'=>"Mei", '06'=>"Juni", '07'=>"Juli", '08'=>"Agustus",
    '09'=>"September", '10'=>"Oktober", '11'=>"November", '12'=>"Desember"
];
?>
<div class="row">
    <div class="col-md-12">
        <h4>
            <?php if(!empty($_GET['cari'])): ?>
                Data Laporan Penjualan <?= $bulan_tes[$_POST['bln']] ?? ''; ?> <?= $_POST['thn']; ?>
            <?php elseif(!empty($_GET['hari'])): ?>
                Data Laporan Penjualan <?= $_POST['hari']; ?>
            <?php else: ?>
                Data Laporan Penjualan <?= $bulan_tes[date('m')]; ?> <?= date('Y'); ?>
            <?php endif; ?>
        </h4>
        <br/>

        <div class="card">
            <div class="card-header"><h5 class="card-title mt-2">Cari Laporan Per Bulan</h5></div>
            <div class="card-body p-0">
                <form method="post" action="index.php?page=laporan&cari=ok">
                    <table class="table table-striped">
                        <tr>
                            <th>Pilih Bulan</th><th>Pilih Tahun</th><th>Aksi</th>
                        </tr>
                        <tr>
                            <td>
                                <select name="bln" class="form-control" required>
                                    <option value="">Bulan</option>
                                    <?php
                                    $bulan = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
                                    $bln1 = ['01','02','03','04','05','06','07','08','09','10','11','12'];
                                    for($c=0; $c<12; $c++){
                                        echo "<option value='{$bln1[$c]}'>$bulan[$c]</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <?php
                                $now = date('Y');
                                echo "<select name='thn' class='form-control' required>";
                                echo '<option value="">Tahun</option>';
                                for ($a=2017; $a<=$now; $a++){
                                    echo "<option value='$a'>$a</option>";
                                }
                                echo "</select>";
                                ?>
                            </td>
                            <td>
                                <input type="hidden" name="periode" value="ya">
                                <button class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
                                <a href="index.php?page=laporan" class="btn btn-success"><i class="fa fa-refresh"></i> Refresh</a>
                                <?php if(!empty($_GET['cari'])): ?>
                                    <a href="excel.php?cari=yes&bln=<?= $_POST['bln']; ?>&thn=<?= $_POST['thn']; ?>" class="btn btn-info"><i class="fa fa-download"></i> Excel</a>
                                <?php else: ?>
                                    <a href="excel.php" class="btn btn-info"><i class="fa fa-download"></i> Excel</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </form>

                <form method="post" action="index.php?page=laporan&hari=cek">
                    <table class="table table-striped">
                        <tr><th>Pilih Hari</th><th>Aksi</th></tr>
                        <tr>
                            <td>
                                <input type="date" value="<?= date('Y-m-d'); ?>" class="form-control" name="hari" required>
                            </td>
                            <td>
                                <input type="hidden" name="periode" value="ya">
                                <button class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
                                <a href="index.php?page=laporan" class="btn btn-success"><i class="fa fa-refresh"></i> Refresh</a>
                                <?php if(!empty($_GET['hari'])): ?>
                                    <a href="excel.php?hari=cek&tgl=<?= $_POST['hari']; ?>" class="btn btn-info"><i class="fa fa-download"></i> Excel</a>
                                <?php else: ?>
                                    <a href="excel.php" class="btn btn-info"><i class="fa fa-download"></i> Excel</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>

        <br><br>

        <!-- Tabel laporan penjualan -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered w-100 table-sm" id="example1">
                        <thead>
                            <tr style="background:#DFF0D8;color:#333;">
                                <th>No</th>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th style="width:10%;">Jumlah</th>
                                <th style="width:10%;">Modal</th>
                                <th style="width:10%;">Total</th>
                                <th>Kasir</th>
                                <th>Pelanggan</th>
                                <th>Tanggal Input</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            if(!empty($_GET['cari'])){
                                $periode = $_POST['bln'].'-'.$_POST['thn'];
                                $hasil = $lihat->periode_jual($periode);
                            } elseif(!empty($_GET['hari'])){
                                $hari = $_POST['hari'];
                                $hasil = $lihat->hari_jual($hari);
                            } else {
                                $hasil = $lihat->jual();
                            }

                            $bayar = 0; $jumlah = 0; $modal = 0;
                            foreach($hasil as $isi): 
                                $subtotal_modal = $isi['harga_beli'] * $isi['jumlah'];
                                $subtotal_total = $isi['total'];
                                $bayar += $subtotal_total;
                                $modal += $subtotal_modal;
                                $jumlah += $isi['jumlah'];
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $isi['id_barang']; ?></td>
                                <td><?= $isi['nama_barang']; ?></td>
                                <td><?= $isi['jumlah']; ?></td>
                                <td>Rp.<?= number_format($subtotal_modal); ?>,-</td>
                                <td>Rp.<?= number_format($subtotal_total); ?>,-</td>
                                <td><?= $isi['nm_member']; ?></td>
                                <td><?= $isi['nama_pelanggan'] ?? '-'; ?></td>
                                <td><?= $isi['tanggal_input']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total Terjual</th>
                                <th><?= $jumlah; ?></th>
                                <th>Rp.<?= number_format($modal); ?>,-</th>
                                <th>Rp.<?= number_format($bayar); ?>,-</th>
                                <th style="background:#0bb365;color:#fff;">Keuntungan</th>
                                <th colspan="2" style="background:#0bb365;color:#fff;">
                                    Rp.<?= number_format($bayar - $modal); ?>,-
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>