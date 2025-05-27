<?php 
session_start();
require_once "includes/auth_check.php";
checkAuth();

// Helper function to get image path or fallback
function getGameImage($title, $image) {
    if (file_exists('images/' . $image)) {
        return 'images/' . $image;
    } elseif (file_exists($image)) {
        return $image;
    }
    // Use game-specific placeholder
    return 'https://placehold.co/400x500/1a1a2e/6c5ce7?text=' . urlencode($title);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trending Games - Assemble Gaming Zone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/trending.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="trending-container">
        <h1>Top 10 Trending Games</h1>
        
        <!-- Debug info - only show to admins -->

        <div class="games-grid">
            <?php
            // Fetch top games from the database
            require_once "includes/db.php";
            $trendingGames = $pdo->query("SELECT * FROM top_games ORDER BY id ASC LIMIT 10")->fetchAll();
            foreach ($trendingGames as $index => $game) {
                $imagePath = !empty($game['image']) ? $game['image'] : 'https://placehold.co/400x500/1a1a2e/6c5ce7?text=' . urlencode($game['name']);
                echo <<<HTML
                <div class="game-card">
                    <span class="game-rank">{$game['game_id']}</span>
                    <div class="game-image">
                        <img src="{$imagePath}" alt="{$game['name']}" loading="lazy">
                        <div class="game-genre">{$game['genre']}</div>
                    </div>
                    <div class="game-info">
                        <h3>{$game['name']}</h3>
                        <p class="platforms"><i class="fas fa-gamepad"></i> <span class="platforms-text">{$game['platforms']}</span></p>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <span>{$game['rating']}</span>
                        </div>
                    </div>
                </div>
                HTML;
            }
            ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <style>
    .trending-container {
        padding: 2rem;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        min-height: 100vh;
    }

    .debug-info {
        background: rgba(255, 255, 255, 0.1);
        padding: 1rem;
        margin: 1rem auto;
        max-width: 1400px;
        border-radius: 8px;
        color: #fff;
    }

    .debug-info pre {
        overflow-x: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .games-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
        padding: 2rem 0;
        max-width: 1400px;
        margin: 0 auto;
    }

    .game-card {
        background: rgba(16, 0, 43, 0.95);
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .game-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        border-color: rgba(108, 92, 231, 0.5);
    }

    .game-rank {
        position: absolute;
        top: 270px;
        left: 10px;
        background: #6c5ce7;
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        z-index: 2;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        font-size: 1.1rem;
    }

    .game-image {
        width: 100%;
        height: 320px;
        overflow: hidden;
        position: relative;
        background: #1a1a2e;
    }

    .game-image::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100px;
        background: linear-gradient(to top, rgba(16, 0, 43, 1), transparent);
        z-index: 1;
    }

    .game-genre {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(108, 92, 231, 0.9);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        z-index: 2;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .game-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .game-card:hover .game-image img {
        transform: scale(1.05);
    }

    .game-info {
        padding: 1.5rem;
        position: relative;
    }

    .game-info h3 {
        color: #fff;
        margin: 0 0 0.8rem 0;
        font-size: 1.3rem;
        font-weight: 600;
        line-height: 1.3;
    }

    .platforms {
        color: #6c5ce7;
        font-size: 1rem;
        margin: 0 0 1rem 0;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 4px 12px;
        border-radius: 18px;
        background: rgba(124, 58, 237, 0.10);
        width: fit-content;
        box-shadow: 0 2px 8px rgba(124, 58, 237, 0.08);
        font-weight: 500;
    }
    .platforms-text {
        color: #a594f9;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .platforms i {
        color: #7c3aed;
        font-size: 1.2rem;
    }

    .rating {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #ffd700;
        font-size: 1.1rem;
    }

    .rating span {
        color: #fff;
        font-weight: 500;
    }

    h1 {
        color: #fff;
        text-align: center;
        margin-bottom: 2rem 0 3rem 0;
        font-size: 3rem;
        text-shadow: 0 4px 8px rgba(0,0,0,0.2);
        font-weight: 800;
        background: linear-gradient(45deg, #6c5ce7, #a594f9);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        position: relative;
        padding:3rem;
    }

    @media (max-width: 768px) {
        .games-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            padding: 1rem;
            gap: 1.5rem;
        }

        .game-image {
            height: 280px;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }
    }
    </style>

    <script src="js/script.js"></script>
</body>
</html>
