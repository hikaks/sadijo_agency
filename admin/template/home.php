<h3>Dashboard</h3>
<br/>

<?php 
// Cek stok barang kurang dari atau sama dengan 3
$sql = "SELECT * FROM barang WHERE stok <= 3";
$row = $config->prepare($sql);
$row->execute();
$r = $row->rowCount();

if ($r > 0) {
    echo "
    <div class='alert alert-warning'>
        <span class='glyphicon glyphicon-info-sign'></span> 
        Ada <span style='color:red'>$r</span> barang yang stok tersisa kurang dari 3 items. Silakan pesan lagi !!
        <span class='pull-right'><a href='index.php?page=barang&stok=yes'>Tabel Barang <i class='fa fa-angle-double-right'></i></a></span>
    </div>";
}

// Statistik
$hasil_barang = $lihat->barang_row();
$hasil_kategori = $lihat->kategori_row();
$stok = $lihat->barang_stok_row();
$jual = $lihat->jual_row();

// Data grafik stok barang
$stmt = $config->prepare("SELECT nama_barang, stok FROM barang");
$stmt->execute();
$nama_barang = [];
$stok_barang = [];
while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $nama_barang[] = $data['nama_barang'];
    $stok_barang[] = $data['stok'];
}
?>

<div class="row">
    <!-- Kartu Statistik -->
    <?php
    $cards = [
        ['title' => 'Nama Barang', 'icon' => 'fas fa-cubes', 'value' => $hasil_barang, 'link' => 'barang'],
        ['title' => 'Stok Barang', 'icon' => 'fas fa-chart-bar', 'value' => $stok['jml'], 'link' => 'barang'],
        ['title' => 'Telah Terjual', 'icon' => 'fas fa-upload', 'value' => $jual['stok'], 'link' => 'laporan'],
        ['title' => 'Kategori Barang', 'icon' => 'fa fa-bookmark', 'value' => $hasil_kategori, 'link' => 'kategori']
    ];

    foreach ($cards as $card) {
        echo "
        <div class='col-md-3 mb-3'>
            <div class='card'>
                <div class='card-header bg-primary text-white'>
                    <h6 class='pt-2'><i class='{$card['icon']}'></i> {$card['title']}</h6>
                </div>
                <div class='card-body text-center'>
                    <h1>" . number_format($card['value']) . "</h1>
                </div>
                <div class='card-footer'>
                    <a href='index.php?page={$card['link']}'>Tabel {$card['title']} <i class='fa fa-angle-double-right'></i></a>
                </div>
            </div>
        </div>";
    }
    ?>
</div>

<!-- Grafik Stok Barang -->
<div class="card mt-4">
    <div class="card-header bg-success text-white">
        <h6 class="pt-2"><i class="fas fa-chart-bar"></i> Grafik Stok per Barang</h6>
    </div>
    <div class="card-body chart-wrapper">
        <canvas id="stokChart" height="150"></canvas>
    </div>
</div>

<?php
// Data grafik penjualan
$sql_jual = "SELECT b.nama_barang, SUM(n.jumlah) AS total_terjual
             FROM nota n
             JOIN barang b ON n.id_barang = b.id_barang
             GROUP BY b.nama_barang";
$stmt_jual = $config->prepare($sql_jual);
$stmt_jual->execute();

$barang_terjual = [];
$jumlah_terjual = [];

while ($data = $stmt_jual->fetch(PDO::FETCH_ASSOC)) {
    $barang_terjual[] = $data['nama_barang'];
    $jumlah_terjual[] = $data['total_terjual'];
}
?>

<!-- Grafik Penjualan Barang -->
<div class="card mt-4">
    <div class="card-header bg-warning text-white">
        <h6><i class="fas fa-chart-line"></i> Grafik Penjualan per Barang</h6>
    </div>
    <div class="card-body chart-wrapper">
        <canvas id="penjualanChart" height="150"></canvas>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const stokChart = new Chart(document.getElementById('stokChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($nama_barang); ?>,
        datasets: [{
            label: 'Stok Barang',
            data: <?php echo json_encode($stok_barang); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Jumlah Stok' }
            },
            x: {
                title: { display: true, text: 'Nama Barang' }
            }
        }
    }
});

const penjualanChart = new Chart(document.getElementById('penjualanChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($barang_terjual); ?>,
        datasets: [{
            label: 'Jumlah Terjual',
            data: <?php echo json_encode($jumlah_terjual); ?>,
            backgroundColor: 'rgba(255, 159, 64, 0.7)',
            borderColor: 'rgba(255, 159, 64, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Jumlah Terjual' }
            },
            x: {
                title: { display: true, text: 'Nama Barang' }
            }
        }
    }
});
</script>

<!-- Tambahkan CSS untuk ukuran grafik -->
<style>
.chart-wrapper {
    height: 300px;
    overflow-x: auto;
}
</style>
