<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';

$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$username = trim($_POST['username'] ?? '');
	$password = (string)($_POST['password'] ?? '');
	$confirm = (string)($_POST['confirm'] ?? '');
	if ($password !== $confirm) { $errors[] = 'Passwords do not match.'; }
	$result = register_user($username, $password);
	if (!$result['ok']) {
		$errors = array_merge($errors, $result['errors']);
	} else {
		$success = true;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register - <?php echo APP_NAME; ?></title>
	<link rel="stylesheet" href="<?php echo APP_BASE_URL; ?>/styles.css">
</head>
<body>
	<?php include __DIR__ . '/navbar.php'; ?>
	<main class="container narrow">
		<h1 class="page-title">Register</h1>
		<?php if ($success): ?><div class="alert success"><p>Account created. You can now <a href="<?php echo APP_BASE_URL; ?>/login.php">login</a>.</p></div><?php endif; ?>
		<?php if ($errors): ?><div class="alert error"><?php foreach ($errors as $e) { echo '<p>' . htmlspecialchars($e) . '</p>'; } ?></div><?php endif; ?>
		<form method="post" class="card form">
			<?php echo csrf_field(); ?>
			<label>Username
				<input type="text" name="username" required>
			</label>
			<label>Password
				<input type="password" name="password" required>
			</label>
			<label>Confirm Password
				<input type="password" name="confirm" required>
			</label>
			<button type="submit" class="btn primary">Create Account</button>
		</form>
	</main>
</body>
</html>
