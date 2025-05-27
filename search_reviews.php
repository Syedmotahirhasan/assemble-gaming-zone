<?php
// search_reviews.php: AJAX search endpoint for live game search
header('Content-Type: application/json');
require_once 'includes/db.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];
if ($q !== '') {
    $stmt = $pdo->prepare("SELECT id, game_name, main_image, release_date, platforms, score FROM game_reviews_full WHERE game_name LIKE ? ORDER BY created_at DESC LIMIT 20");
    $stmt->execute([$q . '%']); // Only names starting with query
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // If query is empty, return all games (limit 20)
    $results = $pdo->query("SELECT id, game_name, main_image, release_date, platforms, score FROM game_reviews_full ORDER BY created_at DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
}
echo json_encode(['reviews' => $results]);
