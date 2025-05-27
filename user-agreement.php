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
    <title>User Agreement - Assemble Gaming Zone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/policy.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="policy-container">
        <div class="policy-content">
            <h1>User Agreement – Assemble Game Zone</h1>
            
            <div class="policy-section">
                <p class="intro">By using Assemble Game Zone, you agree to:</p>
                
                <div class="policy-item">
                    <i class="fas fa-check-circle"></i>
                    <div class="item-content">
                        <h3>Respect Others</h3>
                        <p>No offensive or harmful content.</p>
                    </div>
                </div>

                <div class="policy-item">
                    <i class="fas fa-check-circle"></i>
                    <div class="item-content">
                        <h3>Follow Copyright Rules</h3>
                        <p>Don't copy or distribute our content.</p>
                    </div>
                </div>

                <div class="policy-item">
                    <i class="fas fa-check-circle"></i>
                    <div class="item-content">
                        <h3>Secure Your Account</h3>
                        <p>You're responsible for your login details.</p>
                    </div>
                </div>

                <div class="policy-item">
                    <i class="fas fa-check-circle"></i>
                    <div class="item-content">
                        <h3>Privacy Matters</h3>
                        <p>We handle your data as per our Privacy Policy.</p>
                    </div>
                </div>

                <div class="policy-item">
                    <i class="fas fa-check-circle"></i>
                    <div class="item-content">
                        <h3>Use at Your Own Risk</h3>
                        <p>We're not liable for any damages or third-party links.</p>
                    </div>
                </div>

                <div class="policy-item">
                    <i class="fas fa-check-circle"></i>
                    <div class="item-content">
                        <h3>Stay Updated</h3>
                        <p>Terms may change; continued use means acceptance.</p>
                    </div>
                </div>
            </div>

            <div class="policy-footer">
                <p>📩 Questions? Contact <a href="mailto:syedmhasan229@gmail.com">support@syedmhasan229@gmail.com</a></p>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
