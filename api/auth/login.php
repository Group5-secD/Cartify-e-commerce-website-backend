<?php
require_once __DIR__ . '/../../config/cors.php';
session_start();

header('Content-Type: application/json');

// This is a dummy login for testing session logic.
// In a real app, you would verify credentials against the database.
$_SESSION['user_id'] = 1; 
$_SESSION['username'] = 'testuser';

echo json_encode([
    'message' => 'Logged in successfully (Mock)',
    'user_id' => $_SESSION['user_id']
]);
?>
