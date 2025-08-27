<?php
include "koneksi/connect_db.php";
date_default_timezone_set('Asia/Jakarta');
// Kalau belum login atau bukan admin → kembali ke index.php
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// === Ambil tipe rekap ===
$tipe = $_GET['tipe'] ?? 'bulanan';
$bulan_aktif = $_GET['bulan'] ?? date('Y-m');
$tanggal_aktif = $_GET['tanggal'] ?? date('Y-m-d');

// === Buat filter SQL ===
if ($tipe == "bulanan") {
    $where = "DATE_FORMAT(r.tanggal, '%Y-%m') = '$bulan_aktif'";
} else {
    $where = "DATE(r.tanggal) = '$tanggal_aktif'";
}

// === Hitung total pendapatan ===
$q_total = "
    SELECT SUM(r.total_harga) AS pendapatan
    FROM rekap_penjualan r
    WHERE $where AND r.status = 'LUNAS'
";
$res_total = $db->query($q_total);
$total_pendapatan = 0;
if ($res_total && $row = $res_total->fetch_assoc()) {
    $total_pendapatan = $row['pendapatan'] ?? 0;
}

// === Query data utama, dikelompokkan per jam & menit ===
$query = "
    SELECT 
        DATE_FORMAT(r.tanggal, '%H:%i') AS jam_menit,
        GROUP_CONCAT(CONCAT(m.nama, ' (', r.jumlah, ')') SEPARATOR ', ') AS daftar_menu,
        SUM(r.jumlah) AS total_jumlah,
        SUM(r.total_harga) AS total_harga
    FROM rekap_penjualan r
    JOIN menu m ON r.product_id = m.id
    WHERE $where AND r.status = 'LUNAS'
    GROUP BY DATE_FORMAT(r.tanggal, '%Y-%m-%d %H:%i')
    ORDER BY r.tanggal ASC
";
$result = $db->query($query);

// === Query menu terfavorit ===
$q_menu = "
    SELECT m.nama, SUM(r.jumlah) AS total_jual
    FROM rekap_penjualan r
    JOIN menu m ON r.product_id = m.id
    WHERE $where AND r.status = 'LUNAS'
    GROUP BY r.product_id
    ORDER BY total_jual DESC
    LIMIT 5
";
$res_menu = $db->query($q_menu);
$menu_labels = [];
$menu_values = [];
while($row = $res_menu->fetch_assoc()){
    $menu_labels[] = $row['nama'];
    $menu_values[] = $row['total_jual'];
}

// === Query jam ramai ===
$q_jam = "
    SELECT HOUR(r.tanggal) AS jam, COUNT(*) AS jumlah_transaksi
    FROM rekap_penjualan r
    WHERE $where AND r.status = 'LUNAS'
    GROUP BY HOUR(r.tanggal)
    ORDER BY jumlah_transaksi DESC
";
$res_jam = $db->query($q_jam);
$jam_labels = [];
$jam_values = [];
while($row = $res_jam->fetch_assoc()){
    $jam_labels[] = $row['jam'] . ':00';
    $jam_values[] = $row['jumlah_transaksi'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Elkusa Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-4">

    <h3 class="mb-4 text-center">Rekap Pesanan Lunas</h3>

    <!-- Filter Form -->
    <form method="get" class="row g-2 mb-3">
        <div class="col-auto">
            <select name="tipe" class="form-select" onchange="this.form.submit()">
                <option value="bulanan" <?= $tipe=='bulanan'?'selected':'' ?>>Bulanan</option>
                <option value="harian" <?= $tipe=='harian'?'selected':'' ?>>Harian</option>
            </select>
        </div>
        <div class="col-auto">
            <?php if ($tipe == 'bulanan'): ?>
                <input type="month" name="bulan" value="<?= $bulan_aktif ?>" class="form-control" onchange="this.form.submit()">
            <?php else: ?>
                <input type="date" name="tanggal" value="<?= $tanggal_aktif ?>" class="form-control" onchange="this.form.submit()">
            <?php endif; ?>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Tombol Export -->
        <a href="export_rekap_penjualan.php" 
        class="btn btn-success mb-3">⬇ Export Excel</a>

        <!-- Total Pendapatan -->
        <h5 class="mb-0">
            Total Pendapatan: 
            <span class="text-dark">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></span>
        </h5>
    </div>

    <!-- Tabel Rekap -->
    <table class="table table-bordered table-striped">
        <thead class="table-primary text-center">
            <tr>
                <th>Menu</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['daftar_menu'] ?></td>
                        <td class="text-center"><?= $row['total_jumlah'] ?></td>
                        <td class="text-end"><?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center">Tidak ada data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Diagram -->
    <div class="row mt-5">
        <div class="col-md-6">
            <h5 class="text-center">Menu Terfavorit</h5>
            <canvas id="chartMenu"></canvas>
        </div>
        <div class="col-md-6">
            <h5 class="text-center">Jam Ramai</h5>
            <canvas id="chartJam"></canvas>
        </div>
    </div>

    <script>
    // Chart Menu Terfavorit
    new Chart(document.getElementById('chartMenu'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($menu_labels) ?>,
            datasets: [{
                data: <?= json_encode($menu_values) ?>,
                backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF']
            }]
        }
    });

    // Chart Jam Ramai
    new Chart(document.getElementById('chartJam'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($jam_labels) ?>,
            datasets: [{
                label: 'Jumlah Transaksi',
                data: <?= json_encode($jam_values) ?>,
                backgroundColor: '#36A2EB'
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
    </script>
</div>
</body>
</html>
