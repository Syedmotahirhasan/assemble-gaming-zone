<?php
// admin_games.php
session_start();
// Simple admin authentication check
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

require_once 'includes/db.php'; // Database connection

// Handle add game
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_game'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = 'images/games/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }
    $stmt = $pdo->prepare('INSERT INTO games (name, description, image, category) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $description, $image, $category]);
    $msg = 'Game added!';
}

// Handle delete game
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM games WHERE id = ?');
    $stmt->execute([$_GET['delete']]);
    $msg = 'Game deleted!';
}

// Fetch all games
$games = $pdo->query('SELECT * FROM games ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Games</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        body { font-family: Arial; margin: 40px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
        .msg { color: green; }
    </style>
</head>
<body>
    <h1>Admin - Manage Games</h1>
    <?php if (!empty($msg)) echo '<p class="msg">' . htmlspecialchars($msg) . '</p>'; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Game Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description"></textarea>
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" id="category" name="category">
        </div>
        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <button type="submit" name="add_game">Add Game</button>
    </form>
    <h2>All Games</h2>
    <table>
        <tr><th>ID</th><th>Name</th><th>Category</th><th>Image</th><th>Actions</th></tr>
        <?php foreach ($games as $game): ?>
        <tr>
            <td><?= $game['id'] ?></td>
            <td><?= htmlspecialchars($game['name']) ?></td>
            <td><?= htmlspecialchars($game['category']) ?></td>
            <td><?php if ($game['image']) echo '<img src="' . htmlspecialchars($game['image']) . '" width="60">'; ?></td>
            <td>
                <a href="?delete=<?= $game['id'] ?>" onclick="return confirm('Delete this game?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
