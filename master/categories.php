<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Master Kategori';
$error = '';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM categories WHERE id=" . $id);
    header("Location: categories.php?deleted=1");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        $error = 'Nama kategori tidak boleh kosong.';
    } else {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE categories SET name=? WHERE id=?");
            $stmt->bind_param("si", $name, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $name);
        }
        if ($stmt->execute()) {
            header("Location: categories.php?saved=1");
            exit;
        } else {
            $error = 'Gagal menyimpan: ' . $conn->error;
        }
    }
}

$categories = $conn->query("SELECT * FROM categories ORDER BY id");

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2 style="color:#1A3263">Master Kategori</h2>
        <p>Kelola kategori tiket administrasi sekolah.</p>
    </div>
    <button class="btn btn-sm" style="background-color:#FAB95B; color:#1A3263; font-weight:bold;" data-bs-toggle="modal" data-bs-target="#catModal" onclick="resetForm()">
        Tambah Kategori
    </button>
</div>

<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Data berhasil disimpan.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Kategori berhasil dihapus.</div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead><tr><th style="width:80px">ID</th><th>Nama Kategori</th><th style="width:120px"></th></tr></thead>
            <tbody>
                <?php while ($c = $categories->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $c['id'] ?></td>
                    <td><?= h($c['name']) ?></td>
                    <td>
                        <button class="btn btn-sm" style="background-color:#1A3263; color:#fff; font-weight:bold;"
                            onclick='editRow(<?= json_encode($c) ?>)'
                            data-bs-toggle="modal" data-bs-target="#catModal">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="?delete=<?= $c['id'] ?>" class="btn btn-sm" style="background-color:#D9534F; color:#fff; font-weight:bold;"
                           onclick="return confirm('Yakin hapus kategori ini?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="catModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="f_id">
                    <label class="form-label fw-semibold">Nama Kategori *</label>
                    <input type="text" name="name" id="f_name" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" style="background-color:#1A3263; color:#fff; font-weight:bold;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('modalTitle').innerText = 'Tambah Kategori';
    document.getElementById('f_id').value = '';
    document.getElementById('f_name').value = '';
}
function editRow(row) {
    document.getElementById('modalTitle').innerText = 'Edit Kategori';
    document.getElementById('f_id').value = row.id;
    document.getElementById('f_name').value = row.name;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
