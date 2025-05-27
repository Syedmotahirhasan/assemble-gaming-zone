<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "includes/config.php";

try {
    // Test account details
    $username = "gamer123";
    $email = "gamer@test.com";
    $password = "gaming123";
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Create test user
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$username, $email, $hashed_password]);
    
    if ($result) {
        echo "<div style='font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>";
        echo "<h2 style='color: #4CAF50;'>✅ Test Account Created Successfully!</h2>";
        echo "<div style='background: #f5f5f5; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<p><strong>Username:</strong> gamer123</p>";
        echo "<p><strong>Email:</strong> gamer@test.com</p>";
        echo "<p><strong>Password:</strong> gaming123</p>";
        echo "</div>";
        echo "<a href='login.php' style='display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Go to Login Page</a>";
        echo "</div>";
    }

} catch (PDOException $e) {
    if ($e->getCode() == 23000) { // Duplicate entry error
        echo "<div style='color: #721c24; background: #f8d7da; padding: 20px; border-radius: 5px; margin: 20px;'>";
        echo "This email is already registered. Try logging in instead.";
        echo "<br><br><a href='login.php' style='color: #721c24;'>Go to Login Page</a>";
        echo "</div>";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
