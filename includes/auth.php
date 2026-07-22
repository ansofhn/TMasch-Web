<?php

session_start();

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: /login.php");
        exit;
    }
}

function requireAdmin()
{
    requireLogin();
    if ($_SESSION['role_name'] !== 'Admin') {
        header("Location: /dashboard.php?err=forbidden");
        exit;
    }
}

function currentUser()
{
    return [
        'id'         => $_SESSION['user_id']   ?? null,
        'username'   => $_SESSION['username']  ?? null,
        'full_name'  => $_SESSION['full_name']  ?? null,
        'role_id'    => $_SESSION['role_id']    ?? null,
        'role_name'  => $_SESSION['role_name']  ?? null,
        'department' => $_SESSION['department'] ?? null,
    ];
}

function isAdmin()
{
    return ($_SESSION['role_name'] ?? '') === 'Admin';
}
function isGuru()
{
    return ($_SESSION['role_name'] ?? '') === 'Guru';
}
function isSiswa()
{
    return ($_SESSION['role_name'] ?? '') === 'Siswa';
}

/** Escape helper */
function h($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/** Badge warna untuk status */
function statusBadge($statusName)
{
    $map = [
        'Open'        => 'primary',
        'In Progress' => 'warning',
        'Closed'      => 'secondary',
    ];
    $color = $map[$statusName] ?? 'secondary';
    return '<span class="badge bg-' . $color . '">' . h($statusName) . '</span>';
}

/** Badge warna untuk prioritas */
function priorityBadge($priorityName)
{
    $map = [
        'LOW'      => 'success',
        'MEDIUM'   => 'warning',
        'HIGH'     => 'orange',
        'CRITICAL' => 'danger',
    ];
    $color = $map[$priorityName] ?? 'secondary';
    if ($color === 'orange') {
        return '<span class="badge" style="background:#ea580c">' . h($priorityName) . '</span>';
    }
    return '<span class="badge bg-' . $color . '">' . h($priorityName) . '</span>';
}
