<?php
require_once 'config/Database.php';
try {
    $db = new Database();
    $pdo = $db->getConnection();
    echo "DB Connection Success\n";

    // Create users table if not exists to ensure testing works smoothly
    $query = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        profile_picture VARCHAR(255),
        role VARCHAR(50) DEFAULT 'customer',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($query);
    echo "Table 'users' ready.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
