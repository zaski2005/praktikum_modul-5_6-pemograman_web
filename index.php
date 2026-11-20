<?php

/**
 * index.php — Dashboard
 * Elektromedis Management System
 */

session_start();
require_once 'config/database.php';

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';

// Ambil flash message dari session
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$pdo = getDB();

// --- Statistik ---
$totalAlat  = $pdo->query("SELECT COUNT(*) FROM alat")->fetchColumn();
$totalMerek = $pdo->query("SELECT COUNT(DISTINCT merek) FROM alat")->fetchColumn();
$totalLokasi = $pdo->query("SELECT COUNT(DISTINCT lokasi) FROM alat")->fetchColumn();

// --- Tabel data terbaru (10 terakhir) ---
$stmt = $pdo->query("SELECT * FROM alat ORDER BY id DESC LIMIT 10");
$latestAlat = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>
<?php if ($flash): ?>
    <div id="flashMessage"
        data-message="<?= htmlspecialchars($flash['message']) ?>"
        data-type="<?= htmlspecialchars($flash['type']) ?>"></div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h4>
            <p class="text-muted mb-0">Ringkasan data inventaris alat elektromedis</p>
            <p class="text-muted mb-0 small mt-1">
                <i class="bi bi-person-badge me-1"></i>
                <strong>Anshari Zaski</strong> &mdash; NIM 1202405046
            </p>
        </div>
        <a href="alat.php" class="btn btn-primary">
            <i class="bi bi-clipboard2-pulse me-1"></i> Kelola Data Alat
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <!-- Total Alat -->
    <div class="col-sm-6 col-xl-4">
        <a href="alat.php" class="stat-card text-decoration-none">
            <div class="stat-icon primary">
                <i class="bi bi-tools"></i>
            </div>
            <div>
                <div class="stat-value"><?= number_format($totalAlat) ?></div>
                <div class="stat-label">Total Alat Elektromedis</div>
            </div>
        </a>
    </div>

    <!-- Total Merek -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="bi bi-tags"></i>
            </div>
            <div>
                <div class="stat-value"><?= number_format($totalMerek) ?></div>
                <div class="stat-label">Jumlah Merek</div>
            </div>
        </div>
    </div>

    <!-- Total Lokasi -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="bi bi-geo-alt"></i>
            </div>
            <div>
                <div class="stat-value"><?= number_format($totalLokasi) ?></div>
                <div class="stat-label">Jumlah Lokasi</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Data Terbaru -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-primary"></i>
            <span>Data Alat Terbaru</span>
        </div>
        <a href="alat.php" class="btn btn-sm btn-outline-primary">
            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($latestAlat)): ?>
            <div class="empty-state">
                <i class="bi bi-inbox d-block"></i>
                <p>Belum ada data alat. <a href="alat.php">Tambah sekarang</a>.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Alat</th>
                            <th>Tahun</th>
                            <th>Merek</th>
                            <th>Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latestAlat as $i => $alat): ?>
                            <tr>
                                <td class="row-num"><?= $i + 1 ?></td>
                                <td class="fw-medium"><?= htmlspecialchars($alat['nama_alat']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($alat['tahun']) ?></span></td>
                                <td><?= htmlspecialchars($alat['merek']) ?></td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <i class="bi bi-geo-alt-fill me-1"></i><?= htmlspecialchars($alat['lokasi']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>