<?php
// Run this script ONCE to update the buy_links field for all reviews (for demo/testing purposes)

require_once 'includes/db.php';

$buy_links = [
    'steam' => [
        'url' => 'https://store.steampowered.com/app/2878960/WWE_2K25/',
        'price' => 3999
    ],
    'epic' => [
        'url' => 'https://store.epicgames.com/en-US/p/grand-theft-auto-v',
        'price' => 3499
    ],
    'ubisoft' => [
        'url' => 'https://store.ubi.com/in/game/xxxx',
        
    ],
    'gog' => [
        'url' => 'https://www.gog.com/game/xxxx',
        'price' => 2899
    ]
];

$json = json_encode($buy_links);

$stmt = $pdo->prepare('UPDATE game_reviews_full SET buy_links = ?');
if ($stmt->execute([$json])) {
    echo "Buy links updated for ALL reviews!";
} else {
    echo "Failed to update buy links for all reviews.";
}
