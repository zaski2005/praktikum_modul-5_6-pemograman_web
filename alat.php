<?php
/**
 * alat.php — Halaman Data Alat Elektromedis (CRUD + Search)
 * Elektromedis Management System
 */

session_start();
require_once 'config/database.php';

$pageTitle  = 'Data Alat Elektromedis';
$activePage = 'alat';

// Ambil flash message dari session
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$pdo = getDB();

// ---- CSRF Token ----
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

// ---- Handle POST: Tambah Alat ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    // CSRF check
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Token keamanan tidak valid. Coba lagi.'];
        header('Location: alat.php'); exit;
    }

    $nama_alat = trim($_POST['nama_alat'] ?? '');
    $tahun     = trim($_POST['tahun'] ?? '');
    $merek     = trim($_POST['merek'] ?? '');
    $lokasi    = trim($_POST['lokasi'] ?? '');

    $errors = [];
    if ($nama_alat === '') $errors[] = 'Nama alat wajib diisi.';
    if ($tahun === '' || !preg_match('/^\d{4}$/', $tahun) || (int)$tahun < 1900 || (int)$tahun > (int)date('Y') + 1)
        $errors[] = 'Tahun harus berupa angka 4 digit yang valid.';
    if ($merek === '') $errors[] = 'Merek wajib diisi.';
    if ($lokasi === '') $errors[] = 'Lokasi wajib diisi.';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO alat (nama_alat, tahun, merek, lokasi) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama_alat, $tahun, $merek, $lokasi]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Alat <strong>' . htmlspecialchars($nama_alat) . '</strong> berhasil ditambahkan.'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menambahkan data. Silakan coba lagi.'];
        }
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => implode(' ', $errors)];
    }
    header('Location: alat.php'); exit;
}

// ---- Handle POST: Edit Alat ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Token keamanan tidak valid. Coba lagi.'];
        header('Location: alat.php'); exit;
    }

    $id        = (int)($_POST['id'] ?? 0);
    $nama_alat = trim($_POST['nama_alat'] ?? '');
    $tahun     = trim($_POST['tahun'] ?? '');
    $merek     = trim($_POST['merek'] ?? '');
    $lokasi    = trim($_POST['lokasi'] ?? '');

    $errors = [];
    if ($id <= 0)      $errors[] = 'ID tidak valid.';
    if ($nama_alat === '') $errors[] = 'Nama alat wajib diisi.';
    if ($tahun === '' || !preg_match('/^\d{4}$/', $tahun) || (int)$tahun < 1900 || (int)$tahun > (int)date('Y') + 1)
        $errors[] = 'Tahun harus berupa angka 4 digit yang valid.';
    if ($merek === '') $errors[] = 'Merek wajib diisi.';
    if ($lokasi === '') $errors[] = 'Lokasi wajib diisi.';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE alat SET nama_alat=?, tahun=?, merek=?, lokasi=? WHERE id=?");
            $stmt->execute([$nama_alat, $tahun, $merek, $lokasi, $id]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data alat berhasil diperbarui.'];
        } catch (PDOException $e) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal memperbarui data. Silakan coba lagi.'];
        }
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => implode(' ', $errors)];
    }
    header('Location: alat.php'); exit;
}

// ---- Search & Fetch Data ----
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt = $pdo->prepare("SELECT * FROM alat WHERE nama_alat LIKE ? OR tahun LIKE ? OR merek LIKE ? OR lokasi LIKE ? ORDER BY id DESC");
    $stmt->execute([$like, $like, $like, $like]);
} else {
    $stmt = $pdo->query("SELECT * FROM alat ORDER BY id DESC");
}
$dataAlat = $stmt->fetchAll();

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
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4><i class="bi bi-clipboard2-pulse me-2 text-primary"></i>Data Alat Elektromedis</h4>
            <p class="text-muted mb-0">Kelola inventaris seluruh alat elektromedis</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Alat
        </button>
    </div>
</div>

<!-- Tabel Card -->
<div class="card mb-4">
    <div class="card-header">
        <div class="row align-items-center g-2">
            <div class="col-sm-6">
                <span class="fw-semibold">
                    <i class="bi bi-table me-1 text-primary"></i>Daftar Alat
                    <span class="badge bg-primary ms-1"><?= count($dataAlat) ?></span>
                </span>
            </div>
            <div class="col-sm-6">
                <form method="GET" action="alat.php" class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text"
                           name="q"
                           id="searchInput"
                           class="form-control"
                           placeholder="Cari nama, tahun, merek, lokasi..."
                           value="<?= htmlspecialchars($search) ?>"
                           autocomplete="off">
                </form>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <?php if (empty($dataAlat)): ?>
        <div class="empty-state">
            <i class="bi bi-clipboard-x d-block"></i>
            <p>
                <?= $search ? 'Tidak ditemukan data untuk pencarian "<strong>' . htmlspecialchars($search) . '</strong>".' : 'Belum ada data alat. Klik <strong>Tambah Alat</strong> untuk memulai.' ?>
            </p>
            <?php if ($search): ?>
            <a href="alat.php" class="btn btn-sm btn-outline-secondary mt-2">
                <i class="bi bi-arrow-left me-1"></i>Lihat Semua
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">No.</th>
                        <th>Nama Alat</th>
                        <th>Tahun</th>
                        <th>Merek</th>
                        <th>Lokasi</th>
                        <th style="width:130px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataAlat as $i => $alat): ?>
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
                        <td class="text-center">
                            <button type="button"
                                    class="btn-action edit"
                                    onclick='openEditModal(<?= json_encode([
                                        "id"        => $alat["id"],
                                        "nama_alat" => $alat["nama_alat"],
                                        "tahun"     => $alat["tahun"],
                                        "merek"     => $alat["merek"],
                                        "lokasi"    => $alat["lokasi"],
                                    ]) ?>)'>
                                <i class="bi bi-pencil-fill"></i> Edit
                            </button>
                            <button type="button"
                                    class="btn-action hapus"
                                    onclick="confirmDelete(<?= (int)$alat['id'] ?>, '<?= addslashes(htmlspecialchars($alat['nama_alat'])) ?>')">
                                <i class="bi bi-trash3-fill"></i> Hapus
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ==========================================
     MODAL: TAMBAH ALAT
     ========================================== -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="alat.php" class="needs-validation" novalidate>
                <input type="hidden" name="action" value="tambah">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="tambahModalLabel">
                        <i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah Alat Elektromedis
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="tambah_nama_alat" class="form-label">Nama Alat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tambah_nama_alat" name="nama_alat"
                                   placeholder="Contoh: Sphygmomanometer Digital" required maxlength="100">
                            <div class="invalid-feedback">Nama alat wajib diisi.</div>
                        </div>
                        <div class="col-sm-6">
                            <label for="tambah_tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="tambah_tahun" name="tahun"
                                   placeholder="Contoh: 2023" min="1900" max="<?= date('Y') + 1 ?>" required>
                            <div class="invalid-feedback">Tahun harus valid (4 digit).</div>
                        </div>
                        <div class="col-sm-6">
                            <label for="tambah_merek" class="form-label">Merek <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tambah_merek" name="merek"
                                   placeholder="Contoh: Omron" required maxlength="60">
                            <div class="invalid-feedback">Merek wajib diisi.</div>
                        </div>
                        <div class="col-12">
                            <label for="tambah_lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tambah_lokasi" name="lokasi"
                                   placeholder="Contoh: ICU, Poli Jantung" required maxlength="10">
                            <div class="invalid-feedback">Lokasi wajib diisi.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy-fill me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================
     MODAL: EDIT ALAT
     ========================================== -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="alat.php" class="needs-validation" novalidate id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="id" id="edit_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">
                        <i class="bi bi-pencil-fill text-warning me-2"></i>Edit Alat Elektromedis
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="edit_nama_alat" class="form-label">Nama Alat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_alat" name="nama_alat"
                                   required maxlength="100">
                            <div class="invalid-feedback">Nama alat wajib diisi.</div>
                        </div>
                        <div class="col-sm-6">
                            <label for="edit_tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_tahun" name="tahun"
                                   min="1900" max="<?= date('Y') + 1 ?>" required>
                            <div class="invalid-feedback">Tahun harus valid (4 digit).</div>
                        </div>
                        <div class="col-sm-6">
                            <label for="edit_merek" class="form-label">Merek <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_merek" name="merek"
                                   required maxlength="60">
                            <div class="invalid-feedback">Merek wajib diisi.</div>
                        </div>
                        <div class="col-12">
                            <label for="edit_lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_lokasi" name="lokasi"
                                   required maxlength="10">
                            <div class="invalid-feedback">Lokasi wajib diisi.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="bi bi-floppy-fill me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================
     MODAL: KONFIRMASI HAPUS
     ========================================== -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center pt-0 pb-2">
                <div class="mb-3">
                    <div style="width:60px;height:60px;background:#fce8e8;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                        <i class="bi bi-trash3-fill text-danger fs-3"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Hapus Data?</h5>
                <p class="text-muted small mb-0">
                    Anda akan menghapus alat:<br>
                    <strong id="deleteNama" class="text-dark"></strong><br>
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" action="hapus.php" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash3-fill me-1"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Override confirmDelete to pass id via hidden input (more secure than URL for POST)
function confirmDelete(id, nama) {
    document.getElementById('deleteNama').textContent = nama;
    // Set id as query param (GET to hapus.php)
    document.getElementById('deleteForm').action = 'hapus.php?id=' + encodeURIComponent(id);
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

<?php include 'includes/footer.php'; ?>
