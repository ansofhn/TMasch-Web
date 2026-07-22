<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../config/db.php';

$user = currentUser();
$pageTitle = 'Daftar Tiket';

$search = trim($_GET['q'] ?? '');

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

if (!isAdmin()) {
    $sql .= "AND t.created_by = " . intval($user['id']) . " ";
}
if ($search !== '') {
    $esc = $conn->real_escape_string($search);
    $sql .= "AND (t.title LIKE '%$esc%' OR s.name LIKE '%$esc%' OR c.name LIKE '%$esc%') ";
}
$sql .= "ORDER BY t.id DESC";

$tickets = $conn->query($sql);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h2><?= isAdmin() ? 'Semua Tiket' : 'Tiket Saya' ?></h2>
        <p>Kelola dan pantau seluruh tiket yang diajukan.</p>
    </div>
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari tiket..."
                   value="<?= h($search) ?>" style="width:200px">
            <button class="btn btn-sm btn-outline-secondary">Cari</button>
        </form>
        <a href="create.php" class="btn btn-sm btn-success"><i class="bi bi-plus-circle me-1"></i> Buat Tiket</a>
    </div>
</div>

<div class="card">
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
                    <th>Ditugaskan</th>
                    <th>Tgl Buat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tickets->num_rows === 0): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada tiket ditemukan.</td></tr>
                <?php endif; ?>
                <?php while ($row = $tickets->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td><?= h($row['title']) ?></td>
                    <td><?= h($row['kategori']) ?></td>
                    <td><?= $row['prioritas'] ? priorityBadge($row['prioritas']) : '-' ?></td>
                    <td><?= statusBadge($row['status']) ?></td>
                    <td><?= h($row['dibuat_oleh']) ?></td>
                    <td><?= h($row['ditugaskan']) ?></td>
                    <td><?= date('d/m/y H:i', strtotime($row['created_at'])) ?></td>
                    <td>
                        <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
