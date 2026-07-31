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
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        h1,h2,h3,.display{font-family:'Space Grotesk',sans-serif;}
        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1A3263;
            font-family: 'Space Grotesk',sans-serif;;
        }
        .login-shell {
            width: 94%;
            max-width: 1300px;
            min-height: 88vh;
            background: #E8E2DB;
            border-radius: 24px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,.06);
        }
        .form-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 48px 64px;
        }
        .topnav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .topnav .brand {
            font-weight: 800;
            letter-spacing: .02em;
            color: #1e293b;
            font-size: 1.1rem;
        }
        .topnav .links a {
            color: #94a3b8;
            text-decoration: none;
            margin-left: 32px;
            font-weight: 500;
        }
        .login-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 420px;
        }
        .eyebrow {
            color: #94a3b8;
            font-weight: 700;
            font-size: .78rem;
            letter-spacing: .12em;
            margin-bottom: 6px;
        }
        .login-content h2 {
            color: #1e293b;
            font-weight: 800;
            font-size: 2.2rem;
            margin-bottom: 14px;
        }
        .login-content .sub {
            color: #94a3b8;
            margin-bottom: 32px;
        }
        .login-content .sub a {
            color: #1e293b;
            font-weight: 700;
            text-decoration: none;
        }
        .form-control {
            background: #eef0f1;
            border: none;
            color: #334155;
            padding: 14px 18px;
            border-radius: 10px;
            font-size: .95rem;
        }
        .form-control::placeholder { color: #94a3b8; }
        .form-control:focus {
            background: #e7e9eb;
            color: #1e293b;
            box-shadow: 0 0 0 3px rgba(30,41,59,.1);
        }
        .btn-signin {
            background: #1A3263;
            border: none;
            color: #fff;
            font-weight: 700;
            padding: 14px;
            border-radius: 10px;
        }
        .btn-signin:hover { background: #1A3263; color: #fff; }
        .login-side {
            flex: 1;
            padding: 16px 16px 16px 0;
            cursor: default;
        }
        .placeholder {
            background: #FAB95B;
            width: 100%;
            height: 100%;
            border-radius: 24px;
            display: flex;
            overflow: hidden;
            opacity: 100%;
            text-align: center;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 2rem;
            color: #1A3263;
            padding: 40px 40px 40px 40px;
            cursor: default;
        }
        .child {
            width: 100%;
            height: 100%;
            background: #1A3263;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 24px;
            cursor: default;
        }
        @media (max-width: 900px) {
            .form-side { padding: 40px; }
            .login-side { display: none; }
        }
    </style>
</head>
<body>

    <div class="login-shell">
        <div class="form-side">
            <div class="topnav">
                <span class="brand">T-Masch</span>
                <nav class="links d-none d-md-block">
                    <a href="/landing.php">Halaman Utama</a>
                </nav>
            </div>

            <div class="login-content">
                <div class="eyebrow">WELCOME BACK</div>
                <h2>Login to your account.</h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger py-2"><?= h($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Email" required autofocus>
                    </div>
                    <div class="mb-4">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-signin w-100">Sign In</button>
                </form>
            </div>
        </div>

        <div class="login-side">
             <div class="placeholder">
                <div class="child"></div>
             </div>
        </div>
    </div>

</body>
</html>