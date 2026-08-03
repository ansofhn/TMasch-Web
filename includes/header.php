<?php
$pageTitle = $pageTitle ?? 'T-Masch';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?> — T-Masch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Space Grotesk';
            font-weight: 700;
            font-size: 1.15rem;
            color: #ffffff;
        }

        .brand .dot {
            width: 10px;
            height: 10px;
            border-radius: 2px;
            background: #FAB95B;
        }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">

        <!-- ═══════════ SIDEBAR ═══════════ -->
        <nav class="sidebar d-flex flex-column flex-shrink-0 text-white">
            <div class="sidebar-header">
                <a href="/dashboard.php" class="d-flex align-items-center mb-1 text-white text-decoration-none px-3 pt-3">
                    <span class="fs-4 fw-bold brand"><span class="dot"></span>T-Masch</span>
                </a>
                <div class="px-3 pb-4 small text-white-50">Ticketing Management System</div>
                <hr class="text-white-50 mt-0 mb-0">
            </div>

            <ul class="nav nav-pills flex-column mb-auto px-2">
                <li class="nav-item mb-3">
                    <div class="sidebar-heading">MENU UTAMA</div>
                    <a href="/dashboard.php" class="nav-link sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a href="/tickets/list.php" class="nav-link sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'list.php' ? 'active' : '' ?>">
                        <i class="bi bi-ticket-perforated me-2"></i> Daftar Tiket
                    </a>
                    <a href="/tickets/create.php" class="nav-link sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'create.php' ? 'active' : '' ?>">
                        <i class="bi bi-plus-circle me-2"></i> Buat Tiket
                    </a>
                    <?php if (isGuru()): ?>
                <li class="nav-item mb-3">
                    <div class="sidebar-heading">TUGAS SAYA</div>
                    <a href="/tickets/update.php" class="nav-link sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'update.php' ? 'active' : '' ?>">
                        <i class="bi bi-arrow-repeat me-2"></i> Update Status
                    </a>
                </li>
            <?php endif; ?>
            </li>

            <?php if (isAdmin()): ?>
                <li class="nav-item mb-3">
                    <div class="sidebar-heading">ADMIN</div>
                    <a href="/tickets/assign.php" class="nav-link sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'assign.php' ? 'active' : '' ?>">
                        <i class="bi bi-person-check me-2"></i> Assign Tiket
                    </a>
                    <a href="/tickets/update.php" class="nav-link sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'update.php' ? 'active' : '' ?>">
                        <i class="bi bi-arrow-repeat me-2"></i> Update Status
                    </a>
                </li>

                <li class="nav-item mb-3">
                    <div class="sidebar-heading">MASTER DATA</div>
                    <a href="/master/users.php" class="nav-link sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
                        <i class="bi bi-people me-2"></i> Master User
                    </a>
                    <a href="/master/categories.php" class="nav-link sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>">
                        <i class="bi bi-tags me-2"></i> Kategori
                    </a>
                    <a href="/master/priorities.php" class="nav-link sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'priorities.php' ? 'active' : '' ?>">
                        <i class="bi bi-flag me-2"></i> Prioritas
                    </a>
                </li>

                <li class="nav-item mb-3">
                    <div class="sidebar-heading">LAPORAN</div>
                    <a href="/reports/summary.php" class="nav-link sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'summary.php' ? 'active' : '' ?>">
                        <i class="bi bi-file-earmark-text me-2"></i> Rangkuman Tiket
                    </a>
                    <a href="/reports/resolution.php" class="nav-link sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'resolution.php' ? 'active' : '' ?>">
                        <i class="bi bi-stopwatch me-2"></i> Waktu Resolusi
                    </a>
                    <a href="/reports/trend.php" class="nav-link sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'trend.php' ? 'active' : '' ?>">
                        <i class="bi bi-graph-up me-2"></i> Tren Tiket
                    </a>
                    <a href="/reports/activity.php" class="nav-link sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'activity.php' ? 'active' : '' ?>">
                        <i class="bi bi-person-lines-fill me-2"></i> Aktivitas User
                    </a>
                </li>
            <?php endif; ?>
            </ul>

            <hr class="text-white-50">
            <div class="px-3 pb-3">
                <div class="fw-semibold"><?= h($user['full_name']) ?></div>
                <div class="small text-white-50 mb-2"><?= h($user['role_name']) ?></div>
                <a href="/logout.php" class="btn btn-sm w-100" style="background-color:#FAB95B; color:#1A3263; font-weight:700">
                    Keluar
                </a>
            </div>
        </nav>

        <!-- ═══════════ MAIN CONTENT ═══════════ -->
        <div class="flex-grow-1 main-content">