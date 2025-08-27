<?php
session_start();
include "koneksi/connect_db.php";
// Kalau belum login atau bukan pelayan → kembali ke index.php
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'pelayan') {
    header("Location: index.php");
    exit;
}

// Jika ada aksi selesai (pesanan dipindahkan ke rekap_penjualan)
if (isset($_GET['selesai'])) {
    $meja = intval($_GET['selesai']);

    // Pindahkan pesanan ke rekap_penjualan
    $db->query("
        INSERT INTO rekap_penjualan (meja, product_id, jumlah, total_harga, tanggal, status, uang_bayar)
        SELECT meja, product_id, SUM(jumlah) AS jumlah, SUM(total_harga) AS total_harga, NOW(), 'BELUM', NULL
        FROM pesanan 
        WHERE meja = '$meja'
        GROUP BY product_id
    ");

    // Hapus pesanan dari tabel pesanan
    $db->query("DELETE FROM pesanan WHERE meja = '$meja'");

    header("Location: halaman_pelayan.php");
    exit;
}

// Ambil pesanan aktif (dari tabel pesanan)
$query = "
    SELECT 
        x.meja,
        GROUP_CONCAT(CONCAT(m.nama, ' (', x.jumlah, ')') SEPARATOR ', ') AS daftar_pesanan,
        SUM(x.total_harga) AS total_harga
    FROM (
        SELECT 
            p.meja,
            p.product_id,
            SUM(p.jumlah) AS jumlah,
            SUM(p.total_harga) AS total_harga
        FROM pesanan p
        GROUP BY p.meja, p.product_id
    ) x
    JOIN menu m ON m.id = x.product_id
    GROUP BY x.meja
    ORDER BY x.meja ASC
";
$result = $db->query($query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Elkusa Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .page-wrapper {
            border: 2px solid #0d6efd;
            border-radius: 10px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .page-title {
            background-color: #0d6efd;
            color: white;
            padding: 12px;
            font-weight: bold;
            text-align: left;
            border-radius: 8px 8px 0 0;
            margin: -20px -20px 20px -20px;
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 40px;
        }
    </style>
</head>


<body>
    <!-- HEADER NAVBAR -->
    <nav class="navbar navbar-light bg-white mb-4 shadow-sm border">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- KIRI: Logo + Teks -->
            <a class="navbar-brand d-flex align-items-center text-dark m-0" href="#">
                <img src="assets/image/logo_cafe.png" alt="Logo" height="70" class="me-2 rounded-circle">
                Elkusa Cafe
            </a>

            <!-- KANAN: Tombol Logout -->
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>
    <!-- END HEADER -->

    <div class="container mt-5">
        <div class="page-wrapper position-relative">

            <!-- Judul -->
            <h2 class="page-title"> Pelayan</h2>

            <!-- Tabel Pesanan -->
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>Meja</th>
                        <th>Daftar Pesanan</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?php echo $row['meja']; ?></td>
                                <td><?php echo $row['daftar_pesanan']; ?></td>
                                <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <a href="?selesai=<?php echo $row['meja']; ?>"
                                        class="btn btn-success btn-sm"
                                        onclick="return confirm('Pesanan meja <?php echo $row['meja']; ?> sudah selesai?')">
                                        ✅ Selesai
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Belum ada pesanan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <hr>
        <footer class="bg-light text-center py-4">
             <p class="mb-0">© 2025 masdhanar | Elkusa Cafe </p>
        </footer>
    </div>
</body>

</html>