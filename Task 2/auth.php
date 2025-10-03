<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function start_session(): void {
	if (session_status() === PHP_SESSION_NONE) {
		session_name(SESSION_NAME);
		session_start();
	}
}

function current_user(): ?array {
	start_session();
	return $_SESSION['user'] ?? null;
}

function require_auth(): void {
	if (!current_user()) {
		header('Location: ' . APP_BASE_URL . '/login.php');
		exit;
	}
}

function login(string $username, string $password): bool {
	$pdo = get_pdo();
	$stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = ? LIMIT 1');
	$stmt->execute([$username]);
	$user = $stmt->fetch();
	if (!$user) { return false; }
	if (!password_verify($password, $user['password'])) { return false; }
	start_session();
	$_SESSION['user'] = [ 'id' => (int)$user['id'], 'username' => $user['username'] ];
	return true;
}

function logout(): void {
	start_session();
	$_SESSION = [];
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}
	session_destroy();
}

function register_user(string $username, string $password): array {
	$pdo = get_pdo();
	$errors = [];
	if (strlen($username) < 3) { $errors[] = 'Username must be at least 3 characters.'; }
	if (strlen($password) < 8) { $errors[] = 'Password must be at least 8 characters.'; }
	if ($errors) { return ['ok' => false, 'errors' => $errors]; }
	try {
		$hash = password_hash($password, PASSWORD_BCRYPT);
		$stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
		$stmt->execute([$username, $hash]);
		return ['ok' => true];
	} catch (PDOException $e) {
		if (str_contains($e->getMessage(), 'Duplicate')) {
			$errors[] = 'Username already exists.';
		} else {
			$errors[] = 'Registration failed.';
		}
		return ['ok' => false, 'errors' => $errors];
	}
}
