<?php
require __DIR__ . '/db.php';
$cfg = require __DIR__ . '/config.php';

$categories = db()->query("SELECT * FROM categories ORDER BY position")->fetchAll();
$activeSlug = $_GET['cat'] ?? ($categories[0]['slug'] ?? null);

$activeCategory = null;
foreach ($categories as $c) {
    if ($c['slug'] === $activeSlug) { $activeCategory = $c; break; }
}

$posts = [];
if ($activeCategory) {
    $stmt = db()->prepare("SELECT * FROM posts WHERE category_id = ? AND published = 1 ORDER BY created_at DESC");
    $stmt->execute([$activeCategory['id']]);
    $posts = $stmt->fetchAll();
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)">
<title><?= e($cfg['site']['title']) ?></title>
<link rel="stylesheet" href="asset/style.css">
</head>
<body>

<header class="site-header">
    <h1><?= e($cfg['site']['title']) ?></h1>
    <p class="author">di <?= e($cfg['site']['author']) ?></p>
</header>

<nav class="tabs">
    <?php foreach ($categories as $c): ?>
        <a class="tab <?= $c['slug'] === $activeSlug ? 'active' : '' ?>"
           href="?cat=<?= e($c['slug']) ?>">
            <?= e($c['name']) ?>
        </a>
    <?php endforeach; ?>
</nav>

<main class="content">
    <?php if (!$activeCategory): ?>
        <p class="empty">Nessuna categoria disponibile.</p>
    <?php elseif (empty($posts)): ?>
        <p class="empty">Nessun contenuto in "<?= e($activeCategory['name']) ?>" per ora.</p>
    <?php else: ?>
        <?php foreach ($posts as $p): ?>
            <article class="post">
                <h2><?= e($p['title']) ?></h2>
                <time datetime="<?= date('c', strtotime($p['created_at'])) ?>">
                    <?= date('d F Y', strtotime($p['created_at'])) ?>
                </time>
                <?php if (!empty($p['image_url'])): ?>
                    <img src="<?= e($p['image_url']) ?>" alt="" loading="lazy">
                <?php endif; ?>
                <div class="post-content">
                    <?= nl2br(e($p['content'])) ?>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<footer class="site-footer">
    <a href="admin/">Area riservata</a>
</footer>

</body>
</html>