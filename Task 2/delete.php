<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';

require_auth();
$pdo = get_pdo();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT title FROM posts WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) { http_response_code(404); exit('Post not found'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$del = $pdo->prepare('DELETE FROM posts WHERE id = ?');
	$del->execute([$id]);
	header('Location: ' . APP_BASE_URL . '/');
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Delete Post - <?php echo APP_NAME; ?></title>
	<link rel="stylesheet" href="<?php echo APP_BASE_URL; ?>/styles.css">
</head>
<body>
	<?php include __DIR__ . '/navbar.php'; ?>
	<main class="container narrow">
		<h1 class="page-title">Delete Post</h1>
		<div class="card">
			<p>Are you sure you want to delete "<?php echo htmlspecialchars($post['title']); ?>"?</p>
			<form method="post">
				<?php echo csrf_field(); ?>
				<button class="btn danger" type="submit">Yes, delete</button>
				<a class="btn" href="<?php echo APP_BASE_URL; ?>/view.php?id=<?php echo $id; ?>">Cancel</a>
			</form>
		</div>
	</main>
</body>
</html>
