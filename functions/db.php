<?php
// Flexible PDO wrapper. Supports SQLite (default), MySQL and PostgreSQL via env vars.
// Usage: set environment variables (example below) or edit values here, then:
// require __DIR__ . '/db.php'; then use $pdo

// Read configuration from environment variables (if set)
$driver = getenv('DB_DRIVER') ?: 'sqlite';
$dbPath = getenv('DB_PATH') ?: __DIR__ . '/../data/library.db';
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3360';
$dbName = getenv('DB_NAME') ?: 'mtsp_bibliothek';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '10032008';
$charset = getenv('DB_CHARSET') ?: 'utf8mb4';

try {
    if ($driver === 'sqlite') {
        $dsn = 'sqlite:' . $dbPath;
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // enable foreign keys for SQLite
        $pdo->exec('PRAGMA foreign_keys = ON;');
    } elseif ($driver === 'mysql') {
        $portPart = $port ? (";port={$port}") : '';
        $dsn = "mysql:host={$host};dbname={$dbName}{$portPart};charset={$charset}";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } elseif ($driver === 'pgsql') {
        $portPart = $port ? (" port={$port}") : '';
        $dsn = "pgsql:host={$host};dbname={$dbName}{$portPart}";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } else {
        throw new Exception('Unsupported DB_DRIVER: ' . $driver);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo 'Database connection failed: ' . htmlspecialchars($e->getMessage());
    exit;
}

