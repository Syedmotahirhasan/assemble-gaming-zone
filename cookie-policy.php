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
    <title>Cookie Policy - Assemble Gaming Zone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/policy.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="policy-container">
        <div class="policy-content">
            <h1>Cookies Policy – Assemble Game Zone</h1>
            
            <div class="policy-section">
                <div class="policy-item">
                    <i class="fas fa-cookie-bite"></i>
                    <div class="item-content">
                        <h3>What Are Cookies?</h3>
                        <p>Small files stored on your device to enhance your experience.</p>
                    </div>
                </div>

                <div class="policy-item">
                    <i class="fas fa-cogs"></i>
                    <div class="item-content">
                        <h3>How We Use Cookies</h3>
                        <ul>
                            <li>Improve website functionality & performance.</li>
                            <li>Analyze site traffic & user behavior.</li>
                            <li>Remember preferences for a better experience.</li>
                        </ul>
                    </div>
                </div>

                <div class="policy-item">
                    <i class="fas fa-sliders-h"></i>
                    <div class="item-content">
                        <h3>Your Control</h3>
                        <ul>
                            <li>Manage or disable cookies via browser settings.</li>
                            <li>Blocking cookies may affect site features.</li>
                        </ul>
                    </div>
                </div>

                <div class="policy-item">
                    <i class="fas fa-sync"></i>
                    <div class="item-content">
                        <h3>Policy Updates</h3>
                        <p>Using our site means you accept our cookie usage.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
