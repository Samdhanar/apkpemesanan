<?php
session_start();
// Kalau belum login atau bukan admin → kembali ke index.php
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit;
}
?>
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Eklusa Cafe</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    </head>

    <body style="margin-top: 0;">
        <?php
        include 'header.php';
        ?>

       <!--content-->
        <?php
        include 'rekap_pesanan_lunas.php';
        ?>
        <!--content end-->
        <!-- Footer -->
        <hr>
        <footer class="bg-light text-center py-4">
             <p class="mb-0">© 2025 masdhanar | Elkusa Cafe </p>
        </footer>
    </body>

    </html>