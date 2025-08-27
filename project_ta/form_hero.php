<?php
include "koneksi/connect_hero.php";

// === PROSES TAMBAH DATA ===
if (isset($_POST['add'])) {
    $title = mysqli_real_escape_string($db, $_POST['title']);
    $subtitle = mysqli_real_escape_string($db, $_POST['subtitle']);
    mysqli_query($db, "INSERT INTO hero_text (title, subtitle) VALUES ('$title','$subtitle')");
    header("Location: setting.php"); // setelah tambah kembali ke hero.php
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Hero Text</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

    <h2 class="mb-4">Tambah Hero Text</h2>

    <div class="card">
        <div class="card-header">Form Tambah Hero Text</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Judul (Title)</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sub Judul (Subtitle)</label>
                    <textarea name="subtitle" class="form-control" rows="2" required></textarea>
                </div>
                <button type="submit" name="add" class="btn btn-primary">Simpan</button>
                <a href="setting.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
