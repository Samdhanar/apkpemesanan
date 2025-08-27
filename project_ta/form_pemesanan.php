<?php
session_start();
include "koneksi/connect_db.php";

// --- Notifikasi sukses ---
if (isset($_SESSION['notif'])) {
  echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Sukses!</strong> ' . $_SESSION['notif'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
  unset($_SESSION['notif']);
}

// ambil semua data produk 
$query = mysqli_query($db, "SELECT id, nama, harga, gambar FROM menu ORDER BY id DESC");
$produk = [];
while ($row = mysqli_fetch_assoc($query)) {
  $produk[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Elkusa Cafe</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      font-family: "Segoe UI", Roboto, sans-serif;
    }

    /*  Navbar */
    .navbar {
      background: linear-gradient(90deg, #ffffff, #f1f1f1);
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    }

    .navbar-brand span {
      font-size: 1.4rem;
      font-weight: 500;
      color: #333;
    }

    .navbar-nav .nav-link {
      color: #555 !important;
      transition: color 0.2s;
    }

    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
      color: #0d6efd !important;
      font-weight: 500;
    }

    /*  Card Form */
    .card {
      border: none;
      border-radius: 15px;
      background: #fff;
    }

    .card h2 {
      font-weight: 600;
      color: #333;
    }

    /*  Produk */
    .produk-card {
      cursor: pointer;
      border: none;
      border-radius: 12px;
      overflow: hidden;
      transition: all 0.25s ease-in-out;
      background: #fff;
    }

    .produk-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
    }

    .produk-img {
      width: 100%;
      aspect-ratio: 1 / 1; /* Ukuran 1:1 */
      object-fit: cover;/* isi penuh, tanpa distorsi */
      border-radius: 8px;
      display: block;
    }


    .card-body h6 {
      font-weight: 500;
      margin-bottom: 5px;
    }

    .card-body p {
      font-size: 0.9rem;
    }

    /*  Table */
    table {
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
    }

    table thead {
      background: #0d6efd;
      color: #fff;
    }

    table th,
    table td {
      vertical-align: middle;
    }

    /*  Button */
    .btn-primary {
      border-radius: 8px;
      padding: 10px 20px;
      font-weight: 500;
    }

    .btn-danger {
      border-radius: 8px;
      padding: 6px 14px;
    }

    /* Footer */
    footer {
      background: #f8f9fa;
      border-top: 1px solid #e5e5e5;
      font-size: 0.9rem;
      color: #666;
    }
  </style>

</head>

<body class="bg-light">

  <!-- navbar -->
  <nav class="navbar navbar-expand-lg bg-light border sticky-top">
    <div class="container-lg">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="assets/image/logo_cafe.png" alt="Elkusa Cafe" height="70" class="me-2">
        <span>Elkusa Cafe</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link link-dark <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link link-dark <?= basename($_SERVER['PHP_SELF']) == 'form_pemesanan.php' ? 'active' : '' ?>" href="form_pemesanan.php">Pesan</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Konten -->
  <div class="container mt-5 mb-5">
    <div class="card shadow-lg p-4">

      <!--Aksi Form Pemesanan-->
      <h2 class="mb-4">Pesan disini</h2>
      <form action="koneksi/connect_pemesanan.php" method="POST">

        <!-- Nomor Meja -->
        <div class="mb-3">
          <label for="meja" class="form-label">Nomor Meja</label>
          <input type="number" class="form-control" id="meja" name="meja" required>
        </div>

        <!-- Search -->
        <div class="mb-3">
          <input type="text" class="form-control" id="cariMenu" placeholder="Cari menu...">
        </div>

        <!-- Daftar Produk -->
        <h5>Pilih Menu:</h5>
        <div class="row row-cols-2 row-cols-md-4 g-3 mb-4" id="daftarMenu">
          <?php foreach ($produk as $p): ?>
            <div class="col produk-item">
              <div class="card produk-card border rounded shadow-sm"
                onclick="tambahPesanan('<?= $p['id']; ?>','<?= $p['nama']; ?>',<?= $p['harga']; ?>)">
                <img src="koneksi/unggahan/<?= $p['gambar']; ?>" class="card-img-top produk-img" alt="<?= $p['nama']; ?>">
                <div class="card-body text-center">
                  <h6 class="card-title"><?= $p['nama']; ?></h6>
                  <p class="text-primary fw-bold">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></p>
                </div>
              </div>
            </div>

          <?php endforeach; ?>
        </div>

        <!-- Tabel Pesanan -->
        <table class="table table-bordered mt-4" id="tabelPesanan">
          <thead>
            <tr>
              <th>Produk</th>
              <th>Jumlah</th>
              <th>Total Harga</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

        <!-- Total Belanja -->
        <div class="text-end mt-3">
          <h5>Total Belanja: <span id="grandTotal">Rp 0</span></h5>
        </div>

        <!-- Tombol Simpan -->
        <div class="text-end">
          <button type="submit" class="btn btn-primary">Simpan Pesanan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Footer -->

  <footer class="bg-light text-center py-4">
    <p class="mb-0">© 2025 masdhanar |Elkusa Cafe </p>
  </footer>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    //tambah menu 
    function tambahPesanan(id, nama, harga) {
      let tabel = document.getElementById("tabelPesanan").querySelector("tbody");
      let existingRow = [...tabel.rows].find(r => r.querySelector("input[name='produk_id[]']").value == id);

      if (existingRow) {
        let jumlahInput = existingRow.querySelector("input[name='jumlah[]']");
        jumlahInput.value = parseInt(jumlahInput.value) + 1;
        hitungTotal(jumlahInput, harga);
        return;
      }

      let row = tabel.insertRow();
      row.innerHTML = `
    <td>
      ${nama}
      <input type="hidden" name="produk_id[]" value="${id}">
      <input type="hidden" name="harga_satuan[]" value="${harga}">
    </td>
    <td>
     <input type="number" class="form-control" name="jumlah[]" value="1" min="1" 
       oninput="hitungTotal(this, ${harga})" required>
    </td>
    <td>
      <input type="number" class="form-control" name="total_harga[]" value="${harga}" readonly>
    </td>
    <td>
      <button type="button" class="btn btn-danger" onclick="hapusBaris(this)">Hapus</button>
    </td>
  `;

      updateGrandTotal();
    }

    // Hapus Baris Pesanan 
    function hapusBaris(btn) {
      btn.closest("tr").remove();
      updateGrandTotal();
    }

    //Cari Menu
    document.getElementById("cariMenu").addEventListener("input", function() {
      let keyword = this.value.toLowerCase();
      document.querySelectorAll("#daftarMenu .produk-item").forEach(item => {
        let nama = item.querySelector(".card-title").textContent.toLowerCase();
        item.style.display = nama.includes(keyword) ? "block" : "none";
      });
    });

    //Hitung Total Per Item ( Dalam 1 Baris dengan Pesanan Sama )
    function hitungTotal(input, harga) {
      let row = input.closest("tr");
      let totalInput = row.querySelector("input[name='total_harga[]']");
      let jumlah = parseInt(input.value) || 0;
      totalInput.value = jumlah * harga;
      updateGrandTotal();
    }

    //Hitung Total Pesanan 
    function updateGrandTotal() {
      let total = 0;
      document.querySelectorAll("input[name='total_harga[]']").forEach(el => {
        total += parseInt(el.value) || 0;
      });
      document.getElementById("grandTotal").innerText = "Rp " + total.toLocaleString();
    }
  </script>

</body>

</html>