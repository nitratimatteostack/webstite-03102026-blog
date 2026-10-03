<?php
function db(): PDO {
    if (!extension_loaded('pdo_sqlite')) {
        throw new RuntimeException('Il driver PDO SQLite è necessario. Abilita l\'estensione pdo_sqlite nel file php.ini.');
    }

    static $pdo = null;
    if ($pdo === null) {
        $cfg = require __DIR__ . '/config.php';
        $pdo = new PDO('sqlite:' . $cfg['db']['path'], null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');
    }
    return $pdo;
}