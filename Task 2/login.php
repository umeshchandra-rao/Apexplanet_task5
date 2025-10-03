<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$username = trim($_POST['username'] ?? '');
	$password = (string)($_POST['password'] ?? '');
	if (!$username || !$password) {
		$errors[] = 'Username and password are required.';
	} else if (login($username, $password)) {
		header('Location: ' . APP_BASE_URL . '/');
		exit;
	} else {
		$errors[] = 'Invalid credentials.';
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login - <?php echo APP_NAME; ?></title>
	<link rel="stylesheet" href="<?php echo APP_BASE_URL; ?>/styles.css">
</head>
<body>
	<?php include __DIR__ . '/navbar.php'; ?>
	<main class="container narrow">
		<h1 class="page-title">Login</h1>
		<?php if ($errors): ?><div class="alert error"><?php foreach ($errors as $e) { echo '<p>' . htmlspecialchars($e) . '</p>'; } ?></div><?php endif; ?>
		<form method="post" class="card form">
			<?php echo csrf_field(); ?>
			<label>Username
				<input type="text" name="username" required>
			</label>
			<label>Password
				<input type="password" name="password" required>
			</label>
			<button type="submit" class="btn primary">Login</button>
			<p class="muted">No account? <a href="<?php echo APP_BASE_URL; ?>/register.php">Register</a></p>
		</form>
	</main>
</body>
</html>
