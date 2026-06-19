<?php
/* [includes/auth.php] - PHP 5.6 compatible */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0;
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return array(
        'id'          => $_SESSION['user_id'],
        'first_name'  => isset($_SESSION['first_name'])  ? $_SESSION['first_name']  : '',
        'last_name'   => isset($_SESSION['last_name'])   ? $_SESSION['last_name']   : '',
        'national_id' => isset($_SESSION['national_id']) ? $_SESSION['national_id'] : '',
        'is_verified' => isset($_SESSION['is_verified']) ? $_SESSION['is_verified'] : 0,
    );
}

function requireLogin($redirect = 'login.php') {
    if (!isLoggedIn()) {
        header('Location: ' . $redirect);
        exit;
    }
}
