<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}
require_once 'includes/db.php';

// Add trending game
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_trending'])) {
    $game_id = intval($_POST['game_id']);
    // Prevent duplicates
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM trending_games WHERE game_id = ?');
    $stmt->execute([$game_id]);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare('INSERT INTO trending_games (game_id) VALUES (?)');
        $stmt->execute([$game_id]);
        $msg = 'Trending game added!';
    } else {
        $msg = 'Game is already trending.';
    }
}
// Remove trending game
if (isset($_GET['remove'])) {
    $stmt = $pdo->prepare('DELETE FROM trending_games WHERE id = ?');
    $stmt->execute([$_GET['remove']]);
    $msg = 'Trending game removed!';
}
// Fetch all games and trending games
$games = $pdo->query('SELECT * FROM games ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$trending = $pdo->query('SELECT trending_games.id, games.name FROM trending_games JOIN games ON trending_games.game_id = games.id ORDER BY trending_games.added_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Trending Games</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <h1>Admin - Trending Games</h1>
    <?php if (!empty($msg)) echo '<p class="msg">' . htmlspecialchars($msg) . '</p>'; ?>
    <form method="post">
        <label for="game_id">Select Game to Trend:</label>
        <select name="game_id" id="game_id" required>
            <option value="">--Select Game--</option>
            <?php foreach ($games as $game): ?>
                <option value="<?= $game['id'] ?>"><?= htmlspecialchars($game['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="add_trending">Add to Trending</button>
    </form>
    <h2>Trending Games</h2>
    <table>
        <tr><th>ID</th><th>Game Name</th><th>Actions</th></tr>
        <?php foreach ($trending as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><a href="?remove=<?= $row['id'] ?>" onclick="return confirm('Remove from trending?')">Remove</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
