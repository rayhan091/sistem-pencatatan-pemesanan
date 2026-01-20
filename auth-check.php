<?php
// auth-check.php
// Authentication Check

require_once 'config/config.php';

if (!is_logged_in()) {
    $_SESSION['error'] = 'Silakan login terlebih dahulu untuk mengakses halaman ini.';
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    redirect('login.php');
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    $_SESSION['error'] = 'Sesi Anda telah berakhir. Silakan login kembali.';
    redirect('login.php');
}

// Get current user data
$db = new Database();
$user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);

if (!$user) {
    session_unset();
    session_destroy();
    $_SESSION['error'] = 'Sesi tidak valid. Silakan login kembali.';
    redirect('login.php');
}

// Update user session data
$_SESSION['role'] = $user['role'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['email'] = $user['email'];
?>