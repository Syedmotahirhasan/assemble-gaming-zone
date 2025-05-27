<?php 
session_start();
require_once "includes/auth_check.php";
require_once "includes/config.php";
checkAuth();

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $smtp_host = trim($_POST["smtp_host"]);
    $smtp_port = trim($_POST["smtp_port"]);
    $smtp_username = trim($_POST["smtp_username"]);
    $smtp_password = trim($_POST["smtp_password"]);
    
    try {
        // Check if settings already exist for this user
        $stmt = $pdo->prepare("SELECT id FROM email_settings WHERE user_id = ?");
        $stmt->execute([$_SESSION["id"]]);
        
        if ($stmt->rowCount() > 0) {
            // Update existing settings
            $sql = "UPDATE email_settings SET 
                    email = ?, 
                    smtp_host = ?, 
                    smtp_port = ?, 
                    smtp_username = ?, 
                    smtp_password = ? 
                    WHERE user_id = ?";
        } else {
            // Insert new settings
            $sql = "INSERT INTO email_settings 
                    (user_id, email, smtp_host, smtp_port, smtp_username, smtp_password) 
                    VALUES (?, ?, ?, ?, ?, ?)";
        }
        
        $stmt = $pdo->prepare($sql);
        $params = [$email, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $_SESSION["id"]];
        
        if ($stmt->execute($params)) {
            $success_message = "Email settings saved successfully!";
        } else {
            $error_message = "Something went wrong. Please try again.";
        }
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}

// Get current settings if they exist
try {
    $stmt = $pdo->prepare("SELECT * FROM email_settings WHERE user_id = ?");
    $stmt->execute([$_SESSION["id"]]);
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error fetching settings: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Settings - Assemble Gaming Zone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .settings-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #fff;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            color: #fff;
        }
        
        .success-message {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #fff;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .error-message {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #fff;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .submit-btn {
            background: #6c63ff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        
        .submit-btn:hover {
            background: #5a52d4;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="settings-container">
        <h1>Email Settings</h1>
        
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="smtp_host">SMTP Host</label>
                <input type="text" id="smtp_host" name="smtp_host" required 
                       value="<?php echo htmlspecialchars($settings['smtp_host'] ?? 'smtp.gmail.com'); ?>">
            </div>
            
            <div class="form-group">
                <label for="smtp_port">SMTP Port</label>
                <input type="number" id="smtp_port" name="smtp_port" required 
                       value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>">
            </div>
            
            <div class="form-group">
                <label for="smtp_username">SMTP Username</label>
                <input type="text" id="smtp_username" name="smtp_username" required 
                       value="<?php echo htmlspecialchars($settings['smtp_username'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="smtp_password">SMTP Password (App Password)</label>
                <input type="password" id="smtp_password" name="smtp_password" required 
                       value="<?php echo htmlspecialchars($settings['smtp_password'] ?? ''); ?>">
            </div>
            
            <button type="submit" class="submit-btn">Save Settings</button>
        </form>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 