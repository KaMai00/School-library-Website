<?php
// Initialize the SQLite database and create basic tables + sample data.
// Run this script once (via CLI: `php functions/init_db.php` or open in browser).

// Ensure data directory exists
$dataDir = __DIR__ . '/../data';
if (!is_dir($dataDir)) {
    if (!mkdir($dataDir, 0755, true) && !is_dir($dataDir)) {
        die('Failed to create data directory: ' . $dataDir);
    }
}

$dbFile = $dataDir . '/library.db';
$dsn = 'sqlite:' . $dbFile;

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON;');

    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'user'
    );");

    // Create books table
    $pdo->exec("CREATE TABLE IF NOT EXISTS books (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        author TEXT,
        isbn TEXT,
        category TEXT,
        description TEXT
    );");

    // Insert admin user if not exists
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :u');
    $stmt->execute([':u' => 'admin']);
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('adminpass', PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (:u, :p, :r)');
        $ins->execute([':u' => 'admin', ':p' => $hash, ':r' => 'admin']);
        echo "Created default admin user: username=admin password=adminpass\n";
    } else {
        echo "Admin user already exists.\n";
    }

    // Insert a few sample books
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM books');
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $books = [
            ['1984', 'George Orwell', '9780451524935', 'Dystopia', 'Big Brother is watching you.'],
            ['Brave New World', 'Aldous Huxley', '9780060850524', 'Dystopia', 'A futuristic society.'],
            ['Faust', 'Johann Wolfgang von Goethe', '9780140449013', 'Drama', 'A tragic play.']
        ];
        $ins = $pdo->prepare('INSERT INTO books (title, author, isbn, category, description) VALUES (?, ?, ?, ?, ?)');
        foreach ($books as $b) {
            $ins->execute($b);
        }
        echo "Inserted sample books.\n";
    } else {
        echo "Books table already has entries.\n";
    }

    echo "Initialization complete. Database file: $dbFile\n";
} catch (Exception $e) {
    echo 'Initialization failed: ' . $e->getMessage();
    exit(1);
}
