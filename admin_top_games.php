<?php
session_start();
// Only allow admin with specific email
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'syedmhasan229@gmail.com') {
    header('Location: login.php');
    exit();
}
require_once 'includes/db.php';

// Handle add top game (limit to 10)
$count = $pdo->query('SELECT COUNT(*) FROM top_games')->fetchColumn();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_top_game']) && $count < 10) {
    $game_id = trim($_POST['game_id']);
    $name = trim($_POST['name']);
    $genre = trim($_POST['genre']);
    $rating = floatval($_POST['rating']);
    $platforms = trim($_POST['platforms']);
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = 'images/top_games/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }
    $stmt = $pdo->prepare('INSERT INTO top_games (game_id, name, genre, rating, image, platforms) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$game_id, $name, $genre, $rating, $image, $platforms]);
    $msg = 'Top game added!';
    header('Location: admin_top_games.php');
    exit();
}
// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM top_games WHERE id = ?');
    $stmt->execute([$_GET['delete']]);
    $msg = 'Top game deleted!';
    header('Location: admin_top_games.php');
    exit();
}
// Fetch all top games
$top_games = $pdo->query('SELECT * FROM top_games ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Top Games</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 40px;
            background: #181a2a;
            color: #e0e0ff;
        }
        h1 {
            color: #7c3aed;
            margin-bottom: 24px;
            font-size: 2rem;
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
        }
        .admin-form {
            background: #23244d;
            border-radius: 18px;
            box-shadow: 0 8px 40px 0 #7c3aed33;
            padding: 36px 48px 28px 48px;
            margin-bottom: 36px;
            display: flex;
            flex-direction: column;
            min-width: 420px;
            width: 100%;
            max-width: 500px;
        }
        @media (max-width: 600px) {
            .admin-form {
                min-width: unset;
                padding: 18px 8px;
                max-width: 98vw;
            }
        }
        .form-group {
            margin-bottom: 16px;
        }
        label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            color: #7c3aed;
        }
        input[type="text"], input[type="number"], input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: 7px;
            border: 1px solid #7c3aed;
            background: #23244d;
            color: #e0e0ff;
            font-size: 1rem;
            margin-bottom: 2px;
        }
        input[type="file"] {
            background: transparent;
            color: #e0e0ff;
        }
        button[type="submit"] {
            background: linear-gradient(90deg, #7c3aed 60%, #a594f9 100%);
            color: #fff;
            border: none;
            padding: 10px 28px;
            border-radius: 7px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 8px #7c3aed33;
            transition: background 0.2s;
        }
        button[type="submit"]:hover {
            background: linear-gradient(90deg, #a594f9 20%, #7c3aed 100%);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: #23244d;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px #7c3aed22;
        }
        th, td {
            border: none;
            padding: 14px 12px;
            text-align: left;
        }
        th {
            background: #1a1a2e;
            color: #7c3aed;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background: #23244d;
        }
        tr:nth-child(odd) {
            background: #181a2a;
        }
        .msg {
            color: #7cfa9c;
            font-weight: bold;
            margin-bottom: 18px;
        }
        img {
            max-height: 60px;
            border-radius: 6px;
            box-shadow: 0 2px 8px #7c3aed33;
        }
        .admin-table-actions a {
            color: #fff;
            background: linear-gradient(90deg, #7c3aed 60%, #a594f9 100%);
            font-weight: bold;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 6px;
            transition: background 0.2s, color 0.2s;
        }
        .admin-table-actions a:hover {
            background: linear-gradient(90deg, #a594f9 20%, #7c3aed 100%);
            color: #fff;
        }
    </style>
</head>
<body>
<div class="admin-container">
    <h1>Admin - Top Games</h1>
    <?php if (!empty($msg)) echo '<p class="msg">' . htmlspecialchars($msg) . '</p>'; ?>
    <?php if ($count < 10): ?>
    <form class="admin-form" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="game_id">Game ID</label>
            <input type="text" id="game_id" name="game_id" required>
        </div>
        <div class="form-group">
            <label for="name">Game Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="genre">Genre</label>
            <input type="text" id="genre" name="genre">
        </div>
        <div class="form-group">
            <label for="rating">Rating (0-10)</label>
            <input type="number" id="rating" name="rating" step="0.1" min="0" max="10" required>
        </div>
        <div class="form-group">
            <label for="platforms">Platforms</label>
            <input type="text" id="platforms" name="platforms" placeholder="e.g. Microsoft Windows, Xbox Series X" required>
        </div>
        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/*" required>
        </div>
        <button type="submit" name="add_top_game">Add Top Game</button>
    </form>
    <?php else: ?>
        <p>You can only add up to 10 top games. Delete one to add more.</p>
    <?php endif; ?>
    <h2>Current Top Games</h2>
    <table>
        <tr><th>ID</th><th>Game ID</th><th>Name</th><th>Genre</th><th>Rating</th><th>Platforms</th><th>Image</th><th>Actions</th></tr>
        <?php foreach ($top_games as $game): ?>
        <tr>
            <td><?= $game['id'] ?></td>
            <td><?= htmlspecialchars($game['game_id']) ?></td>
            <td><?= htmlspecialchars($game['name']) ?></td>
            <td><?= htmlspecialchars($game['genre']) ?></td>
            <td><?= htmlspecialchars($game['rating']) ?></td>
            <td><?= htmlspecialchars($game['platforms']) ?></td>
            <td><?php if ($game['image']) echo '<img src="' . htmlspecialchars($game['image']) . '" width="60">'; ?></td>
            <td class="admin-table-actions"><a href="?delete=<?= $game['id'] ?>" onclick="return confirm('Delete this game?')">Delete</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
