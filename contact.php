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
    <title>Contact Us - Assemble Gaming Zone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/contact.css">
    <style>
        .success-message {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid #28a745;
            color: #28a745;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 16px;
            animation: slideDown 0.5s ease-out, fadeIn 0.5s ease-out;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .success-message:before {
            content: '✓';
            display: inline-block;
            width: 20px;
            height: 20px;
            line-height: 20px;
            text-align: center;
            background: #28a745;
            color: white;
            border-radius: 50%;
            margin-right: 10px;
        }

        @keyframes slideDown {
            from { transform: translateY(-20px); }
            to { transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="contact-container">
        <h1>Contact Us</h1>
        <div class="contact-content">
            <div class="contact-info">
                <div class="contact-section">
                    <i class="fas fa-envelope"></i>
                    <h2>Email</h2>
                    <p>General Inquiries: <a href="mailto:syedmhasan229@gmail.com">syedmhasan229@gmail.com</a></p>
                    <p>Business Inquiries: <a href="mailto:business@assemblegamezone.com">business@assemblegamezone.com</a></p>
                </div>

                <div class="contact-section">
                    <i class="fas fa-hashtag"></i>
                    <h2>Social Media</h2>
                    <div class="social-links">
                        <a href="https://www.instagram.com/s.m.h_assemble/profilecard/?igsh=bThibHduYTBjeXRp" target="_blank" class="social-link">
                            <i class="fab fa-instagram"></i>
                            <span>Instagram</span>
                        </a>
                        <a href="https://youtube.com/@s.m.h_assemble229?feature=shared" target="_blank" class="social-link">
                            <i class="fab fa-youtube"></i>
                            <span>YouTube</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="contact-form">
                <h2>Send us a Message</h2>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="success-message"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
                    <?php unset($_SESSION['success']); ?>
                <?php elseif (isset($_SESSION['error'])): ?>
                    <div class="error-message"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="includes/send_message.php" method="POST">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required placeholder="Your Name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="Your Email">
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required placeholder="Subject">
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" required placeholder="Your Message"></textarea >
                    </div>
                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="js/script.js"></script>
</body>
</html>
