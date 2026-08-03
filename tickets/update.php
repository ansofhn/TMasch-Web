<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Update Status Tiket';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticketId = intval($_POST['ticket_id'] ?? 0);
    $note     = trim($_POST['resolution_note'] ?? '');

    if (!isAdmin() && !isTicketAssignedToMe($conn, $ticketId, currentUser()['id'])) {
        $error = 'Anda tidak berhak menyelesaikan tiket ini.';
    } elseif ($ticketId <= 0) {
        $error = 'Pilih tiket terlebih dahulu.';
    } elseif ($note === '') {
        $error = 'Catatan resolusi wajib diisi.';
    } else {
        // status_id = 3 (Closed)
        $stmt = $conn->prepare(
            "UPDATE tickets SET status_id=3, resolution_note=?, resolved_at=NOW() WHERE id=?"
        );
        $stmt->bind_param("si", $note, $ticketId);
        $stmt->execute();
        header("Location: update.php?closed=1");
        exit;
    }
}

// Tiket yang sedang In Progress (siap ditutup)
$user = currentUser();
$sqlActive =
    "SELECT t.id, t.title, p.name AS prioritas, s.name AS status,
            COALESCE(ab.full_name,'-') AS ditugaskan
     FROM tickets t
     JOIN statuses s ON t.status_id = s.id
     LEFT JOIN priorities p ON t.priority_id = p.id
     LEFT JOIN users ab ON t.assigned_to = ab.id
     WHERE s.name = 'In Progress' ";
if (!isAdmin()) {
    $sqlActive .= "AND t.assigned_to = " . intval($user['id']) . " ";
}
$sqlActive .= "ORDER BY t.id DESC";
$activeTickets = $conn->query($sqlActive);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2 style="color:#1A3263">Update Status Tiket</h2>
    <p>Selesaikan tiket yang sedang diproses (In Progress → Closed).</p>
</div>

<?php if (isset($_GET['closed'])): ?>
    <div class="alert alert-success">Tiket berhasil diselesaikan dan ditutup.</div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= h($error) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Tiket Sedang Diproses</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Prioritas</th>
                            <th>Ditugaskan</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($activeTickets->num_rows === 0): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Tidak ada tiket yang sedang diproses.</td>
                            </tr>
                        <?php endif; ?>
                        <?php while ($t = $activeTickets->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $t['id'] ?></td>
                                <td><?= h($t['title']) ?></td>
                                <td><?= $t['prioritas'] ? priorityBadge($t['prioritas']) : '-' ?></td>
                                <td><?= h($t['ditugaskan']) ?></td>
                                <td>
                                    <button class="btn btn-sm" style="background-color:#249D8F; color:#fff; font-weight:bold;"
                                        onclick="pilihTiket(<?= $t['id'] ?>, '<?= h(addslashes($t['title'])) ?>')">
                                        Selesaikan
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
            <div class="card-header bg-white fw-semibold">Form Penyelesaian</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Tiket Terpilih</label>
                        <input type="text" id="ticketLabel" class="form-control" readonly placeholder="Klik 'Selesaikan' pada tabel...">
                        <input type="hidden" name="ticket_id" id="ticket_id">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan Resolusi *</label>
                        <textarea name="resolution_note" class="form-control" rows="5" required
                            placeholder="Jelaskan bagaimana tiket ini diselesaikan..."></textarea>
                    </div>

                    <button type="submit" class="btn w-100" style="background-color:#249D8F; color:#fff; font-weight:bold;">
                        Tutup Tiket (Closed)
                    </button>
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