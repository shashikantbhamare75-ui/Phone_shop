<?php
// includes/session.php
// Must be included at the very top of every page, before any HTML output

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Returns true if a user is currently logged in.
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Returns true if the logged-in user is an admin.
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Redirects to login page if not logged in. Call at the top of any
 * page that requires a logged-in customer (cart.php, checkout.php, profile.php).
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

/**
 * Redirects to admin login if not an admin. Call at the top of every
 * file inside admin/ except admin/login.php itself.
 */
function requireAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        header("Location: login.php");
        exit();
    }
}?>