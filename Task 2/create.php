<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';

require_auth();
$pdo = get_pdo();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$title = trim($_POST['title'] ?? '');
	$content = trim($_POST['content'] ?? '');
	if (!$title) { $errors[] = 'Title is required.'; }
	if (!$content) { $errors[] = 'Content is required.'; }
	if (!$errors) {
		$stmt = $pdo->prepare('INSERT INTO posts (title, content) VALUES (?, ?)');
		$stmt->execute([$title, $content]);
		header('Location: ' . APP_BASE_URL . '/');
		exit;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Create Post - <?php echo APP_NAME; ?></title>
	<link rel="stylesheet" href="<?php echo APP_BASE_URL; ?>/styles.css">
</head>
<body>
	<?php include __DIR__ . '/navbar.php'; ?>
	<main class="container narrow">
		<h1 class="page-title">Create Post</h1>
		<?php if ($errors): ?><div class="alert error"><?php foreach ($errors as $e) { echo '<p>' . htmlspecialchars($e) . '</p>'; } ?></div><?php endif; ?>
		<form method="post" class="card form">
			<?php echo csrf_field(); ?>
			<label>Title
				<input type="text" name="title" required>
			</label>
			<label>Content
				<textarea name="content" rows="8" required></textarea>
			</label>
			<button class="btn primary" type="submit">Save</button>
		</form>
	</main>
</body>
</html>
