<?php
require_once 'includes/db_connect.php';
require_once 'includes/auth_check.php';

$error = '';
$success = '';

if (!isset($_GET['email']) || !isset($_GET['code'])) {
    header('Location: login.php');
    exit();
}

$email = $_GET['email'];
$code = $_GET['code'];

// Verify the reset code and check attempts
$stmt = $conn->prepare("SELECT * FROM password_resets WHERE email = ? AND verification_code = ? AND used = 0 AND expires_at > NOW()");
$stmt->bind_param("ss", $email, $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: login.php?error=invalid_reset');
    exit();
}

$reset_data = $result->fetch_assoc();

// Check reset count and time restrictions
if ($reset_data['reset_count'] >= 3) {
    $last_reset = new DateTime($reset_data['last_reset_time']);
    $now = new DateTime();
    $diff = $now->diff($last_reset);
    
    if ($diff->days < 2) {
        header('Location: login.php?error=too_many_resets');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Update user password
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);
            $stmt->execute();
            
            // Mark reset code as used and update reset count
            $stmt = $conn->prepare("UPDATE password_resets SET used = 1, reset_count = reset_count + 1, last_reset_time = NOW() WHERE email = ? AND verification_code = ?");
            $stmt->bind_param("ss", $email, $code);
            $stmt->execute();
            
            $conn->commit();
            $success = "Password successfully updated. You can now login with your new password.";
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = "An error occurred. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success">
                <?php echo $success; ?>
                <p><a href="login.php">Click here to login</a></p>
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
