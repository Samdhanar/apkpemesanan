<?php
include "koneksi/connect_db.php";
session_start();

// Kalau belum login atau bukan admin → kembali ke index.php
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}
// ================== PROSES HAPUS PRODUK ==================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = mysqli_query($db, "SELECT gambar FROM menu WHERE id=$id");
    $row = mysqli_fetch_assoc($query);

    if ($row && !empty($row['gambar']) && file_exists("koneksi/unggahan/" . $row['gambar'])) {
        unlink("koneksi/unggahan/" . $row['gambar']);
    }

    mysqli_query($db, "DELETE FROM menu WHERE id=$id");
    header("Location: product_admin.php?msg=deleted");
    exit;
}

// ================== SEARCH ==================
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$where  = $search ? "WHERE nama LIKE '%" . mysqli_real_escape_string($db, $search) . "%'" : "";

// ambil semua produk tanpa pagination
$produk = mysqli_query($db, "SELECT * FROM menu $where ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Elkusa Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        #searchInput {
            border: 2px solid #000;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        #searchInput:focus {
            border: 3px solid #000;
            box-shadow: 0 0 6px rgba(0, 91, 187, 0.5);
            outline: none;
        }

        .card-produk {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-produk:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: .5rem;
            border-top-right-radius: .5rem;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container my-5 mt-5">
        <h2 class="text-center mb-4">Daftar Menu Produk</h2>

        <!-- 🔹 Tombol Tambah & Search -->
        <div class="row mb-4 mt-4 align-items-center">
            <!-- Tombol Tambah -->
            <div class="col-md-3 col-12 mb-2 mb-md-0 mt-5">
                <a href="form_menu.php" class="btn btn-success btn-lg w-100">
                    <i class="bi bi-plus-circle"></i> Tambah Produk </a>
            </div>

            <!-- Search Box -->
            <div class="col-md-6 col-12 ms-auto mt-5">
                <input type="text" class="form-control form-control-lg"
                    placeholder="Cari produk..." id="searchInput">
            </div>
        </div>

        <!-- 🔹 JavaScript Filter -->
        <script>
            document.getElementById("searchInput").addEventListener("input", function() {
                let keyword = this.value.toLowerCase();
                document.querySelectorAll("#daftarMenu .produk-item").forEach(item => {
                    let nama = item.querySelector(".card-title").textContent.toLowerCase();
                    item.style.display = nama.includes(keyword) ? "block" : "none";
                });
            });
        </script>



        <!-- 🔹 Card Produk -->
        <div class="row g-4 mt-3" id="daftarMenu">
            <?php if (mysqli_num_rows($produk) > 0) {
                while ($p = mysqli_fetch_assoc($produk)) { ?>
                    <div class="col-md-4 col-lg-3 produk-item">
                        <div class="card card-produk h-100 shadow-sm">
                            <img src="koneksi/unggahan/<?= $p['gambar']; ?>"
                                class="card-img-top"
                                alt="<?= $p['nama']; ?>">
                            <div class="card-body text-center d-flex flex-column">
                                <h5 class="card-title"><?= $p['nama']; ?></h5>
                                <p class="card-text text-primary fw-bold">
                                    Rp. <?= number_format($p['harga'], 0, ',', '.'); ?>
                                </p>
                                <div class="mt-auto d-flex justify-content-between">
                                    <a href="edit_menu.php?id=<?= $p['id']; ?>"
                                        class="btn btn-warning btn-sm w-50 me-1">Update</a>
                                    <a href="product_admin.php?delete=<?= $p['id']; ?>&search=<?= urlencode($search) ?>"
                                        class="btn btn-danger btn-sm w-50 ms-1"
                                        onclick="return confirm('Yakin mau hapus produk ini?')">Hapus</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
            } else { ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        Tidak ada produk ditemukan.
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <hr>
    <footer class="bg-light text-center py-4">
         <p class="mb-0">© 2025 masdhanar | Elkusa Cafe </p>
    </footer>
</body>

</html>