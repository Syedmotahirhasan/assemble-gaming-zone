<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

echo "<div style='font-family: Arial; padding: 20px;'>";
echo "<h2>Email Test with Debug Info</h2>";

try {
    $mail = new PHPMailer(true);

    // Debug mode ON
    $mail->SMTPDebug = 3; // More detailed debug output
    $mail->Debugoutput = function($str, $level) {
        echo "<pre style='background: #f4f4f4; padding: 10px; margin: 5px;'>" . htmlspecialchars($str) . "</pre>";
    };

    echo "<div style='background: #e9ecef; padding: 10px; margin: 10px 0;'>";
    echo "Starting SMTP connection test...<br>";
    echo "Host: smtp.gmail.com<br>";
    echo "Port: 465<br>";
    echo "Security: SMTPS<br>";
    echo "</div>";

    // Basic settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->SMTPAuth = true;

    // Your credentials
    $mail->Username = 'syedmhasan229@gmail.com';
    $mail->Password = 'obpb lmwx pnlz wrco'; // Your App Password

    // Additional settings for troubleshooting
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Timeout = 30;

    // Email content
    $mail->setFrom('syedmhasan229@gmail.com', 'Test');
    $mail->addAddress('syedmhasan229@gmail.com');
    $mail->Subject = 'Test Email ' . date('H:i:s');
    $mail->Body = 'This is a test email sent at ' . date('Y-m-d H:i:s');

    echo "<div style='background: #e9ecef; padding: 10px; margin: 10px 0;'>";
    echo "Attempting to send email...<br>";
    echo "From: syedmhasan229@gmail.com<br>";
    echo "To: syedmhasan229@gmail.com<br>";
    echo "</div>";

    // Send it
    if($mail->send()) {
        echo "<div style='background: #d4edda; color: green; padding: 10px; margin: 10px 0;'>";
        echo "✓ Email sent successfully!<br>";
        echo "Please check your inbox AND spam folder.";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: red; padding: 10px; margin: 10px 0;'>";
        echo "✗ Email could not be sent.<br>";
        echo "Error: " . $mail->ErrorInfo;
        echo "</div>";
    }

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: red; padding: 10px; margin: 10px 0;'>";
    echo "✗ Error occurred!<br>";
    echo "Error message: " . $e->getMessage() . "<br>";
    echo "PHP version: " . phpversion() . "<br>";
    if(function_exists('openssl_get_cert_locations')) {
        echo "OpenSSL installed: Yes<br>";
    } else {
        echo "OpenSSL installed: No<br>";
    }
    echo "</div>";
}

echo "</div>";
?> 