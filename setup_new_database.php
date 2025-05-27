<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Connect without database selected
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create new database
    $sql = "CREATE DATABASE IF NOT EXISTS assembles_gamezone";
    $pdo->exec($sql);
    
    // Select the new database
    $pdo->exec("USE assembles_gamezone");
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);

    // Create games table
    $sql = "CREATE TABLE IF NOT EXISTS games (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        description TEXT,
        genre VARCHAR(50),
        rating DECIMAL(3,1),
        release_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);

    // Create reviews table
    $sql = "CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT,
        author_id INT,
        rating INT NOT NULL,
        review_text TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
        FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);

    // Create a test account
    $username = "gamer123";
    $email = "gamer@test.com";
    $password = password_hash("gaming123", PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $email, $password]);

    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>";
    echo "<h2 style='color: #4CAF50;'>✅ Database Setup Complete!</h2>";
    echo "<div style='background: #f5f5f5; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<p>✅ Database 'assembles_gamezone' created</p>";
    echo "<p>✅ Tables created: users, games, reviews</p>";
    echo "<p>✅ Test account created:</p>";
    echo "<ul>";
    echo "<li>Email: gamer@test.com</li>";
    echo "<li>Password: gaming123</li>";
    echo "</ul>";
    echo "</div>";
    echo "<a href='login.php' style='display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Go to Login Page</a>";
    echo "</div>";

} catch(PDOException $e) {
    echo "<div style='color: #721c24; background: #f8d7da; padding: 20px; border-radius: 5px; margin: 20px;'>";
    echo "Error: " . $e->getMessage();
    echo "</div>";
}
?>
