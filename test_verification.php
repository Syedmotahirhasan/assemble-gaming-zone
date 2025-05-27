<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/mail_helper.php';

echo "<div style='font-family: monospace; padding: 20px; background: #f5f5f5;'>";
echo "<h2>SMTP Debug Test</h2>";

try {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    // Enable debugging
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = function($str, $level) {
        echo "<pre style='margin: 2px; padding: 5px; background: white;'>" . htmlspecialchars($str) . "</pre>";
    };

    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'syedmhasan229@gmail.com';
    $mail->Password = 'obpb lmwx pnlz wrco';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Generate verification code
    $verification_code = sprintf("%06d", mt_rand(100000, 999999));
    
    // Recipients
    $mail->setFrom('syedmhasan229@gmail.com', 'Assemble Gaming Zone');
    $mail->addAddress('syedmhasan229@gmail.com');

    // Content
    $mail->isHTML(true);
    $mail->Subject = "Test Verification Code: " . $verification_code;
    $mail->Body = "Your verification code is: <strong>" . $verification_code . "</strong>";
    $mail->AltBody = "Your verification code is: " . $verification_code;

    echo "<div style='margin: 10px 0; padding: 10px; background: #e9ecef;'>";
    echo "Attempting to send verification code: <strong>" . $verification_code . "</strong><br>";
    echo "To email: <strong>syedmhasan229@gmail.com</strong><br>";
    echo "Using SMTP port: <strong>587</strong><br>";
    echo "</div>";

    if($mail->send()) {
        echo "<div style='color: green; margin-top: 20px; padding: 10px; background: #d4edda;'>";
        echo "✓ Email sent successfully!<br>";
        echo "Verification code: <strong>" . $verification_code . "</strong><br>";
        echo "Please check both your inbox AND spam folder.";
        echo "</div>";
    } else {
        echo "<div style='color: red; margin-top: 20px; padding: 10px; background: #f8d7da;'>";
        echo "✗ Failed to send email<br>";
        echo "Error: " . $mail->ErrorInfo;
        echo "</div>";
    }

} catch (Exception $e) {
    echo "<div style='color: red; margin-top: 20px; padding: 10px; background: #f8d7da;'>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine();
    echo "</div>";
}

// Display PHP error log
echo "<h3>PHP Error Log:</h3>";
$error_log = error_get_last();
if ($error_log) {
    echo "<pre>" . print_r($error_log, true) . "</pre>";
} else {
    echo "No PHP errors logged.";
}

echo "</div>";
?> 