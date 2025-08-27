<?php
session_start();
include "connect_db.php";

// pastikan data dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produk_id'])) {
    $meja   = $_POST['meja'];
    $produk = $_POST['produk_id'];
    $jumlah = $_POST['jumlah'];
    $harga  = $_POST['harga_satuan'];

    // simpan ke tabel pesanan
    foreach ($produk as $i => $id_produk) {
        $jml   = intval($jumlah[$i]);
        $harga_satuan = intval($harga[$i]);
        $total = $jml * $harga_satuan;

        // karena kolom = meja, product_id, jumlah, total_harga, tanggal
        $stmt = $db->prepare("INSERT INTO pesanan (meja, product_id, jumlah, total_harga, tanggal) VALUES (?,?,?,?,NOW())");
        $stmt->bind_param("iiii", $meja, $id_produk, $jml, $total);
        $stmt->execute();
    }

    // simpan pesan ke session
    $_SESSION['notif'] = "Pesanan sedang diproses. Silahkan tunggu 15 menit.";

    // redirect kembali ke form
    header("Location: ../form_pemesanan.php");
    exit();
} else {
    header("Location: ../form_pemesanan.php");
    exit();
}
