<?php
require __DIR__ . '/db.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/access_control.php';
require __DIR__ . '/csrf.php';

// Require login for reactions
require_auth();

$user = get_logged_in_user();
// Enforce POST for state-changing action
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!verify_csrf_post()) { http_response_code(400); die('Invalid CSRF token'); }

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$type = isset($_POST['type']) && in_array($_POST['type'], ['like', 'dislike']) ? $_POST['type'] : '';

// Validate inputs
if (!$post_id || !$type) {
    header('Location: index.php');
    exit;
}

// Verify post exists
$pdo = get_pdo();
$stmt = $pdo->prepare("SELECT id FROM posts WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $post_id]);
if (!$stmt->fetch()) {
    header('Location: index.php');
    exit;
}

// Add reaction
add_reaction($post_id, $user['id'], $type);

// Redirect back to referring page or index
$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
header("Location: $redirect");
exit;