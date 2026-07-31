<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
require_once __DIR__ . '/../config/db.php';

$pageTitle = 'Master User';
$error = '';

// ── DELETE ───────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id=" . $id);
    header("Location: users.php?deleted=1");
    exit;
}

// ── CREATE / UPDATE ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = intval($_POST['id'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $roleId   = intval($_POST['role_id'] ?? 0);
    $dept     = trim($_POST['department'] ?? '');

    if ($username === '' || $fullName === '' || $roleId <= 0) {
        $error = 'Username, nama lengkap, dan role wajib diisi.';
    } elseif ($id === 0 && $password === '') {
        $error = 'Password wajib diisi untuk user baru.';
    } else {
        if ($id > 0) {
            if ($password !== '') {
                $stmt = $conn->prepare(
                    "UPDATE users SET username=?, password=?, full_name=?, role_id=?, department=? WHERE id=?"
                );
                $stmt->bind_param("sssisi", $username, $password, $fullName, $roleId, $dept, $id);
            } else {
                $stmt = $conn->prepare(
                    "UPDATE users SET username=?, full_name=?, role_id=?, department=? WHERE id=?"
                );
                $stmt->bind_param("ssisi", $username, $fullName, $roleId, $dept, $id);
            }
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO users (username, password, full_name, role_id, department) VALUES (?,?,?,?,?)"
            );
            $stmt->bind_param("sssis", $username, $password, $fullName, $roleId, $dept);
        }
        if ($stmt->execute()) {
            header("Location: users.php?saved=1");
            exit;
        } else {
            $error = 'Gagal menyimpan: ' . $conn->error;
        }
    }
}

$roles = $conn->query("SELECT id, name FROM roles ORDER BY id");
$users = $conn->query(
    "SELECT u.id, u.username, u.full_name, u.department, r.name AS role_name
     FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.full_name"
);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2 style="color:#1A3263">Master User</h2>
        <p>Kelola akun Admin, Guru, dan Siswa.</p>
    </div>
    <button class="btn btn-sm" style="background-color:#FAB95B; color:#1A3263; font-weight:bold;" data-bs-toggle="modal" data-bs-target="#userModal"
        onclick="resetForm()">
        Tambah User
    </button>
</div>

<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Data berhasil disimpan.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">User berhasil dihapus.</div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Dept/Kelas</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $u['id'] ?></td>
                        <td><?= h($u['full_name']) ?></td>
                        <td><?= h($u['username']) ?></td>
                        <td><span class="badge bg-secondary"><?= h($u['role_name']) ?></span></td>
                        <td><?= h($u['department']) ?></td>
                        <td>
                            <button class="btn btn-sm" style="background-color:#1A3263; color:#fff; font-weight:bold;"
                                onclick='editUser(<?= json_encode($u) ?>)'
                                data-bs-toggle="modal" data-bs-target="#userModal">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm" style="background-color:#D9534F; color:#fff; font-weight:bold;"
                                onclick="return confirm('Yakin ingin menghapus user ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ── Modal Form ── -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="f_id">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap *</label>
                        <input type="text" name="full_name" id="f_full_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username *</label>
                        <input type="text" name="username" id="f_username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password <span id="pwHint" class="text-muted fw-normal">*</span></label>
                        <input type="text" name="password" id="f_password" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role *</label>
                        <select name="role_id" id="f_role_id" class="form-select" required>
                            <?php $roles->data_seek(0);
                            while ($r = $roles->fetch_assoc()): ?>
                                <option value="<?= $r['id'] ?>"><?= h($r['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dept / Kelas</label>
                        <input type="text" name="department" id="f_department" class="form-control">
                    </div>
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
        document.getElementById('modalTitle').innerText = 'Tambah User';
        document.getElementById('f_id').value = '';
        document.getElementById('f_full_name').value = '';
        document.getElementById('f_username').value = '';
        document.getElementById('f_password').value = '';
        document.getElementById('f_department').value = '';
        document.getElementById('pwHint').innerText = '*';
    }

    function editUser(u) {
        document.getElementById('modalTitle').innerText = 'Edit User';
        document.getElementById('f_id').value = u.id;
        document.getElementById('f_full_name').value = u.full_name;
        document.getElementById('f_username').value = u.username;
        document.getElementById('f_password').value = '';
        document.getElementById('f_department').value = u.department || '';
        document.getElementById('pwHint').innerText = '(kosongkan jika tidak diubah)';
    }
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>