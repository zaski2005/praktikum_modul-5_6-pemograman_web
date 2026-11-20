<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Elektromedis Management System') ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light-subtle">

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar" id="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-heart-pulse-fill"></i>
        </div>
        <div class="brand-text">
            <span class="brand-title">Elektromedis</span>
            <span class="brand-subtitle">Management System</span>
        </div>
    </div>

    <!-- Nav -->
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="index.php">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activePage ?? '') === 'alat' ? 'active' : '' ?>" href="alat.php">
                    <i class="bi bi-clipboard2-pulse"></i>
                    <span>Data Alat Elektromedis</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2">
            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-person-fill text-white small"></i>
            </div>
            <div>
                <div class="text-white fw-semibold small">Administrator</div>
                <div class="text-white-50" style="font-size:0.7rem">SIMRS ELEKTROMEDIS</div>
            </div>
        </div>
    </div>
</div>

<!-- ===== MAIN WRAPPER ===== -->
<div class="main-wrapper" id="mainWrapper">

    <!-- ===== NAVBAR TOP ===== -->
    <header class="topbar" id="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-link p-0 text-secondary sidebar-toggle" onclick="toggleSidebar()" title="Toggle Sidebar">
                <i class="bi bi-list fs-4"></i>
            </button>
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                    <?php if (($activePage ?? '') !== 'dashboard'): ?>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($pageTitle ?? '') ?></li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle">
                <i class="bi bi-circle-fill" style="font-size:0.45rem; vertical-align:middle;"></i>
                Online
            </span>
            <div class="vr mx-1"></div>
            <div class="d-flex align-items-center gap-2">
                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                    <i class="bi bi-person-fill text-white small"></i>
                </div>
                <span class="text-secondary small d-none d-sm-inline fw-medium">Admin</span>
            </div>
        </div>
    </header>

    <!-- ===== PAGE CONTENT ===== -->
    <main class="page-content">
