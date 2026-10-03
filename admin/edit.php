<?php
session_start();
require __DIR__ . '/../db.php';
require __DIR__ . '/auth.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$categories = db()->query("SELECT * FROM categories ORDER BY position")->fetchAll();
$post = ['title' => '', 'content' => '', 'image_url' => '', 'category_id' => $categories[0]['id'] ?? 0, 'published' => 1];

if ($id) {
    $stmt = db()->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch() ?: $post;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title'       => trim($_POST['title'] ?? ''),
        'content'     => trim($_POST['content'] ?? ''),
        'image_url'   => trim($_POST['image_url'] ?? ''),
        'category_id' => (int)$_POST['category_id'],
        'published'   => isset($_POST['published']) ? 1 : 0,
    ];
    if ($id) {
        $stmt = db()->prepare("UPDATE posts SET title=?, content=?, image_url=?, category_id=?, published=? WHERE id=?");
        $stmt->execute([...array_values($data), $id]);
    } else {
        $stmt = db()->prepare("INSERT INTO posts (title, content, image_url, category_id, published) VALUES (?,?,?,?,?)");
        $stmt->execute(array_values($data));
    }
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $id ? 'Modifica' : 'Nuovo' ?> post</title>
<link rel="stylesheet" href="../asset/style.css">
</head>
<body>

<h1><?= $id ? 'Modifica post' : 'Nuovo post' ?></h1>

<form method="post">
    <p>
        <label style="display:block;font-size:13px;color:var(--text-tertiary);margin-bottom:6px;font-weight:500;text-transform:uppercase;letter-spacing:0.06em;">Titolo</label>
        <input name="title" value="<?= htmlspecialchars($post['title']) ?>" required autofocus>
    </p>

    <p>
        <label style="display:block;font-size:13px;color:var(--text-tertiary);margin-bottom:6px;font-weight:500;text-transform:uppercase;letter-spacing:0.06em;">Categoria</label>
        <select name="category_id">
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $c['id'] == $post['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label style="display:block;font-size:13px;color:var(--text-tertiary);margin-bottom:6px;font-weight:500;text-transform:uppercase;letter-spacing:0.06em;">URL immagine (opzionale)</label>
        <input name="image_url" type="url" value="<?= htmlspecialchars($post['image_url']) ?>" placeholder="https://...">
    </p>

    <p>
        <label style="display:block;font-size:13px;color:var(--text-tertiary);margin-bottom:6px;font-weight:500;text-transform:uppercase;letter-spacing:0.06em;">Contenuto</label>
        <textarea name="content" rows="14" required><?= htmlspecialchars($post['content']) ?></textarea>
    </p>

    <p>
        <label>
            <input type="checkbox" name="published" <?= $post['published'] ? 'checked' : '' ?>>
            Pubblicato
        </label>
    </p>

    <p style="display:flex;gap:12px;align-items:center;margin-top:32px;">
        <button type="submit">Salva</button>
        <a href="index.php" style="color:var(--text-secondary);text-decoration:none;font-size:15px;">Annulla</a>
    </p>
</form>

</body>
</html>