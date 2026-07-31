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
        'Open'        => '#1A3263',
        'In Progress' => '#FAB95B',
        'Closed'      => '#64748b',
    ];
    $color = $map[$statusName] ?? '#64748b';
    return '<span class="badge" style="background-color:' . $color . ';">' . h($statusName) . '</span>';
}

/** Badge warna untuk prioritas */
function priorityBadge($priorityName)
{
    $map = [
        'LOW'      => '#D5E7B5',
        'MEDIUM'   => '#72BAA9',
        'HIGH'     => '#AE2448',
        'CRITICAL' => '#6E1A37',
    ];
    $color = $map[$priorityName] ?? '#64748b';
    return '<span class="badge" style="background-color:' . $color . ';">' . h($priorityName) . '</span>';
}
