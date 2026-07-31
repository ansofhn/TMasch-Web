<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Laporan Rangkuman Tiket';

$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT t.id, t.title, c.name AS kategori, p.name AS prioritas,
               s.name AS status, cb.full_name AS dibuat_oleh,
               COALESCE(ab.full_name,'-') AS ditugaskan, t.created_at
        FROM tickets t
        JOIN categories c ON t.category_id = c.id
        LEFT JOIN priorities p ON t.priority_id = p.id
        JOIN statuses s ON t.status_id = s.id
        JOIN users cb ON t.created_by = cb.id
        LEFT JOIN users ab ON t.assigned_to = ab.id
        WHERE 1=1 ";
if ($statusFilter !== '') {
    $esc = $conn->real_escape_string($statusFilter);
    $sql .= "AND s.name = '$esc' ";
}
$sql .= "ORDER BY t.id DESC";
$tickets = $conn->query($sql);

$totalOpen = $conn->query("SELECT COUNT(*) c FROM tickets t JOIN statuses s ON t.status_id=s.id WHERE s.name='Open'")->fetch_assoc()['c'];
$totalProgress = $conn->query("SELECT COUNT(*) c FROM tickets t JOIN statuses s ON t.status_id=s.id WHERE s.name='In Progress'")->fetch_assoc()['c'];
$totalClosed = $conn->query("SELECT COUNT(*) c FROM tickets t JOIN statuses s ON t.status_id=s.id WHERE s.name='Closed'")->fetch_assoc()['c'];
$totalAll = $totalOpen + $totalProgress + $totalClosed;

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2 no-print">
    <div>
        <h2 style="color:#1A3263">Laporan Rangkuman Tiket</h2>
        <p>Daftar semua tiket beserta status, kategori, dan prioritas.</p>
    </div>
    <div class="d-flex gap-2 align-items-start">
        <select onchange="window.location='?status='+this.value" class="form-select form-select-sm w-auto">
            <option value="">Semua Status</option>
            <option value="Open" <?= $statusFilter == 'Open' ? 'selected' : '' ?>>Open</option>
            <option value="In Progress" <?= $statusFilter == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
            <option value="Closed" <?= $statusFilter == 'Closed' ? 'selected' : '' ?>>Closed</option>
        </select>
        <button onclick="window.print()" class="btn btn-sm" style="background-color:#FAB95B; color:#1A3263; font-weight:bold;">
            <i class="bi bi-printer me-1"></i> Cetak / PDF
        </button>
    </div>
</div>

<div class="row g-3 mb-4 no-print">
    <div class="col-md-3">
        <div class="kpi-card" style="border-left-color:#1A3263">
            <div class="kpi-value" style="color:#1A3263"><?= $totalAll ?></div>
            <div class="kpi-label">Total Tiket</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card" style="border-left-color:#1A3263">
            <div class="kpi-value" style="color:#1A3263"><?= $totalOpen ?></div>
            <div class="kpi-label">Open</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card" style="border-left-color:#FAB95B">
            <div class="kpi-value" style="color:#FAB95B"><?= $totalProgress ?></div>
            <div class="kpi-label">In Progress</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card" style="border-left-color:#64748b">
            <div class="kpi-value text-secondary"><?= $totalClosed ?></div>
            <div class="kpi-label">Closed</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <strong>T-Masch</strong> — Laporan Rangkuman Tiket &nbsp;|&nbsp;
        <span class="text-muted">Dicetak: <?= date('d/m/Y H:i') ?></span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Dibuat Oleh</th>
                    <th>Ditugaskan</th>
                    <th>Tgl Buat</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($t = $tickets->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $t['id'] ?></td>
                        <td><?= h($t['title']) ?></td>
                        <td><?= h($t['kategori']) ?></td>
                        <td><?= $t['prioritas'] ? priorityBadge($t['prioritas']) : '-' ?></td>
                        <td><?= statusBadge($t['status']) ?></td>
                        <td><?= h($t['dibuat_oleh']) ?></td>
                        <td><?= h($t['ditugaskan']) ?></td>
                        <td><?= date('d/m/y H:i', strtotime($t['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>