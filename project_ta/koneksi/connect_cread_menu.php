<?php
include "connect_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = $_POST['nama'];
    $harga  = $_POST['harga'];
    $gambar = $_FILES['gambar']['name'];
    $tmp    = $_FILES['gambar']['tmp_name'];

    // folder tujuan
    $folder = "unggahan/";

    // pastikan folder ada
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    // simpan nama unik untuk gambar
    $namaFile = time() . "_" . basename($gambar);
    $pathFile = $folder . $namaFile;

    // pindahkan file ke folder
    if (move_uploaded_file($tmp, $pathFile)) {
        // simpan hanya nama file ke database
        $sql = "INSERT INTO menu (nama, harga, gambar) VALUES ('$nama', '$harga', '$namaFile')";
        $db->query($sql);
        echo "<script>alert('Menu berhasil ditambahkan!'); window.location='../product_admin.php';</script>";
    } else {
        echo "<script>alert('Gagal upload gambar!'); window.location='../form_menu.php';</script>";
    }
}
?>
