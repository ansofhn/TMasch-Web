<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Laporan Waktu Resolusi';

$sql = "SELECT t.id, t.title, p.name AS prioritas, s.name AS status,
               COALESCE(ab.full_name,'-') AS ditugaskan,
               t.created_at, t.resolved_at,
               TIMESTAMPDIFF(HOUR, t.created_at, COALESCE(t.resolved_at, NOW())) AS jam_berjalan
        FROM tickets t
        LEFT JOIN priorities p ON t.priority_id = p.id
        JOIN statuses s ON t.status_id = s.id
        LEFT JOIN users ab ON t.assigned_to = ab.id
        ORDER BY t.id DESC";
$tickets = $conn->query($sql);

// Rata-rata waktu resolusi (hanya yang sudah Closed)
$avgRes = $conn->query(
    "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) avg_hours
     FROM tickets WHERE resolved_at IS NOT NULL"
)->fetch_assoc()['avg_hours'];
$avgRes = $avgRes !== null ? round($avgRes, 1) : 0;

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-start no-print">
    <div>
        <h2>Laporan Waktu Resolusi</h2>
        <p>Mengukur berapa lama tiket diproses dari dibuat sampai selesai.</p>
    </div>
    <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-printer me-1"></i> Cetak / PDF
    </button>
</div>

<div class="row g-3 mb-4 no-print">
    <div class="col-md-4">
        <div class="kpi-card" style="border-left-color:#10b981">
            <div class="kpi-value text-success"><?= $avgRes ?> jam</div>
            <div class="kpi-label">Rata-rata Waktu Resolusi</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <strong>T-Masch</strong> — Laporan Waktu Resolusi &nbsp;|&nbsp;
        <span class="text-muted">Dicetak: <?= date('d/m/Y H:i') ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>ID</th><th>Judul</th><th>Prioritas</th><th>Status</th>
                    <th>Ditugaskan</th><th>Tgl Buat</th><th>Tgl Selesai</th><th>Durasi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($t = $tickets->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $t['id'] ?></td>
                    <td><?= h($t['title']) ?></td>
                    <td><?= $t['prioritas'] ? priorityBadge($t['prioritas']) : '-' ?></td>
                    <td><?= statusBadge($t['status']) ?></td>
                    <td><?= h($t['ditugaskan']) ?></td>
                    <td><?= date('d/m/y H:i', strtotime($t['created_at'])) ?></td>
                    <td><?= $t['resolved_at'] ? date('d/m/y H:i', strtotime($t['resolved_at'])) : '-' ?></td>
                    <td>
                        <?php if ($t['status'] === 'Closed'): ?>
                            <span class="badge bg-success"><?= $t['jam_berjalan'] ?> jam</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark"><?= $t['jam_berjalan'] ?> jam (berjalan)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
