<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "includes/config.php";

try {
    // Check if database exists
    $databases = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<h3>Step 1: Check Database</h3>";
    if (in_array('assemble_gaming', $databases)) {
        echo "✅ Database 'assemble_gaming' exists<br>";
    } else {
        echo "❌ Database 'assemble_gaming' does not exist<br>";
    }

    // Check if tables exist
    echo "<h3>Step 2: Check Tables</h3>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (in_array('users', $tables)) {
        echo "✅ Table 'users' exists<br>";
        
        // Check users table structure
        echo "<h3>Step 3: Check Users Table Structure</h3>";
        $columns = $pdo->query("DESCRIBE users")->fetchAll();
        foreach ($columns as $column) {
            echo "Column '{$column['Field']}': {$column['Type']}<br>";
        }

        // Check if test user exists
        echo "<h3>Step 4: Check Test User</h3>";
        $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE email = ?");
        $stmt->execute(['test@test.com']);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "✅ Test user exists:<br>";
            echo "- Username: {$user['username']}<br>";
            echo "- Email: {$user['email']}<br>";
        } else {
            echo "❌ Test user not found<br>";
            
            // Create test user
            echo "<h3>Creating Test User...</h3>";
            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                'test',
                'test@test.com',
                password_hash('test123', PASSWORD_DEFAULT)
            ]);
            
            if ($result) {
                echo "✅ Test user created successfully!<br>";
                echo "Email: test@test.com<br>";
                echo "Password: test123<br>";
            }
        }
    } else {
        echo "❌ Table 'users' does not exist<br>";
    }

    echo "<br><hr><br>";
    echo "<a href='login.php' style='padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Try Login Now</a>";
    echo " with test@test.com / test123";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
