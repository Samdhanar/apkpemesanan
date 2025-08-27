<?php
include "koneksi/connect_db.php";
date_default_timezone_set('Asia/Jakarta');
session_start();

// Kalau belum login atau bukan kasir → kembali ke index.php
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'kasir') {
    header("Location: index.php");
    exit;
}

// ================== PROSES PEMBAYARAN ==================
if (isset($_POST['aksi']) && $_POST['aksi'] == "bayar") {
    $meja = $_POST['meja'];
    $jam_menit = $_POST['jam_menit'];
    $bayar = intval($_POST['bayar']);
    $diskon = intval($_POST['diskon']); // ambil diskon

    // Ambil total harga transaksi berdasarkan meja + jam_menit
    $q = $db->query("
        SELECT SUM(total_harga) as total_harga, MAX(tanggal) as tanggal
        FROM rekap_penjualan 
        WHERE meja='$meja' AND DATE_FORMAT(tanggal, '%H:%i')='$jam_menit' AND status='BELUM'
    ");
    $d = $q->fetch_assoc();
    $total = $d['total_harga'];
    $tanggal = date("d-m-Y H:i:s", strtotime($d['tanggal']));

    // Hitung total setelah diskon
    $total_setelah_diskon = $total - ($total * $diskon / 100);

    if ($bayar < $total_setelah_diskon) {
        echo "<script>alert('Uang bayar kurang dari total belanja!'); window.location='kasir.php';</script>";
        exit;
    }

    // update hanya status
    $db->query("UPDATE rekap_penjualan 
            SET status='LUNAS'
            WHERE meja='$meja' AND DATE_FORMAT(tanggal, '%H:%i')='$jam_menit' AND status='BELUM'");


    // Ambil detail pesanan untuk struk
    $items = [];
    $result = $db->query("
        SELECT m.nama, r.jumlah, r.total_harga
        FROM rekap_penjualan r
        JOIN menu m ON r.product_id = m.id
        WHERE r.meja='$meja' AND DATE_FORMAT(r.tanggal, '%H:%i')='$jam_menit'
    ");
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    $potongan = $total - $total_setelah_diskon;
    $kembali = $bayar - $total_setelah_diskon;
?>

    <!--HTML untuk form struk-->
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <title>Cetak Struk</title>
        <style>
            body {
                font-family: monospace;
            }

            .struk {
                width: 300px;
                margin: auto;
            }

            .center {
                text-align: center;
            }

            .right {
                float: right;
            }

            hr {
                border: 1px dashed #000;
            }
        </style>
    </head>

    <body onload="window.print()">
        <!-- Struk Pembayaran -->
        <div class="struk">
            <div class="center">
                <h3>CAFE ELKUSA</h3>
                <small>Jl. Contoh Alamat No. 123<br>Telp: 1903-1903-1903</small>
            </div>
            <hr>
            Meja : <?= $meja ?><br>
            Jam Pesan : <?= $jam_menit ?><br>
            Tanggal : <?= $tanggal ?><br>
            <hr>
            <?php foreach ($items as $row): ?>
                <?= $row['nama']; ?> (x<?= $row['jumlah']; ?>)
                <span class="right">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></span><br>
            <?php endforeach; ?>
            <hr>
            Subtotal : Rp <?= number_format($total, 0, ',', '.'); ?><br>
            Diskon <?= $diskon ?>% : Rp <?= number_format($potongan, 0, ',', '.'); ?><br>
            Total&nbsp;&nbsp; : Rp <?= number_format($total_setelah_diskon, 0, ',', '.'); ?><br>
            Bayar&nbsp;&nbsp; : Rp <?= number_format($bayar, 0, ',', '.'); ?><br>
            Kembali : Rp <?= number_format($kembali, 0, ',', '.'); ?><br>
            <hr>
            <div class="center">
                Terima kasih sudah berbelanja 🙏<br>
                *** Sampai Jumpa Lagi ***
            </div>
        </div>
    </body>

    </html>
<?php
    exit;
}
// ================== END PROSES BAYAR ==================


// ================== TAMPILAN KASIR ==================
//ambil data 
$query = "
    SELECT 
        r.meja,
        DATE_FORMAT(r.tanggal, '%H:%i') AS jam_menit,
        GROUP_CONCAT(CONCAT(m.nama, ' (', r.jumlah, ')') SEPARATOR ', ') AS daftar_pesanan,
        SUM(r.total_harga) AS total_harga
    FROM rekap_penjualan r
    JOIN menu m ON r.product_id = m.id
    WHERE r.status = 'BELUM'
    GROUP BY r.meja, jam_menit
    ORDER BY MAX(r.tanggal) DESC
";
$result = $db->query($query);
?>

<!--HTML untuk diskon-->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Elkusa Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Fungsi pembayaran dengan diskon
        function pembayaran(meja, jam_menit, total) {
            let diskon = prompt("Masukkan diskon (%) jika ada:", "0");
            if (diskon === null || isNaN(diskon) || diskon < 0) diskon = 0;

            let total_setelah_diskon = total - (total * diskon / 100);

            alert("Total belanja: Rp" + total.toLocaleString() +
                "\nDiskon: " + diskon + "%" +
                "\nTotal setelah diskon: Rp" + total_setelah_diskon.toLocaleString());

            let bayar = prompt("Masukkan jumlah uang bayar (Total: Rp" + total_setelah_diskon.toLocaleString() + "):", "");
            if (bayar && !isNaN(bayar)) {
                if (parseInt(bayar) < total_setelah_diskon) {
                    alert("Uang bayar kurang dari total belanja!");
                    return;
                }
                let form = document.getElementById("formBayar");
                form.meja.value = meja;
                form.jam_menit.value = jam_menit;
                form.bayar.value = bayar;
                form.diskon.value = diskon;
                form.submit();
            }
        }
    </script>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-light bg-white mb-4 shadow-sm border">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand d-flex align-items-center text-dark m-0" href="#">
                <img src="assets/image/logo_cafe.png" alt="Logo" height="70" class="me-2 rounded-circle">
                Elkusa Cafe
            </a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>
    <!-- END HEADER -->

    <div class="bg-light">
        <form method="POST" id="formBayar" style="display:none;">
            <input type="hidden" name="aksi" value="bayar">
            <input type="hidden" name="meja">
            <input type="hidden" name="jam_menit">
            <input type="hidden" name="bayar">
            <input type="hidden" name="diskon">
        </form>

        <div class="container-lg mt-4 mb-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-start">
                    <h4 class="mb-0"> Kasir</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th class="text-center">Meja</th>
                                <th class="text-center">Jam Pesan</th>
                                <th class="text-center">Daftar Pesanan</th>
                                <th class="text-center">Total Harga</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center"><?= $row['meja']; ?></td>
                                        <td class="text-center"><?= $row['jam_menit']; ?></td>
                                        <td><?= $row['daftar_pesanan']; ?></td>
                                        <td class="text-start">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-success btn-sm"
                                                onclick="pembayaran('<?= $row['meja']; ?>', '<?= $row['jam_menit']; ?>', <?= $row['total_harga']; ?>)">
                                                Pembayaran
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada pesanan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <hr>
        <footer class="bg-light text-center py-4">
            <p class="mb-0">© 2025 masdhanar | Elkusa Cafe </p>
        </footer>
    </div>
</body>

</html>