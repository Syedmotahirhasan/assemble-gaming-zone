<?php
session_start();
require_once "includes/config.php";

// Check if user is allowed to reset password
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['can_reset_password'])) {
    $_SESSION['reset_error'] = "Please enter your email first.";
    header("Location: forgot-password.php");
    exit;
}

$email = $_SESSION['reset_email'];

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords
    if (empty($new_password) || empty($confirm_password)) {
        $error = "Please enter both passwords.";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        try {
            // Update password in the database
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashed_password, $email]);
            
            // Set success message
            $success = "Password has been successfully changed!";
            
            // Clear reset session variables after 2 seconds (to show success message)
            $_SESSION['password_changed'] = true;
        } catch (Exception $e) {
            $error = "An error occurred. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #FFFFFF;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: rgba(16, 0, 43, 0.95);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .nav-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .nav-header span {
            color: #FFFFFF;
            background: #6c5ce7;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: normal;
        }
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #FFFFFF;
            font-size: 24px;
            font-weight: normal;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #FFFFFF;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #FFFFFF;
            font-size: 16px;
            box-sizing: border-box;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #6c5ce7;
            background: rgba(255, 255, 255, 0.15);
        }
        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .reset-button {
            width: 100%;
            padding: 12px;
            background: #6c5ce7;
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
            margin-top: 1rem;
            font-weight: normal;
        }
        .reset-button:hover {
            background: #5849c2;
            transform: translateY(-2px);
        }
        .reset-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
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
        .back-to-login {
            text-align: center;
            margin-top: 1rem;
        }
        .back-to-login a {
            color: #6c5ce7;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }
        .back-to-login a:hover {
            color: #5849c2;
            text-decoration: underline;
        }
        #passwordMatch {
            font-size: 14px;
            margin-top: 0.5rem;
            color: rgba(255, 255, 255, 0.7);
        }
        .match {
            color: #34c759;
        }
        .no-match {
            color: #ff3b30;
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
            background: none;
            border: none;
            padding: 0;
        }
        .password-toggle:hover {
            color: #6c5ce7;
        }
        .password-field input {
            padding-right: 40px;
        }
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal {
            background: #1B1E2E;
            border-radius: 12px;
            padding: 1.5rem;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .modal-title {
            color: #FFFFFF;
            font-size: 16px;
            margin-bottom: 1rem;
        }
        .modal-message {
            color: #FFFFFF;
            margin-bottom: 1.5rem;
            font-size: 14px;
        }
        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .modal-button {
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }
        .modal-button.primary {
            background: #6c5ce7;
            color: #FFFFFF;
        }
        .modal-button.primary:hover {
            background: #5849c2;
        }
        .modal-button.secondary {
            background: transparent;
            color: #6c5ce7;
            border: 1px solid #6c5ce7;
        }
        .modal-button.secondary:hover {
            background: rgba(108, 92, 231, 0.1);
        }
    </style>
</head>
<body>
    <div class="modal-overlay" id="confirmModal">
        <div class="modal">
            <div class="modal-title">localhost:8080 says</div>
            <div class="modal-message">Are you sure you want to change your password?</div>
            <div class="modal-buttons">
                <button class="modal-button secondary" onclick="closeModal()">Cancel</button>
                <button class="modal-button primary" onclick="confirmPasswordChange()">OK</button>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="nav-header">
            <span>Forgot Password</span>
        </div>
        
        <h2>Reset Password</h2>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <script>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 2000);
            </script>
        <?php else: ?>
            <form method="post" id="resetForm">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="password-field">
                        <input type="password" id="new_password" name="new_password" required 
                               placeholder="Enter new password" minlength="8">
                        <button type="button" class="password-toggle" onclick="togglePassword('new_password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="password-field">
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               placeholder="Confirm new password" minlength="8">
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div id="passwordMatch"></div>
                </div>
                <button type="submit" id="submitBtn" class="reset-button" disabled>Accept New Password</button>
            </form>
        <?php endif; ?>
        
        <div class="back-to-login">
            <a href="login.php">Back to Login</a>
        </div>
    </div>

    <script>
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const submitBtn = document.getElementById('submitBtn');
    const passwordMatch = document.getElementById('passwordMatch');
    const resetForm = document.getElementById('resetForm');
    const confirmModal = document.getElementById('confirmModal');
    let formSubmitting = false;

    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function checkPasswords() {
        if (newPassword.value.length >= 8 && newPassword.value === confirmPassword.value) {
            submitBtn.disabled = false;
            passwordMatch.textContent = 'Passwords match!';
            passwordMatch.className = 'match';
        } else {
            submitBtn.disabled = true;
            if (confirmPassword.value) {
                if (newPassword.value.length < 8) {
                    passwordMatch.textContent = 'Password must be at least 8 characters';
                } else {
                    passwordMatch.textContent = 'Passwords do not match';
                }
                passwordMatch.className = 'no-match';
            } else {
                passwordMatch.textContent = '';
            }
        }
    }

    function showModal() {
        confirmModal.style.display = 'flex';
    }

    function closeModal() {
        confirmModal.style.display = 'none';
        formSubmitting = false;
    }

    function confirmPasswordChange() {
        formSubmitting = true;
        closeModal();
        resetForm.submit();
    }

    newPassword.addEventListener('input', checkPasswords);
    confirmPassword.addEventListener('input', checkPasswords);

    resetForm.addEventListener('submit', function(e) {
        if (!formSubmitting) {
            e.preventDefault();
            showModal();
        }
    });
    </script>
</body>
</html>
