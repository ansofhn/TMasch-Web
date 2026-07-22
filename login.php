<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

if (isLoggedIn()) {
    header("Location: /dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username dan password tidak boleh kosong.';
    } else {
        $stmt = $conn->prepare(
            "SELECT u.id, u.username, u.full_name, u.department, u.role_id, r.name AS role_name
             FROM users u JOIN roles r ON u.role_id = r.id
             WHERE u.username = ? AND u.password = ?"
        );
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $_SESSION['user_id']    = $row['id'];
            $_SESSION['username']   = $row['username'];
            $_SESSION['full_name']  = $row['full_name'];
            $_SESSION['department'] = $row['department'];
            $_SESSION['role_id']    = $row['role_id'];
            $_SESSION['role_name']  = $row['role_name'];
            header("Location: /dashboard.php");
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — T-Masch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            background: #0f172a;
        }
        .brand-panel {
            background: linear-gradient(160deg, #1d4ed8, #1e3a8a);
            color: #fff;
            width: 42%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
        }
        .brand-panel h1 { font-weight: 800; font-size: 2.6rem; }
        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
        }
        .login-card {
            width: 100%;
            max-width: 380px;
        }
        .login-card label { color: #cbd5e1; font-weight: 600; font-size: 0.85rem; }
        .login-card h2 { color: #fff; font-weight: 700; }
        .login-card p.sub { color: #94a3b8; }
        .form-control {
            background: #1e293b;
            border: 1px solid #334155;
            color: #f1f5f9;
        }
        .form-control:focus {
            background: #1e293b;
            border-color: #2563eb;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(37,99,235,.25);
        }
        .btn-primary { background: #2563eb; border: none; }
        .default-hint { color: #64748b; font-size: 0.8rem; }
        @media (max-width: 768px) { .brand-panel { display: none; } }
    </style>
</head>
<body>

    <div class="brand-panel">
        <h1>T-Masch</h1>
        <p class="fs-6">Ticketing Management System<br>Administration School</p>
        <hr class="border-light opacity-25 my-4">
    </div>

    <div class="form-panel">
        <div class="login-card">
            <h2>Selamat Datang</h2>
            <p class="sub mb-4">Masuk ke sistem administrasi sekolah</p>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= h($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 mt-2">Masuk</button>
            </form>
        </div>
    </div>

</body>
</html>
