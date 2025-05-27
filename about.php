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
    <title>About Us - Assemble Gaming Zone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/about.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="about-container">
        <h1>About Assemble Gaming Zone</h1>
        <div class="about-content">
            <div class="about-section">
                <i class="fas fa-gamepad"></i>
                <h2>Your Gaming Hub</h2>
                <p>Welcome to Assemble Gaming Zone, your ultimate destination for gaming news, reviews, and tutorials. We're passionate about bringing you the latest and greatest from the gaming world.</p>
            </div>

            <div class="about-section">
                <i class="fas fa-users"></i>
                <h2>Our Community</h2>
                <p>Join our growing community of gamers who share their experiences, strategies, and passion for gaming. Whether you're a casual player or a hardcore gamer, there's a place for you here.</p>
            </div>

            <div class="about-section">
                <i class="fas fa-star"></i>
                <h2>Expert Reviews</h2>
                <p>Our team of experienced gamers provides in-depth reviews and analysis of the latest games across all platforms. We help you make informed decisions about your next gaming adventure.</p>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
