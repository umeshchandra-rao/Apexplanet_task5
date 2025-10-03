<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

logout();
header('Location: ' . APP_BASE_URL . '/login.php');
exit;
