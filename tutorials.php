<?php 
session_start();
require_once "includes/auth_check.php";
checkAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaming Tutorials - Assemble Gaming Zone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/tutorials.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="tutorials-container">
        <h1>Gaming Guides & Tutorials</h1>
        
        <div class="tutorials-filter">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search tutorials...">
                <button id="searchButton"><i class="fas fa-search"></i></button>
            </div>
            
            <div class="category-filter">
                <button class="category-btn active" data-category="all">All</button>
                <button class="category-btn" data-category="beginner">Beginner Guides</button>
                <button class="category-btn" data-category="advanced">Advanced Tips</button>
                <button class="category-btn" data-category="walkthroughs">Walkthroughs</button>
            </div>
        </div>

        <div class="tutorials-grid">
            <?php
            // Sample tutorials data (in a real app, this would come from a database)
            $tutorials = [
                [
                    'id' => 1,
                    'title' => 'Face the ultimate challenge in Tomb Raider as you take on the thrilling final boss battle.',
                    'category' => 'walkthroughs',
                    'image' => 'tutorials/tr.jpg',
                    'description' => 'This tutorial will guide you through the intense final boss battle in Tomb Raider, 
                    covering strategies, key tips, and how to emerge victorious',
                    'Difficulty' => 'Easy',
                    'author' => 'TheGamerDie',
                    'date' => '2025-03-15'
                ],
                [
                    'id' => 2,
                    'title' => 'Master advanced combat mechanics and strategies like those used 
                    against the final boss Wazir in Prince of Persia: The Two Thrones to improve your gameplay.',
                    'category' => 'advanced',
                    'image' => 'tutorials/p.jpg',
                    'description' => 'This tutorial will guide you through the final boss battle against the Wazir in Prince of Persia: The Two Thrones, 
                    covering strategies, key tips, and how to defeat him.',
                    'Difficulty' => 'Hard',
                    'author' => 'RippleFX',
                    'date' => '2025-03-14'
                ],
                // Add more tutorials here
            ];

            foreach ($tutorials as $tutorial) {
                $date = date('F j, Y', strtotime($tutorial['date']));
                echo <<<HTML
                <div class="tutorial-card" data-category="{$tutorial['category']}">
                    <div class="tutorial-image">
                        <img src="{$tutorial['image']}" alt="{$tutorial['title']}">
                        <div class="category-tag">{$tutorial['category']}</div>
                    </div>
                    <div class="tutorial-info">
                        <h3>{$tutorial['title']}</h3>
                        <p class="tutorial-description">{$tutorial['description']}</p>
                        <div class="tutorial-meta">
                            <span class="author"><i class="fas fa-user"></i> {$tutorial['author']}</span>
                            <span class="date"><i class="fas fa-calendar"></i> {$date}</span>
                        </div>
                        <a href="tutorial-detail.php?id={$tutorial['id']}" class="read-more" target="_blank">Read Tutorial</a>
                    </div>
                </div>
                HTML;
            }
            ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="js/script.js"></script>
    <script src="js/tutorials.js"></script>
</body>
</html>
