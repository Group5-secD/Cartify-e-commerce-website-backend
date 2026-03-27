<?php
require_once '../../config/Database.php';

try {
    // Get connection
    $conn = new Database();
    $pdo = $conn->getConnection();
    
    if($pdo == null) {
        echo "PDO IS NULL";
    } else {
        echo "✅ Connected to database successfully!<br><br>";
    }
    
    // Test 1: Insert a new user
    $username = "testuser";
    $email = "testuser@gmail.com";
    $password = password_hash("12345", PASSWORD_DEFAULT);
    $profilePicture = NULL;  // Or provide a URL
    $created_at = date('Y-m-d H:i:s');  // Current timestamp
    
    $sql = "INSERT INTO users (username, email, password, profilePicture, created_at) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$username, $email, $password, $profilePicture, $created_at]);
    
    if ($result) {
        $lastId = $pdo->lastInsertId();
        echo "✅ User inserted successfully! ID: " . $lastId . "<br><br>";
    }
    
    // Test 2: Retrieve and display users
    echo "📋 Users in database:<br>";
    $stmt = $pdo->query("SELECT id, username, email, created_at FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($users as $user) {
        echo "- ID: " . $user['id'] . " | ";
        echo "Username: " . $user['username'] . " | ";
        echo "Email: " . $user['email'] . " | ";
        echo "Created: " . $user['created_at'] . "<br>";
    }
    
    // Close connection
    $pdo = null;
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>