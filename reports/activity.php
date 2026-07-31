<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Laporan Aktivitas User';

$sql = "SELECT u.id, u.full_name, r.name AS role_name, u.department,
               (SELECT COUNT(*) FROM tickets t WHERE t.created_by = u.id) AS tiket_dibuat,
               (SELECT COUNT(*) FROM tickets t JOIN statuses s ON t.status_id=s.id
                WHERE t.created_by = u.id AND s.name = 'Closed') AS tiket_selesai,
               (SELECT COUNT(*) FROM tickets t JOIN statuses s ON t.status_id=s.id
                WHERE t.created_by = u.id AND s.name != 'Closed') AS tiket_aktif
        FROM users u
        JOIN roles r ON u.role_id = r.id
        ORDER BY tiket_dibuat DESC";
$users = $conn->query($sql);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-start no-print">
    <div>
        <h2 style="color:#1A3263">Laporan Aktivitas User</h2>
        <p>Ringkasan tiket yang dibuat dan diselesaikan per user.</p>
    </div>
    <button onclick="window.print()" class="btn btn-sm" style="background-color:#FAB95B; color:#1A3263; font-weight:bold;">
        <i class="bi bi-printer me-1"></i> Cetak / PDF
    </button>
</div>

<div class="card">
    <div class="card-header bg-white">
        <strong>T-Masch</strong> — Laporan Aktivitas User &nbsp;|&nbsp;
        <span class="text-muted">Dicetak: <?= date('d/m/Y H:i') ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nama User</th><th>Role</th><th>Dept/Kelas</th>
                    <th>Tiket Dibuat</th><th>Tiket Selesai</th><th>Tiket Aktif</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td class="fw-semibold"><?= h($u['full_name']) ?></td>
                    <td><span class="badge bg-secondary"><?= h($u['role_name']) ?></span></td>
                    <td><?= h($u['department']) ?></td>
                    <td><?= $u['tiket_dibuat'] ?></td>
                    <td><span style="color:#249D8F"><?= $u['tiket_selesai'] ?></span></td>
                    <td><span style="color:#1A3263"><?= $u['tiket_aktif'] ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
