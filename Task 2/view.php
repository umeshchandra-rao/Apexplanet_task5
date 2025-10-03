<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$pdo = get_pdo();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) { http_response_code(404); exit('Post not found'); }
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo htmlspecialchars($post['title']); ?> - <?php echo APP_NAME; ?></title>
	<link rel="stylesheet" href="<?php echo APP_BASE_URL; ?>/styles.css">
</head>
<body>
	<?php include __DIR__ . '/navbar.php'; ?>
	<main class="container narrow">
		<article class="card">
			<h1><?php echo htmlspecialchars($post['title']); ?></h1>
			<p class="muted"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></p>
			<div class="content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
			<?php if ($user): ?>
				<div class="actions">
					<a class="btn" href="<?php echo APP_BASE_URL; ?>/edit.php?id=<?php echo (int)$post['id']; ?>">Edit</a>
					<a class="btn danger" href="<?php echo APP_BASE_URL; ?>/delete.php?id=<?php echo (int)$post['id']; ?>">Delete</a>
				</div>
			<?php endif; ?>
		</article>
	</main>
</body>
</html>
