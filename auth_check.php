<?php
// logout.php
require_once 'config.php';

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;

// auth_check.php
// Include this file in pages that require user login
require_once 'config.php';

if (!isLoggedIn()) {
    // Store the current URL in the session to redirect back after login
    $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
    redirect('login.php', 'Please login to access this page', 'warning');
}
?>