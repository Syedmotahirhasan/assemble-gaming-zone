<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assemble Gaming Zone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="hero">
        <div class="hero-content">
            <h1>Welcome to Assemble Gaming Zone</h1>
            <p>Your ultimate destination for gaming news, reviews, and tutorials</p>
            <a href="trending.php" class="cta-button">Explore Trending Games</a>
        </div>
    </main>

    <section class="featured-section">
        <h2>Featured Content</h2>
        <div class="featured-grid">
            <div class="featured-card">
                <i class="fas fa-gamepad"></i>
                <h3>Latest Reviews</h3>
                <p>Discover in-depth reviews of the newest games</p>
            </div>
            <div class="featured-card">
                <i class="fas fa-chart-line"></i>
                <h3>Trending Games</h3>
                <p>See what's hot in the gaming world</p>
            </div>
            <div class="featured-card">
                <i class="fas fa-book-reader"></i>
                <h3>Gaming Guides</h3>
                <p>Master your favorite games with our tutorials</p>
            </div>
        </div>
    </section>

    <main class="auth-container">
        <div class="auth-box">

        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>
