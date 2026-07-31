<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../config/db.php';

$user = currentUser();
$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare(
    "SELECT t.*, c.name AS kategori, p.name AS prioritas,
            s.name AS status, cb.full_name AS dibuat_oleh,
            COALESCE(ab.full_name,'-') AS ditugaskan
     FROM tickets t
     JOIN categories c ON t.category_id = c.id
     LEFT JOIN priorities p ON t.priority_id = p.id
     JOIN statuses s ON t.status_id = s.id
     JOIN users cb ON t.created_by = cb.id
     LEFT JOIN users ab ON t.assigned_to = ab.id
     WHERE t.id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();

if (!$ticket) {
    header("Location: list.php");
    exit;
}

// Batasi akses: non-admin hanya bisa lihat tiket miliknya
if (!isAdmin() && $ticket['created_by'] != $user['id']) {
    header("Location: list.php?err=forbidden");
    exit;
}

$pageTitle = 'Detail Tiket #' . $ticket['id'];
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>Tiket #<?= $ticket['id'] ?></h2>
        <p><?= h($ticket['title']) ?></p>
    </div>
    <a href="list.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-body p-4">
                <div class="d-flex gap-2 mb-3">
                    <?= statusBadge($ticket['status']) ?>
                    <?= $ticket['prioritas'] ? priorityBadge($ticket['prioritas']) : '' ?>
                    <span class="badge bg-light text-dark border"><?= h($ticket['kategori']) ?></span>
                </div>

                <h5 class="fw-semibold">Deskripsi</h5>
                <p class="text-muted"><?= nl2br(h($ticket['description'] ?: '-')) ?></p>

                <?php if ($ticket['resolution_note']): ?>
                    <hr>
                    <h5 class="fw-semibold">Catatan Resolusi</h5>
                    <p class="text-muted"><?= nl2br(h($ticket['resolution_note'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3">Informasi Tiket</h6>
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Dibuat oleh</td>
                        <td class="fw-semibold"><?= h($ticket['dibuat_oleh']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Ditugaskan ke</td>
                        <td class="fw-semibold"><?= h($ticket['ditugaskan']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tgl dibuat</td>
                        <td><?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tgl selesai</td>
                        <td><?= $ticket['resolved_at'] ? date('d/m/Y H:i', strtotime($ticket['resolved_at'])) : '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>