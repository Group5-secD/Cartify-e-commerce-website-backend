<?php
require_once __DIR__ . '/../../config/cors.php';
require_once 'middleware.php';

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'logged_in' => true,
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? null
    ]);
}
else {
    echo json_encode([
        'logged_in' => false
    ]);
}
?>
