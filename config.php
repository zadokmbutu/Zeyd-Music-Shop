<?php
// Database Configuration
$host = "localhost";
$dbname = "ecommerce";
$username = "root";
$password = "";

// Secure Database Connection
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
        $username, 
        $password, 
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Enables error exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // Fetch data as associative array
            PDO::ATTR_EMULATE_PREPARES   => false,                 // Enforce real prepared statements
        ]
    );
} catch (PDOException $e) {
    die("❌ ERROR: Could not connect. " . $e->getMessage());
}

// Start Secure Session If Not Already Started
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400, // 1 Day
        'path'     => '/',
        'domain'   => '', // Default domain
        'secure'   => isset($_SERVER['HTTPS']), // Enable Secure Mode if HTTPS is used
        'httponly' => true, // Prevent JavaScript Access
        'samesite' => 'Strict' // Protect Against CSRF Attacks
    ]);
    session_start();
}

/**
 * Check if the user is logged in
 *
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect to a specified page with a message
 *
 * @param string $url The target URL
 * @param string $message The message to display (optional)
 * @param string $type The message type (success, danger, info)
 */
function redirect($url, $message = "", $type = "info") {
    if (!empty($message)) {
        $_SESSION['message'] = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        $_SESSION['message_type'] = $type;
    }
    header("Location: $url");
    exit;
}

/**
 * Display system messages (e.g., success, error notifications)
 */
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'] ?? 'info';
        echo '<div class="alert alert-' . htmlspecialchars($type) . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8');
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
        
        // Remove message after displaying it
        unset($_SESSION['message'], $_SESSION['message_type']);
    }
}
?>
