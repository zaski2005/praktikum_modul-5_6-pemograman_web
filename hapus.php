<?php
/**
 * hapus.php — Proses Hapus Alat
 * Elektromedis Management System
 */

session_start();
require_once 'config/database.php';

// Hanya terima request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: alat.php'); exit;
}

// CSRF check
if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Token keamanan tidak valid. Aksi dibatalkan.'];
    header('Location: alat.php'); exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'ID alat tidak valid.'];
    header('Location: alat.php'); exit;
}

$pdo = getDB();

try {
    // Cek dulu apakah data ada
    $check = $pdo->prepare("SELECT nama_alat FROM alat WHERE id = ?");
    $check->execute([$id]);
    $alat = $check->fetch();

    if (!$alat) {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Data alat tidak ditemukan.'];
        header('Location: alat.php'); exit;
    }

    $stmt = $pdo->prepare("DELETE FROM alat WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['flash'] = [
        'type'    => 'success',
        'message' => 'Alat <strong>' . htmlspecialchars($alat['nama_alat']) . '</strong> berhasil dihapus.'
    ];
} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Gagal menghapus data. Silakan coba lagi.'];
}

header('Location: alat.php'); exit;
