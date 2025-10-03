<?php
/**
 * Authentication and authorization functions
 */

require_once 'validation.php';
require_once 'db.php';
require_once 'csrf.php';

/**
 * Register a new user
 * 
 * @param string $username Username
 * @param string $password Password
 * @param string $email Email
 * @param string $role Role (default: 'user')
 * @return array Result with success status and message
 */
function register_user($username, $password, $email, $role = 'user') {
    $pdo = get_pdo();
    
    // Validate input
    $validation = validate_form(
        ['username' => $username, 'password' => $password, 'email' => $email],
        [
            'username' => ['required' => true, 'min_length' => 3, 'max_length' => 50],
            'password' => ['required' => true, 'type' => 'password'],
            'email' => ['required' => true, 'type' => 'email']
        ]
    );
    
    if (!$validation['valid']) {
        return ['success' => false, 'message' => reset($validation['errors'])];
    }
    
    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->rowCount() > 0) {
        return ['success' => false, 'message' => 'Username or email already exists'];
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Validate role
    $allowed_roles = ['user', 'admin', 'editor'];
    if (!in_array($role, $allowed_roles)) {
        $role = 'user'; // Default to user if invalid role
    }
    
    // Insert user
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$username, $hashed_password, $email, $role]);
    
    if ($result) {
        return ['success' => true, 'message' => 'Registration successful'];
    } else {
        return ['success' => false, 'message' => 'Registration failed'];
    }
}

/**
 * Login a user
 * 
 * @param string $username Username
 * @param string $password Password
 * @return array Result with success status and user data if successful
 */
function login_user($username, $password) {
    $pdo = get_pdo();
    
    // Validate input
    $validation = validate_form(
        ['username' => $username, 'password' => $password],
        [
            'username' => ['required' => true],
            'password' => ['required' => true]
        ]
    );
    
    if (!$validation['valid']) {
        return ['success' => false, 'message' => reset($validation['errors'])];
    }
    
    // Basic login rate limiting per username + IP
    if (session_status() == PHP_SESSION_NONE) { session_start(); }
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = 'login_attempts_' . sha1(strtolower($username) . '|' . $ip);
    $attempts = $_SESSION[$key]['count'] ?? 0;
    $firstAt = $_SESSION[$key]['first'] ?? time();
    // Reset window after 10 minutes
    if (time() - $firstAt > 600) { $attempts = 0; $firstAt = time(); }
    if ($attempts >= 5) {
        $wait = max(0, 600 - (time() - $firstAt));
        return ['success' => false, 'message' => 'Too many attempts. Try again in ' . ceil($wait/60) . ' min'];
    }

    // Get user
    $stmt = $pdo->prepare("SELECT id, username, password, email, role FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION[$key] = ['count' => $attempts + 1, 'first' => $firstAt];
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
    
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // Reset attempts on success
    unset($_SESSION[$key]);
    
    // Remove password from user data
    unset($user['password']);
    
    return ['success' => true, 'message' => 'Login successful', 'user' => $user];
}

/**
 * Check if user is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function is_logged_in() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['user_id']);
}

/**
 * Get current user data
 * 
 * @return array|null User data or null if not logged in
 */
function get_logged_in_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    return $stmt->fetch();
}

/**
 * Check if user has required role
 * 
 * @param string|array $required_roles Required role(s)
 * @return bool True if user has required role, false otherwise
 */
function has_role($required_roles) {
    if (!is_logged_in()) {
        return false;
    }
    
    // Convert single role to array
    if (!is_array($required_roles)) {
        $required_roles = [$required_roles];
    }
    
    return in_array($_SESSION['role'], $required_roles);
}

/**
 * Logout current user
 */
function logout_user() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();
}

/**
 * Check if user has permission to perform action
 * 
 * @param string $action Action to check
 * @param int|null $resource_user_id User ID of resource owner (for ownership checks)
 * @return bool True if user has permission, false otherwise
 */
function has_permission($action, $resource_user_id = null) {
    if (!is_logged_in()) {
        return false;
    }
    
    $user = get_logged_in_user();
    
    // Admin has all permissions
    if ($user['role'] === 'admin') {
        return true;
    }
    
    // Check ownership (user can edit their own resources)
    if ($resource_user_id !== null && $resource_user_id === $user['id']) {
        return true;
    }
    
    // Role-based permissions
    $permissions = [
        'user' => ['view'],
        'editor' => ['view', 'create', 'edit'],
        'admin' => ['view', 'create', 'edit', 'delete']
    ];
    
    return in_array($action, $permissions[$user['role']]);
}