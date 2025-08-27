<!-- slidebar.php -->
<div class="d-flex flex-column">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="text-white m-0"><i class="bi bi-gear-fill"></i> Setting</h4>
        <a href="halaman_admin.php" class="text-white" style="text-decoration:none;">
            <i class="bi bi-x-lg"></i>
        </a>
    </div>
    <hr class="border-secondary">

    <a href="setting.php" class="nav-link text-white mb-2 <?php echo basename($_SERVER['PHP_SELF'])=='setting.php'?'bg-primary rounded':''; ?>">
        <i class="bi bi-sliders"></i> Hero Setting
    </a>

    <a href="user.php" class="nav-link text-white mb-2">
        <i class="bi bi-people-fill"></i> #
    </a>

    <a href="laporan.php" class="nav-link text-white mb-2">
        <i class="bi bi-file-earmark-bar-graph"></i> #
    </a>
</div>

<style>
    .nav-link {
        padding: 8px 12px;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .nav-link:hover {
        background: #0d6efd;
        color: #fff !important;
    }
</style>

