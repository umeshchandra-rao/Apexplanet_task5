<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

function csrf_token(): string {
	start_session();
	if (empty($_SESSION[CSRF_TOKEN_NAME])) {
		$_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
	}
	return $_SESSION[CSRF_TOKEN_NAME];
}

function csrf_field(): string {
	$token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
	return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
}

function verify_csrf(): void {
	start_session();
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$token = $_POST[CSRF_TOKEN_NAME] ?? '';
		if (!$token || !hash_equals($_SESSION[CSRF_TOKEN_NAME] ?? '', $token)) {
			http_response_code(400);
			exit('Invalid CSRF token');
		}
	}
}
