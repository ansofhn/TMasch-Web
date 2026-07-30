<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Assign Tiket';
$error = '';

// Proses assign
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticketId = intval($_POST['ticket_id'] ?? 0);
    $staffId  = intval($_POST['staff_id'] ?? 0);
    $priorityId = intval($_POST['priority_id'] ?? 0);

    if ($ticketId <= 0 || $staffId <= 0) {
        $error = 'Pilih tiket dan staff terlebih dahulu.';
    } else {
        // status_id = 2 (In Progress) setelah di-assign
        $stmt = $conn->prepare(
            "UPDATE tickets SET assigned_to=?, priority_id=?, status_id=2 WHERE id=?"
        );
        $stmt->bind_param("iii", $staffId, $priorityId, $ticketId);
        $stmt->execute();
        header("Location: assign.php?assigned=1");
        exit;
    }
}

// Tiket yang belum di-assign (status Open)
$openTickets = $conn->query(
    "SELECT t.id, t.title, p.name AS prioritas, s.name AS status, cb.full_name AS dibuat_oleh
     FROM tickets t
     JOIN statuses s ON t.status_id = s.id
     LEFT JOIN priorities p ON t.priority_id = p.id
     JOIN users cb ON t.created_by = cb.id
     WHERE s.name = 'Open'
     ORDER BY t.id DESC"
);

// Staff (role Guru & Admin)
$staffList = $conn->query(
    "SELECT u.id, u.full_name, r.name AS role_name
     FROM users u JOIN roles r ON u.role_id = r.id
     WHERE r.name IN ('Guru','Admin')
     ORDER BY u.full_name"
);

$priorities = $conn->query("SELECT id, name FROM priorities ORDER BY id");

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2>Assign Tiket</h2>
    <p>Tugaskan tiket yang berstatus Open ke Guru / Staff.</p>
</div>

<?php if (isset($_GET['assigned'])): ?>
    <div class="alert alert-success">Tiket berhasil ditugaskan.</div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= h($error) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Tiket Belum Ditugaskan (Open)</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr><th>ID</th><th>Judul</th><th>Prioritas</th><th>Dibuat Oleh</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php if ($openTickets->num_rows === 0): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada tiket yang perlu ditugaskan.</td></tr>
                        <?php endif; ?>
                        <?php while ($t = $openTickets->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $t['id'] ?></td>
                            <td><?= h($t['title']) ?></td>
                            <td><?= $t['prioritas'] ? priorityBadge($t['prioritas']) : '-' ?></td>
                            <td><?= h($t['dibuat_oleh']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary"
                                    onclick="pilihTiket(<?= $t['id'] ?>, '<?= h(addslashes($t['title'])) ?>')">
                                    Pilih
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Form Penugasan</div>
            <div class="card-body">
                <form method="POST" id="formAssign">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Tiket Terpilih</label>
                        <input type="text" id="ticketLabel" class="form-control" readonly placeholder="Klik 'Pilih' pada tabel...">
                        <input type="hidden" name="ticket_id" id="ticket_id">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tugaskan ke *</label>
                        <select name="staff_id" class="form-select" required>
                            <option value="">-- Pilih Staff --</option>
                            <?php while ($s = $staffList->fetch_assoc()): ?>
                                <option value="<?= $s['id'] ?>"><?= h($s['full_name']) ?> [<?= h($s['role_name']) ?>]</option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Set Ulang Prioritas *</label>
                        <select name="priority_id" class="form-select" required>
                            <option value="">-- Pilih Prioritas --</option>
                            <?php while ($p = $priorities->fetch_assoc()): ?>
                                <option value="<?= $p['id'] ?>"><?= h($p['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Tugaskan Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function pilihTiket(id, title) {
    document.getElementById('ticket_id').value = id;
    document.getElementById('ticketLabel').value = '#' + id + ' — ' + title;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
