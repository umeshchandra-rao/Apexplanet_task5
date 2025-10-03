<?php

/**
 * Returns a shared PDO connection using settings from config.php
 */
function get_pdo(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = require __DIR__ . '/config.php';
    $db = $config['db'];

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    // First connect without specifying DB to ensure DB exists
    $serverDsn = sprintf(
        'mysql:host=%s;port=%d;charset=%s',
        $db['host'],
        $db['port'],
        $db['charset']
    );
    $serverPdo = new PDO($serverDsn, $db['username'], $db['password'], $options);
    $dbName = preg_replace('/[^a-zA-Z0-9_]/', '', $db['database']);
    $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET {$db['charset']} COLLATE utf8mb4_unicode_ci");

    // Now connect to the database
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $db['host'],
        $db['port'],
        $db['database'],
        $db['charset']
    );
    $pdo = new PDO($dsn, $db['username'], $db['password'], $options);

    // Ensure posts table exists
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS posts (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FULLTEXT KEY ft_title_content (title, content)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    return $pdo;
}


