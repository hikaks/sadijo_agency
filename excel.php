<?php 
    @ob_start();
    session_start();
    if (empty($_SESSION['admin'])) {
        echo '<script>window.location="login.php";</script>';
        exit;
    }

    header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=data-laporan-" . date('Y-m-d') . ".xls");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);

    require 'config.php';
    include $view;
    $lihat = new view($config);

    $bulan_tes = array(
        '01' => "Januari",
        '02' => "Februari",
        '03' => "Maret",
        '04' => "April",
        '05' => "Mei",
        '06' => "Juni",
        '07' => "Juli",
        '08' => "Agustus",
        '09' => "September",
        '10' => "Oktober",
        '11' => "November",
        '12' => "Desember"
    );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Laporan Penjualan</title>
</head>
<body>
    <div class="modal-view">
        <h3 style="text-align:center;">
            <?php if (!empty(htmlentities($_GET['cari']))) { ?>
                Data Laporan Penjualan <?= $bulan_tes[htmlentities($_GET['bln'])]; ?> <?= htmlentities($_GET['thn']); ?>
            <?php } elseif (!empty(htmlentities($_GET['hari']))) { ?>
                Data Laporan Penjualan <?= htmlentities($_GET['tgl']); ?>
            <?php } else { ?>
                Data Laporan Penjualan <?= $bulan_tes[date('m')]; ?> <?= date('Y'); ?>
            <?php } ?>
        </h3>
        <table border="1" width="100%" cellpadding="3" cellspacing="4">
            <thead>
                <tr bgcolor="yellow">
                    <th>No</th>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Modal</th>
                    <th>Total</th>
                    <th>Kasir</th>
                    <th>Pelanggan</th>
                    <th>Tanggal Input</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $no = 1;
                    if (!empty(htmlentities($_GET['cari']))) {
                        $periode = htmlentities($_GET['bln']) . '-' . htmlentities($_GET['thn']);
                        $hasil = $lihat->periode_jual($periode);
                    } elseif (!empty(htmlentities($_GET['hari']))) {
                        $hari = htmlentities($_GET['tgl']);
                        $hasil = $lihat->hari_jual($hari);
                    } else {
                        $hasil = $lihat->jual();
                    }

                    $bayar = 0;
                    $jumlah = 0;
                    $modal = 0;
                    foreach ($hasil as $isi) {
                        $bayar += $isi['total'];
                        $modal += $isi['harga_beli'] * $isi['jumlah'];
                        $jumlah += $isi['jumlah'];
                ?>
                <tr>
                    <td><?= $no; ?></td>
                    <td><?= htmlentities($isi['id_barang']); ?></td>
                    <td><?= htmlentities($isi['nama_barang']); ?></td>
                    <td><?= htmlentities($isi['jumlah']); ?></td>
                    <td>Rp.<?= number_format($isi['harga_beli'] * $isi['jumlah']); ?>,-</td>
                    <td>Rp.<?= number_format($isi['total']); ?>,-</td>
                    <td><?= htmlentities($isi['nm_member']); ?></td>
                    <td><?= htmlentities($isi['nama_pelanggan'] ?? '-'); ?></td>
                    <td><?= htmlentities($isi['tanggal_input']); ?></td>
                </tr>
                <?php $no++; } ?>
                <tr>
                    <td colspan="3"><b>Total Terjual</b></td>
                    <td><b><?= $jumlah; ?></b></td>
                    <td><b>Rp.<?= number_format($modal); ?>,-</b></td>
                    <td><b>Rp.<?= number_format($bayar); ?>,-</b></td>
                    <td><b>Keuntungan</b></td>
                    <td colspan="2"><b>Rp.<?= number_format($bayar - $modal); ?>,-</b></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>