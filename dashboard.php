<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/config/db.php';

$user = currentUser();
$pageTitle = 'Dashboard';

// ── KPI counts ─────────────────────────────────────────────
function countByStatus($conn, $statusName, $userId = null) {
    if ($userId) {
        $stmt = $conn->prepare(
            "SELECT COUNT(*) c FROM tickets t JOIN statuses s ON t.status_id=s.id
             WHERE s.name=? AND t.created_by=?"
        );
        $stmt->bind_param("si", $statusName, $userId);
    } else {
        $stmt = $conn->prepare(
            "SELECT COUNT(*) c FROM tickets t JOIN statuses s ON t.status_id=s.id WHERE s.name=?"
        );
        $stmt->bind_param("s", $statusName);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['c'];
}

if (isAdmin()) {
    $openCount     = countByStatus($conn, 'Open');
    $progressCount = countByStatus($conn, 'In Progress');
    $closedCount   = countByStatus($conn, 'Closed');
} else {
    $openCount     = countByStatus($conn, 'Open', $user['id']);
    $progressCount = countByStatus($conn, 'In Progress', $user['id']);
    $closedCount   = countByStatus($conn, 'Closed', $user['id']);
}

// ── Recent tickets ───────────────────────────────────────────
$sql = "SELECT t.id, t.title, c.name AS kategori, p.name AS prioritas,
               s.name AS status, cb.full_name AS dibuat_oleh, t.created_at
        FROM tickets t
        JOIN categories c ON t.category_id = c.id
        LEFT JOIN priorities p ON t.priority_id = p.id
        JOIN statuses s ON t.status_id = s.id
        JOIN users cb ON t.created_by = cb.id ";
if (!isAdmin()) {
    $sql .= "WHERE t.created_by = " . intval($user['id']) . " ";
}
$sql .= "ORDER BY t.id DESC LIMIT 15";
$recentTickets = $conn->query($sql);

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h2>Dashboard</h2>
    <p>Selamat datang, <strong><?= h($user['full_name']) ?></strong> — <?= h($user['role_name']) ?></p>
</div>

<!-- ── KPI Cards ── -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-card" style="border-left-color:#3b82f6">
            <div class="kpi-value text-primary"><?= $openCount ?></div>
            <div class="kpi-label">Tiket Open</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card" style="border-left-color:#f59e0b">
            <div class="kpi-value" style="color:#f59e0b"><?= $progressCount ?></div>
            <div class="kpi-label">Sedang Diproses</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card" style="border-left-color:#10b981">
            <div class="kpi-value text-success"><?= $closedCount ?></div>
            <div class="kpi-label">Closed</div>
        </div>
    </div>
</div>

<!-- ── Recent tickets ── -->
<div class="card">
    <div class="card-header bg-white fw-semibold py-3">
        <?= isAdmin() ? 'Tiket Terbaru' : 'Tiket Saya' ?>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Dibuat Oleh</th>
                    <th>Tgl Buat</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recentTickets->num_rows === 0): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada tiket.</td></tr>
                <?php endif; ?>
                <?php while ($row = $recentTickets->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td><?= h($row['title']) ?></td>
                    <td><?= h($row['kategori']) ?></td>
                    <td><?= $row['prioritas'] ? priorityBadge($row['prioritas']) : '-' ?></td>
                    <td><?= statusBadge($row['status']) ?></td>
                    <td><?= h($row['dibuat_oleh']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
