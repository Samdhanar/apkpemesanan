<?php
include "koneksi/connect_hero.php";
session_start();

// === PROSES UPDATE DATA ===
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $title = mysqli_real_escape_string($db, $_POST['title']);
    $subtitle = mysqli_real_escape_string($db, $_POST['subtitle']);
    mysqli_query($db, "UPDATE hero_text SET title='$title', subtitle='$subtitle' WHERE id=$id");
    header("Location: setting.php");
    exit;
}

// === PROSES HAPUS DATA ===
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($db, "DELETE FROM hero_text WHERE id=$id");
    header("Location: setting.php");
    exit;
}

// === AMBIL DATA UNTUK DITAMPILKAN ===
$result = getHeroTexts($db);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Hero Text</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <style>
        /* Supaya tabel punya sudut melengkung */
        .table-rounded {
            border-collapse: separate !important;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
        }

        /* Header pojok atas */
        .table-rounded thead tr:first-child th:first-child {
            border-top-left-radius: 12px;
        }
        .table-rounded thead tr:first-child th:last-child {
            border-top-right-radius: 12px;
        }

        /* Body pojok bawah */
        .table-rounded tbody tr:last-child td:first-child {
            border-bottom-left-radius: 12px;
        }
        .table-rounded tbody tr:last-child td:last-child {
            border-bottom-right-radius: 12px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            
            <!-- Sidebar kiri -->
            <div class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">
                <?php include "slidebar.php"; ?>
            </div>

            <!-- Konten kanan -->
            <div class="col-md-9 col-lg-10 p-4">
                <h2 class="mb-4">Hero Text Data</h2>

                <!-- Tombol Tambah Data -->
                <a href="form_hero.php" class="btn btn-primary mb-3">+ Tambah Hero Text</a>

                <!-- Tabel Data -->
                <table class="table table-bordered table-rounded">
                    <thead class="table-primary">
                        <tr>
                            <th>Title</th>
                            <th>Subtitle</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?= $row['title'] ?></td>
                                <td><?= $row['subtitle'] ?></td>
                                <td>
                                    <!-- Tombol Edit -->
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $row['id'] ?>">Edit</button>
                                    <a href="setting.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="edit<?= $row['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Hero Text</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Judul (Title)</label>
                                                    <input type="text" name="title" class="form-control" value="<?= $row['title'] ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Sub Judul (Subtitle)</label>
                                                    <textarea name="subtitle" class="form-control" rows="2" required><?= $row['subtitle'] ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="update" class="btn btn-success">Simpan</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
