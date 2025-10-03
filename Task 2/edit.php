<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';

require_auth();
$pdo = get_pdo();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) { http_response_code(404); exit('Post not found'); }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$title = trim($_POST['title'] ?? '');
	$content = trim($_POST['content'] ?? '');
	if (!$title) { $errors[] = 'Title is required.'; }
	if (!$content) { $errors[] = 'Content is required.'; }
	if (!$errors) {
		$upd = $pdo->prepare('UPDATE posts SET title = ?, content = ? WHERE id = ?');
		$upd->execute([$title, $content, $id]);
		header('Location: ' . APP_BASE_URL . '/view.php?id=' . $id);
		exit;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Edit Post - <?php echo APP_NAME; ?></title>
	<link rel="stylesheet" href="<?php echo APP_BASE_URL; ?>/styles.css">
</head>
<body>
	<?php include __DIR__ . '/navbar.php'; ?>
	<main class="container narrow">
		<h1 class="page-title">Edit Post</h1>
		<?php if ($errors): ?><div class="alert error"><?php foreach ($errors as $e) { echo '<p>' . htmlspecialchars($e) . '</p>'; } ?></div><?php endif; ?>
		<form method="post" class="card form">
			<?php echo csrf_field(); ?>
			<label>Title
				<input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
			</label>
			<label>Content
				<textarea name="content" rows="8" required><?php echo htmlspecialchars($post['content']); ?></textarea>
			</label>
			<button class="btn primary" type="submit">Update</button>
		</form>
	</main>
</body>
</html>
