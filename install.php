<?php
/**
 * install.php — Esegui UNA SOLA VOLTA aprendo questo file nel browser.
 * Crea il database SQLite con tabelle e dati iniziali.
 * Dopo l'installazione, ELIMINA questo file per sicurezza.
 */

if (!extension_loaded('pdo_sqlite')) {
    die('Il driver PDO SQLite è richiesto. Abilita l\'estensione pdo_sqlite nel file php.ini prima di eseguire l\'installazione.');
}

$dbFile = __DIR__ . '/database.sqlite';
$firstRun = !file_exists($dbFile);

try {
    $pdo = new PDO('sqlite:' . $dbFile, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');

    // Schema
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug TEXT UNIQUE NOT NULL,
            name TEXT NOT NULL,
            position INTEGER DEFAULT 0
        );

        CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            image_url TEXT DEFAULT NULL,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
            published INTEGER DEFAULT 1,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        );
    ");

    // Categorie di default
    $pdo->exec("
        INSERT OR IGNORE INTO categories (slug, name, position) VALUES
        ('chi-sono', 'Chi sono', 1),
        ('sport',    'Sport',    2),
        ('hobby',    'Hobby',    3),
        ('other',    'Other',    4);
    ");

    // Utente admin di default: admin / admin123
    $defaultHash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO users (username, password_hash) VALUES (?, ?)");
    $stmt->execute(['admin', $defaultHash]);

    echo "<h1 style='font-family:system-ui;'>✅ Installazione completata!</h1>";
    echo "<p style='font-family:system-ui;'>Database creato in: <code>" . htmlspecialchars($dbFile) . "</code></p>";
    echo "<ul style='font-family:system-ui;'>";
    echo "<li>Utente admin: <strong>admin</strong></li>";
    echo "<li>Password: <strong>admin123</strong> (cambiala subito!)</li>";
    echo "</ul>";
    echo "<p style='font-family:system-ui;'><a href='index.php'>→ Vai al sito</a> &nbsp;|&nbsp; <a href='admin/'>→ Pannello admin</a></p>";
    echo "<p style='font-family:system-ui;color:#c00;'><strong>⚠️ Elimina ora il file <code>install.php</code> dal progetto.</strong></p>";

} catch (Exception $e) {
    die('Errore: ' . $e->getMessage());
}