<?php
session_start();

require __DIR__ . '/../db.php';
require __DIR__ . '/auth.php';
require_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    $stmt = db()->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$id]);
}

header('Location: index.php');
exit;
