<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    // Server settings
                    
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'syedmhasan229@gmail.com';             // SMTP username
    $mail->Password   = 'obpb lmwx pnlz wrco';                 // SMTP password
    $mail->SMTPSecure = 'PHPMailer::ENCRYPTION_SMTPS';        // Enable implicit TLS encryption
    $mail->Port       = 465;                                    // TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`



    // Simple test message
    $mail->setFrom('syedmhasan229@gmail.com', 'Test Sender');
    $mail->addAddress('syedmhasan229@gmail.com');     // Add a recipient
    $mail->Subject = 'Simple Test Email';
    $mail->Body    = 'This is a simple test email to verify SMTP settings.';

    $mail->send();
    echo '<div style="color: green; font-family: Arial; padding: 20px;">Message has been sent</div>';
} catch (Exception $e) {
    echo '<div style="color: red; font-family: Arial; padding: 20px;">';
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    echo "<br><br>Debug information:<br>";
    echo "PHP version: " . phpversion() . "<br>";
    echo "OpenSSL version: " . OPENSSL_VERSION_TEXT . "<br>";
    echo '</div>';
}
?> 