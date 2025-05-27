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
    <title>Privacy Policy - Assemble Gaming Zone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/policy.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="policy-container">
        <div class="policy-content">
            <h1>Privacy Policy – Assemble Game Zone</h1>
            
            <div class="policy-section">
                <div class="policy-item">
                    <i class="fas fa-database"></i>
                    <div class="item-content">
                        <h3>What We Collect</h3>
                        <p>Name, email (if provided), site usage data, cookies.</p>
                    </div>
                </div>

                <div class="policy-item">
                    <i class="fas fa-chart-line"></i>
                    <div class="item-content">
                        <h3>How We Use It</h3>
                        <p>Improve site experience, send updates (with consent), ensure security.</p>
                    </div>
                </div>

                <div class="policy-item">
                    <i class="fas fa-shield-alt"></i>
                    <div class="item-content">
                        <h3>Your Privacy</h3>
                        <p>We don't sell or share your data. Your info is protected.</p>
                    </div>
                </div>

                <div class="policy-item">
                    <i class="fas fa-user-shield"></i>
                    <div class="item-content">
                        <h3>Your Rights</h3>
                        <p>Request, update, or delete your data anytime. Manage cookies in settings.</p>
                    </div>
                </div>

                <div class="policy-item">
                    <i class="fas fa-sync"></i>
                    <div class="item-content">
                        <h3>Policy Updates</h3>
                        <p>Using the site means you accept any updates.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
