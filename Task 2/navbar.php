<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
$user = current_user();
?>
<header class="navbar">
	<div class="container nav-inner">
		<a class="brand" href="<?php echo APP_BASE_URL; ?>/"><?php echo APP_NAME; ?></a>
		<nav class="nav-links">
			<a href="<?php echo APP_BASE_URL; ?>/">Home</a>
			<?php if ($user): ?>
				<a href="<?php echo APP_BASE_URL; ?>/create.php">Create</a>
				<span class="nav-user">Hi, <?php echo htmlspecialchars($user['username']); ?></span>
				<a class="btn" href="<?php echo APP_BASE_URL; ?>/logout.php">Logout</a>
			<?php else: ?>
				<a class="btn" href="<?php echo APP_BASE_URL; ?>/login.php">Login</a>
				<a class="btn primary" href="<?php echo APP_BASE_URL; ?>/register.php">Register</a>
			<?php endif; ?>
		</nav>
	</div>
</header>
