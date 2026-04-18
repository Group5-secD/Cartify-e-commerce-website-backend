<?php
require_once '../config/Database.php';

try {
    // Get connection
    $conn = new Database();
    $pdo = $conn->getConnection();

    if ($pdo == null) {
        echo "PDO IS NULL";
    }
    else {
        echo "Connected to database successfully!<br><br>";
    }

    // Test 1: Insert a new user
    $username = "testuser_" . time(); // Unique username for each test
    $email = "testuser_" . time() . "@gmail.com";
    $password = password_hash("123456", PASSWORD_DEFAULT);
    $profilePicture = NULL;
    $created_at = date('Y-m-d H:i:s');

    $sql = "INSERT INTO users (username, email, password, profilePicture, created_at) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$username, $email, $password, $profilePicture, $created_at]);

    if ($result) {
        $lastId = $pdo->lastInsertId();
        echo "<b>Test 1 Success:</b> User inserted successfully! ID: " . $lastId . "<br>";
    }

    // Test 2: Retrieve and display users
    echo "<br><b>Test 2:</b> Users in database:<br>";
    $stmt = $pdo->query("SELECT id, username, email, created_at FROM users ORDER BY id DESC LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        echo "- ID: " . $user['id'] . " | ";
        echo "Username: " . $user['username'] . " | ";
        echo "Email: " . $user['email'] . "<br>";
    }

    // Close connection
    $pdo = null;

}
catch (PDOException $e) {
    echo "<b>Error:</b> " . $e->getMessage();
}
?>
