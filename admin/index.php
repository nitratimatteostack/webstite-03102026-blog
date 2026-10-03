<?php
session_start();
require __DIR__ . '/../db.php';
require __DIR__ . '/auth.php';
require_admin();

$posts = db()->query("
    SELECT p.*, c.name AS cat_name
    FROM posts p JOIN categories c ON c.id = p.category_id
    ORDER BY p.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard</title>
<link rel="stylesheet" href="../asset/style.css">
</head>
<body>

<h1>Gestione contenuti</h1>

<p style="display:flex;gap:16px;font-size:14px;margin-bottom:32px;">
    <a href="edit.php">+ Nuovo post</a>
    <a href="../index.php" target="_blank">Vedi sito ↗</a>
    <a href="logout.php" style="margin-left:auto;color:var(--text-secondary);">Logout</a>
</p>

<table>
<thead>
    <tr>
        <th>Titolo</th>
        <th>Categoria</th>
        <th>Data</th>
        <th>Stato</th>
        <th></th>
    </tr>
</thead>
<tbody>
<?php foreach ($posts as $p): ?>
<tr>
    <td><strong><?= htmlspecialchars($p['title']) ?></strong></td>
    <td><?= htmlspecialchars($p['cat_name']) ?></td>
    <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
    <td><?= $p['published'] ? '✓' : '—' ?></td>
    <td style="text-align:right;white-space:nowrap;">
        <a href="edit.php?id=<?= $p['id'] ?>">Modifica</a>
        &nbsp;·&nbsp;
        <a href="delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Eliminare definitivamente?')" style="color:#ff3b30;">Elimina</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</body>
</html>