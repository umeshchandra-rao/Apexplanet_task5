<?php
/**
 * Simple CSRF protection helpers
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get or generate a CSRF token for the current session
 *
 * @return string
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output a hidden input field containing the CSRF token
 *
 * @return void
 */
function csrf_field() {
    $token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Verify CSRF token from POST request
 *
 * @return bool
 */
function verify_csrf_post() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return true; // Only enforce for POST
    }
    $token = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    return is_string($token) && is_string($sessionToken) && hash_equals($sessionToken, $token);
}


