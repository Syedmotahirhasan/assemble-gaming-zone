<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}
require_once "includes/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Assemble Gaming Zone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-nav">
                <button class="auth-nav-item <?php echo !isset($_SESSION['signup_error']) ? 'active' : ''; ?>" data-form="login-form">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                <button class="auth-nav-item <?php echo isset($_SESSION['signup_error']) ? 'active' : ''; ?>" data-form="signup-form">
                    <i class="fas fa-user-plus"></i> Sign Up
                </button>
                <button class="auth-nav-item" data-form="forgot-form">
                    <i class="fas fa-key"></i> Forgot Password
                </button>
            </div>

            <!-- Login Form -->
            <div class="auth-form <?php echo !isset($_SESSION['signup_error']) ? 'active' : ''; ?>" id="login-form">
                <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
                <?php 
                if(isset($_SESSION['login_error'])) {
                    echo '<div class="error-message"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                    unset($_SESSION['login_error']);
                }
                if(isset($_SESSION['login_message'])) {
                    echo '<div class="success-message"><i class="fas fa-check-circle"></i> ' . htmlspecialchars($_SESSION['login_message']) . '</div>';
                    unset($_SESSION['login_message']);
                }
                ?>
                <form action="includes/process_login.php" method="post">
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <div class="password-field">
                            <input type="password" name="password" id="login-password" required>
                            <i class="fas fa-eye password-toggle" data-target="login-password">
                                <i class="fas fa-eye"></i>
                            </i>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit">Login</button>
                </form>
            </div>

            <!-- Sign Up Form -->
            <div class="auth-form <?php echo isset($_SESSION['signup_error']) ? 'active' : ''; ?>" id="signup-form">
                <h2>Sign Up</h2>
                <?php 
                if(isset($_SESSION['signup_error'])) {
                    echo '<div class="error-message"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($_SESSION['signup_error']) . '</div>';
                    unset($_SESSION['signup_error']);
                }
                ?>
                <form action="includes/process_signup.php" method="post">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Username</label>
                        <input type="text" name="username" required minlength="3">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <div class="password-field">
                            <input type="password" name="password" id="signup-password" required minlength="6">
                            <i class="fas fa-eye password-toggle" data-target="signup-password">
                                <i class="fas fa-eye"></i>
                            </i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Confirm Password</label>
                        <div class="password-field">
                            <input type="password" name="confirm_password" id="signup-confirm" required minlength="6">
                            <i class="fas fa-eye password-toggle" data-target="signup-confirm">
                                <i class="fas fa-eye"></i>
                            </i>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit">Sign Up</button>
                </form>
            </div>

            <!-- Forgot Password Form -->
            <div class="auth-form" id="forgot-form">
                <h2><i class="fas fa-key"></i> Reset Password</h2>
                <?php 
                if(isset($_SESSION['reset_error'])) {
                    echo '<div class="error-message"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($_SESSION['reset_error']) . '</div>';
                    unset($_SESSION['reset_error']);
                }
                if(isset($_SESSION['reset_message'])) {
                    echo '<div class="success-message"><i class="fas fa-check-circle"></i> ' . htmlspecialchars($_SESSION['reset_message']) . '</div>';
                    unset($_SESSION['reset_message']);
                }
                ?>
                <form action="includes/process_forgot_password.php" method="post">
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <button type="submit" class="btn-submit">Reset Password</button>
                </form>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <style>
    .auth-container {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    }

    .auth-box {
        background: rgba(16, 0, 43, 0.95);
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 400px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .auth-nav {
        display: flex;
        gap: 10px;
        margin-bottom: 2rem;
    }

    .auth-nav-item {
        flex: 1;
        padding: 12px;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        border-radius: 8px;
        color: #fff;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
    }

    .auth-nav-item:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .auth-nav-item.active {
        background: #6c5ce7;
    }

    .auth-form {
        display: none;
    }

    .auth-form.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #fff;
        font-size: 14px;
    }

    .password-field {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: rgba(255, 255, 255, 0.7);
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        font-size: 16px;
    }

    .password-toggle .fa-eye {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .password-toggle:hover {
        color: #6c5ce7;
    }

    .password-toggle::before {
        content: '';
        position: absolute;
        width: 140%;
        height: 2px;
        background-color: currentColor;
        transform: rotate(45deg);
        top: 50%;
        left: -20%;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .password-toggle.hide::before {
        opacity: 0;
    }

    /* Show line when password is visible */
    .password-toggle:not(.hide)::before {
        opacity: 1;
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: #fff;
        font-size: 16px;
        transition: all 0.3s;
    }

    .password-field input {
        padding-right: 40px;
    }

    .form-group input:focus {
        outline: none;
        border-color: #6c5ce7;
        background: rgba(255, 255, 255, 0.15);
    }

    .btn-submit {
        width: 100%;
        padding: 12px;
        background: #6c5ce7;
        border: none;
        border-radius: 8px;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-submit:hover {
        background: #5849c2;
        transform: translateY(-2px);
    }

    .error-message {
        background: rgba(255, 59, 48, 0.1);
        color: #ff3b30;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 1rem;
        font-size: 14px;
        border: 1px solid rgba(255, 59, 48, 0.2);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .success-message {
        background: rgba(52, 199, 89, 0.1);
        color: #34c759;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 1rem;
        font-size: 14px;
        border: 1px solid rgba(52, 199, 89, 0.2);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Make sure icons are aligned */
    .fas {
        width: 16px;
        text-align: center;
        margin-right: 4px;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching functionality
        const navButtons = document.querySelectorAll('.auth-nav-item');
        const forms = document.querySelectorAll('.auth-form');

        navButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons and forms
                navButtons.forEach(btn => btn.classList.remove('active'));
                forms.forEach(form => form.classList.remove('active'));

                // Add active class to clicked button and corresponding form
                button.classList.add('active');
                const formId = button.getAttribute('data-form');
                document.getElementById(formId).classList.add('active');
            });
        });

        // Password toggle functionality
        const toggleButtons = document.querySelectorAll('.password-toggle');
        toggleButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    button.classList.remove('hide'); // Remove hide class when showing password
                } else {
                    passwordInput.type = 'password';
                    button.classList.add('hide'); // Add hide class when hiding password
                }
            });
        });

        // Initialize all password fields as hidden
        document.querySelectorAll('input[type="password"]').forEach(input => {
            const toggle = input.parentElement.querySelector('.password-toggle');
            if (toggle) toggle.classList.add('hide');
        });
    });
    </script>
</body>
</html>
