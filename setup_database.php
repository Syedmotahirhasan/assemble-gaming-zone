<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Create connection
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Drop existing database to ensure clean setup
    $sql = "DROP DATABASE IF EXISTS assemble_gaming";
    $pdo->exec($sql);
    
    // Create database
    $sql = "CREATE DATABASE assemble_gaming";
    $pdo->exec($sql);
    echo "Database created successfully<br>";
    
    // Select the database
    $pdo->exec("USE assemble_gaming");
    
    // Drop existing tables if they exist
    $pdo->exec("DROP TABLE IF EXISTS users");
    $pdo->exec("DROP TABLE IF EXISTS games");
    $pdo->exec("DROP TABLE IF EXISTS reviews");
    $pdo->exec("DROP TABLE IF EXISTS contact_messages");
    
    // Create users table
    $sql = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // Create a test user
    $username = "test";
    $email = "test@test.com";
    $password = password_hash("test123", PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $email, $password]);

    // Create games table
    $sql = "CREATE TABLE games (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100) NOT NULL,
        image_url VARCHAR(255),
        platforms VARCHAR(255),
        rating DECIMAL(3,1),
        release_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    // Create reviews table
    $sql = "CREATE TABLE reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT,
        title VARCHAR(100) NOT NULL,
        content TEXT,
        score DECIMAL(3,1),
        platforms VARCHAR(255),
        author_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
        FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    
    // Create contact_messages table
    $sql = "CREATE TABLE contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(200),
        message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    
    echo "Database setup completed successfully!<br>";
    echo "Test account created:<br>";
    echo "Email: test@test.com<br>";
    echo "Password: test123<br>";
    echo "<a href='login.php'>Go to Login Page</a>";

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
