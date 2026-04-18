<?php
require_once '../config/Database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();

    echo "Checking 'users' table...<br>";

    // add email column and profilePicture column if missing
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS email VARCHAR(100) UNIQUE NOT NULL AFTER username");
    echo "Added 'email' column (if it was missing).<br>";

    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS profilePicture VARCHAR(500) NULL AFTER password");
    echo "Added 'profilePicture' column (if it was missing).<br>";

    echo "<br><b>Database schema fixed!</b> Now you can run the Registration form perfectly.";

} catch (PDOException $e) {
    echo "Error fixing database: " . $e->getMessage();
}
?>