<?php
/**
 * User access control system
 */
require_once __DIR__ . '/auth.php';

/**
 * Check if user can access a page
 * Redirects to login page if not authorized
 * 
 * @param string|array $required_roles Required role(s) to access the page
 * @return void
 */
function require_auth($required_roles = null) {
    // Check if user is logged in
    if (!is_logged_in()) {
        // Store the requested URL for redirection after login
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        
        // Redirect to login page
        header('Location: login.php');
        exit;
    }
    
    // If no specific roles required, just being logged in is enough
    if ($required_roles === null) {
        return;
    }
    
    // Check if user has required role
    if (!has_role($required_roles)) {
        // Redirect to unauthorized page
        header('Location: unauthorized.php');
        exit;
    }
}

/**
 * Check if user can perform action on a resource
 * 
 * @param string $action Action to check (view, create, edit, delete)
 * @param array $resource Resource data with user_id
 * @return bool True if authorized, false otherwise
 */
function can_access_resource($action, $resource = null) {
    // If not logged in, deny access
    if (!is_logged_in()) {
        return false;
    }
    
    $user = get_logged_in_user();
    
    // Admin can do anything
    if ($user['role'] === 'admin') {
        return true;
    }
    
    // Check resource ownership
    $resource_user_id = $resource['user_id'] ?? null;
    $is_owner = ($resource_user_id !== null && $resource_user_id === $user['id']);
    
    // Role-based permissions
    switch ($user['role']) {
        case 'editor':
            // Editors can view, create, and edit any post, but only delete their own
            return ($action !== 'delete' || $is_owner);
            
        case 'user':
            // Regular users can view any post, but only create, edit, or delete their own
            return ($action === 'view' || ($action === 'create') || ($action !== 'view' && $is_owner));
            
        default:
            return false;
    }
}

/**
 * Display content only if user has permission
 * 
 * @param string $action Action to check
 * @param array $resource Resource data with user_id
 * @param callable $content_callback Function that outputs content
 * @return void
 */
function show_if_authorized($action, $resource, $content_callback) {
    if (can_access_resource($action, $resource)) {
        call_user_func($content_callback);
    }
}