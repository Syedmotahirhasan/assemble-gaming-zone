<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "<div style='font-family: monospace; padding: 20px; background: #f5f5f5;'>";
echo "<h2>SMTP SSL Test</h2>";

try {
    $mail = new PHPMailer(true);

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
    $mail->Password = 'yzlm owix vlpe pitm';
    
    // Try SSL instead of TLS
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;  // SSL port

    // Additional settings for better delivery
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Generate verification code
    $verification_code = sprintf("%06d", mt_rand(100000, 999999));
    
    // Recipients
    $mail->setFrom('syedmhasan229@gmail.com', 'Assemble Gaming Zone');
    $mail->addAddress('syedmhasan229@gmail.com');

    // Content
    $mail->isHTML(true);
    $mail->Subject = "SSL Test - Verification Code: " . $verification_code;
    $mail->Body = "Your verification code is: <strong>" . $verification_code . "</strong>";
    $mail->AltBody = "Your verification code is: " . $verification_code;

    echo "<div style='margin: 10px 0; padding: 10px; background: #e9ecef;'>";
    echo "Attempting to send verification code using SSL...<br>";
    echo "Code: <strong>" . $verification_code . "</strong><br>";
    echo "To: <strong>syedmhasan229@gmail.com</strong><br>";
    echo "Using SMTP port: <strong>465 (SSL)</strong><br>";
    echo "</div>";

    if($mail->send()) {
        echo "<div style='color: green; margin-top: 20px; padding: 10px; background: #d4edda;'>";
        echo "✓ Email sent successfully using SSL!<br>";
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

echo "</div>";
?> 