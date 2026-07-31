<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../config/db.php';

$user = currentUser();
$pageTitle = 'Buat Tiket Baru';
$error = '';
$success = '';

// Ambil kategori & prioritas
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
$priorities = $conn->query("SELECT id, name FROM priorities ORDER BY id");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryId  = intval($_POST['category_id'] ?? 0);
    $priorityId  = intval($_POST['priority_id'] ?? 0);

    if ($title === '') {
        $error = 'Judul tiket tidak boleh kosong.';
    } elseif ($categoryId <= 0) {
        $error = 'Pilih kategori tiket.';
    } elseif ($priorityId <= 0) {
        $error = 'Pilih prioritas tiket.';
    } else {
        // status_id = 1 (Open) sebagai default
        $stmt = $conn->prepare(
            "INSERT INTO tickets (title, description, category_id, priority_id, status_id, created_by, created_at)
             VALUES (?, ?, ?, ?, 1, ?, NOW())"
        );
        $stmt->bind_param("ssiii", $title, $description, $categoryId, $priorityId, $user['id']);
        if ($stmt->execute()) {
            header("Location: list.php?created=1");
            exit;
        } else {
            $error = 'Gagal menyimpan tiket: ' . $conn->error;
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2 style="color:#1A3263">Buat Tiket Baru</h2>
    <p>Isi formulir di bawah untuk mengajukan tiket administrasi sekolah.</p>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= h($error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-4" style="max-width:600px">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Judul *</label>
                <input type="text" name="title" class="form-control" required
                    value="<?= h($_POST['title'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Kategori *</label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php $categories->data_seek(0);
                    while ($c = $categories->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= (isset($_POST['category_id']) && $_POST['category_id'] == $c['id']) ? 'selected' : '' ?>>
                            <?= h($c['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Prioritas *</label>
                <select name="priority_id" class="form-select" required>
                    <option value="">-- Pilih Prioritas --</option>
                    <?php $priorities->data_seek(0);
                    while ($p = $priorities->fetch_assoc()): ?>
                        <option value="<?= $p['id'] ?>"
                            <?= (isset($_POST['priority_id']) && $_POST['priority_id'] == $p['id']) ? 'selected' : '' ?>>
                            <?= h($p['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="description" class="form-control" rows="4"
                    placeholder="Jelaskan permasalahan secara detail..."><?= h($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="list.php" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn" style="background-color:#FAB95B; color:#1A3263; font-weight:bold;">Ajukan Tiket</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>