<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$pdo = get_pdo();
$stmt = $pdo->query('SELECT id, title, created_at FROM posts ORDER BY created_at DESC');
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Home - <?php echo APP_NAME; ?></title>
	<link rel="stylesheet" href="<?php echo APP_BASE_URL; ?>/styles.css">
</head>
<body>
	<?php include __DIR__ . '/navbar.php'; ?>
	<main class="container">
		<h1 class="page-title">Posts</h1>
		<a class="btn primary" href="<?php echo APP_BASE_URL; ?>/create.php">New Post</a>
		<div class="card-list">
			<?php foreach ($posts as $post): ?>
				<a class="card" href="<?php echo APP_BASE_URL; ?>/view.php?id=<?php echo (int)$post['id']; ?>">
					<h3><?php echo htmlspecialchars($post['title']); ?></h3>
					<p class="muted"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></p>
				</a>
			<?php endforeach; ?>
			<?php if (!$posts): ?>
				<p class="muted">No posts yet.</p>
			<?php endif; ?>
		</div>
	</main>
</body>
</html>
