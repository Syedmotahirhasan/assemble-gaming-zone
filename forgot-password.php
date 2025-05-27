<?php
session_start();
require_once "includes/config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email exists, set session and redirect to reset password page
        $_SESSION['reset_email'] = $email;
        $_SESSION['can_reset_password'] = true;
        header("Location: reset-password.php");
        exit;
    } else {
        $_SESSION['reset_error'] = "This email does not exist in our records.";
        header("Location: forgot-password.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
            display: inline-block;
            color: #FFFFFF;
            background: #6c5ce7;
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 16px;
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
            font-weight: normal;
        }
        .reset-button:hover {
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
            text-align: center;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="nav-header">
            <span>Forgot Password</span>
        </div>
        
        <h2>Reset Password</h2>
        
        <?php
        if (isset($_SESSION['reset_error'])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION['reset_error']) . '</div>';
            unset($_SESSION['reset_error']);
        }
        ?>
        
        <form method="post" action="includes/process_forgot_password.php">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>
            <button type="submit" class="reset-button">Reset Password</button>
        </form>
        
        <div class="back-to-login">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>
