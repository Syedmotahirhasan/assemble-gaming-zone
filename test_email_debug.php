<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(__FILE__) . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "<div style='font-family: monospace; padding: 20px;'>";
echo "<h2>SMTP Connection Test</h2>";

try {
    $mail = new PHPMailer(true);

    echo "<div style='margin: 10px 0;'>Testing SMTP Connection...</div>";

    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                    
    $mail->isSMTP();                                         
    $mail->Host       = 'smtp.gmail.com';                    
    $mail->SMTPAuth   = true;                               
    $mail->Username   = 'syedmhasan229@gmail.com';          
    $mail->Password   = 'obpb lmwx pnlz wrco';           // Your App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;    
    $mail->Port       = 587;                                

    // Test the connection without sending an email
    if ($mail->smtpConnect()) {
        echo "<div style='color: green; margin: 10px 0;'>✓ SMTP Connection Successful!</div>";
        
        // Now try to send a test email
        $mail->setFrom('syedmhasan229@gmail.com', 'Assemble Gaming Zone');
        $mail->addAddress('syedmhasan229@gmail.com');
        $mail->Subject = 'Test Email from Assemble Gaming Zone';
        $mail->Body    = 'This is a test email to verify SMTP settings are working.';

        if($mail->send()) {
            echo "<div style='color: green; margin: 10px 0;'>✓ Test email sent successfully!</div>";
        } else {
            echo "<div style='color: red; margin: 10px 0;'>✗ Failed to send email: " . $mail->ErrorInfo . "</div>";
        }
        
        $mail->smtpClose();
    } else {
        echo "<div style='color: red; margin: 10px 0;'>✗ SMTP Connection Failed!</div>";
    }

} catch (Exception $e) {
    echo "<div style='color: red; margin: 10px 0;'>Error: " . $e->getMessage() . "</div>";
}

// Display PHP version and loaded extensions
echo "<h3>System Information:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Loaded Extensions:<br>";
$loaded_extensions = get_loaded_extensions();
sort($loaded_extensions);
foreach($loaded_extensions as $extension) {
    echo "- $extension<br>";
}

echo "</div>";
?> 