<?php

// Tambah Pelanggan
if (isset($_POST['submit'])) {
    $nama = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['no_telepon'];
    $email = $_POST['email'];

    $stmt = $config->prepare("INSERT INTO pelanggan (nama_pelanggan, alamat, no_telepon, email) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nama, $alamat, $telepon, $email]);
    echo "<script>alert('Data pelanggan berhasil disimpan');window.location='index.php?page=pelanggan';</script>";
}

// Hapus Pelanggan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $config->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?");
    $stmt->execute([$id]);
    echo "<script>alert('Data pelanggan berhasil dihapus');window.location='index.php?page=pelanggan';</script>";
}
?>

<h4>Form Input Pelanggan</h4>
<form method="POST" action="">
    <div class="form-group">
        <label>Nama Pelanggan</label>
        <input type="text" name="nama_pelanggan" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Alamat</label>
        <textarea name="alamat" class="form-control" required></textarea>
    </div>
    <div class="form-group">
        <label>No Telepon</label>
        <input type="number" name="no_telepon" class="form-control" required pattern="\\d+" title="Hanya angka yang diperbolehkan">
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
</form>

<hr>
<h4>Daftar Pelanggan</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>Telepon</th>
            <th>Email</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $stmt = $config->prepare("SELECT * FROM pelanggan ORDER BY id_pelanggan DESC");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
            <td><?= htmlspecialchars($row['alamat']); ?></td>
            <td><?= htmlspecialchars($row['no_telepon']); ?></td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td>
               <!-- <a href="admin/module/pelanggan/edit.php?id=<?= $row['id_pelanggan']; ?>" class="btn btn-warning btn-sm">Edit</a> -->
                <a href="index.php?page=pelanggan&hapus=<?= $row['id_pelanggan']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger btn-sm">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
