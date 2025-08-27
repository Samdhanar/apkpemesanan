    <?php
    include "koneksi/connect_db.php";
    $query = mysqli_query($db, "SELECT * FROM hero_text ORDER BY id ASC");
    $hero_data = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $hero_data[] = $row;
    }
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Elkusa Cafe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        /* Navbar */
        .navbar {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .navbar .nav-link {
            position: relative;
            padding-bottom: 6px;
            transition: color 0.3s ease;
        }

        .navbar .nav-link:hover {
            color: #0d6efd !important;
        }

        .navbar .nav-link.active {
            color: #0d6efd !important;
            font-weight: 500;
        }

        .navbar .nav-link.active::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            background-color: #0066ffff;
            border-radius: 2px;
        }

        /* Hero section */
        .hero {
            background: url("assets/image/bg-cafe.jpg") center/cover no-repeat;
            color: white;
            text-align: center;
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            min-height: 300px;
            /* tambahan agar tinggi stabil */
            display: flex;
            /* tinggi minimal hero */
            align-items: center;
            justify-content: center;
            /* konten selalu di tengah vertikal */
            padding: 40px 20px;
            /* padding secukupnya */
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #0037ff80;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 700px;
            margin: auto;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
        }

        .fade-text {
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .fade-text.show {
            opacity: 1;
        }

        /* Search */
        #searchInput {
            border: 2px solid #0d6efd;
            border-radius: 50px;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }

        #searchInput:focus {
            border: 2px solid #0a58ca;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.3);
            outline: none;
        }

        /* Card Produk */
        .card-produk {
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-produk:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            aspect-ratio: 1 / 1;
            /* bikin gambar persegi */
            object-fit: cover;
            /* biar tidak gepeng */
            width: 100%;
            /* full lebar card */
        }

        .card-img-container {
            position: relative;
            width: 100%;
            aspect-ratio: 1 / 1;
            /* bikin kotak 1:1 */
            overflow: hidden;
        }

        .card-img-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* crop gambar biar rapi */
        }

        .harga-badge {
            font-size: 1rem;
            padding: 6px 12px;
            border-radius: 50px;
        }

        /* WhatsApp button */
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 20px;
            right: 20px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        /* Tooltip */
        .whatsapp-tooltip {
            position: absolute;
            bottom: 70px;
            /* jarak di atas icon */
            right: 0;
            background: #2f3972ff;
            color: #fff;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 14px;
            white-space: nowrap;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease-in-out;
            pointer-events: none;
        }

        /* Tampilkan saat hover */
        .whatsapp-float:hover .whatsapp-tooltip {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand bg-white border sticky-top">
        <div class="container-lg">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="assets/image/logo_cafe.png" alt="Elkusa Cafe" height="60" class="me-2">
                <span class="fs-4">Elkusa Cafe</span>
            </a>
            <div class="collapse navbar-collapse justify-content-end">
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

    <!-- Hero Section -->
    <!-- Hero Section -->
    <div class="container-fluid mt-4">
        <div class="hero text-center text-white">
            <div class="hero-content">
                <h1 id="welcome-text" class="fade-text"></h1>
                <p class="lead fade-text" id="welcome-sub"></p>
            </div>
        </div>
    </div>
    <!--js animasi dan teks berganti-->
    <script>
        // Data dari database
        const heroData = <?php echo json_encode($hero_data); ?>;

        let index = 0;
        const titleEl = document.getElementById("welcome-text");
        const subEl = document.getElementById("welcome-sub");

        function changeText() {
            titleEl.classList.remove("show");
            subEl.classList.remove("show");

            setTimeout(() => {
                index = (index + 1) % heroData.length;
                titleEl.textContent = heroData[index].title;
                subEl.textContent = heroData[index].subtitle;

                titleEl.classList.add("show");
                subEl.classList.add("show");
            }, 1000);
        }

        window.addEventListener("DOMContentLoaded", () => {
            if (heroData.length > 0) {
                titleEl.textContent = heroData[0].title;
                subEl.textContent = heroData[0].subtitle;
                titleEl.classList.add("show");
                subEl.classList.add("show");

                setInterval(changeText, 5000);
            }
        });
    </script>
    <!-- End Hero Section -->

    <!-- Search Menu -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Daftar Menu Produk</h2>
        <div class="d-flex justify-content-center mb-4">
            <div class="search-box w-50">
                <input type="text" class="form-control" placeholder="Cari produk..." id="searchInput">
            </div>
        </div>

        <!-- Produk Cards -->
        <div class="row g-4 mt-4" id="daftarMenu">
            <?php
            include "koneksi/connect_db.php";
            $where = "";
            if (isset($_GET['cari']) && !empty($_GET['cari'])) {
                $cari = mysqli_real_escape_string($db, $_GET['cari']);
                $where = "WHERE nama LIKE '%$cari%' OR harga LIKE '%$cari%'";
            }
            $produk = mysqli_query($db, "SELECT * FROM menu $where ORDER BY id DESC");
            if (mysqli_num_rows($produk) > 0) {
                while ($p = mysqli_fetch_assoc($produk)) { ?>
                    <div class="col-md-4 col-lg-3 produk-item">
                        <div class="card card-produk h-100 shadow-sm">

                            <!-- Container foto 1:1 -->
                            <div class="card-img-container">
                                <img src="koneksi/unggahan/<?php echo $p['gambar']; ?>"
                                    class="card-img-top"
                                    alt="<?php echo $p['nama']; ?>">
                            </div>

                            <div class="card-body text-center">
                                <h5 class="card-title"><?php echo $p['nama']; ?></h5>
                                <span class="badge bg-primary harga-badge">
                                    Rp. <?php echo number_format($p['harga'], 0, ',', '.'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php }
            } else { ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">Tidak ada produk ditemukan.</div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!--floating whastsapp-->
    <a href="https://wa.me/6283823204843" class="whatsapp-float" target="_blank"> <i class="bi bi-whatsapp"></i> <span class="whatsapp-tooltip">Layanan Pengaduan</span></a>

    <!-- Footer -->
    <hr>
    <footer class="text-center py-4 mt-5">
        <p class="mb-0">© 2025 masdhanar | Elkusa Cafe </p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search filter JS
        document.getElementById("searchInput").addEventListener("input", function() {
            let keyword = this.value.toLowerCase();
            document.querySelectorAll("#daftarMenu .produk-item").forEach(item => {
                let nama = item.querySelector(".card-title").textContent.toLowerCase();
                item.style.display = nama.includes(keyword) ? "block" : "none";
            });
        });
    </script>
</body>

</html>