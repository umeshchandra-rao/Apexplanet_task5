<?php
require __DIR__ . '/db.php';
require __DIR__ . '/auth.php';

// Require login for reactions
require_login();

$user = get_logged_in_user();
$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;
$type = isset($_GET['type']) && in_array($_GET['type'], ['like', 'dislike']) ? $_GET['type'] : '';

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