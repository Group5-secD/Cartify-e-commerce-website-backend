<?php
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

require_once '../../config/db.php';
require_once '../../helpers/upload.php';

// Sanitize inputs safely
$username = htmlspecialchars(trim($_POST['username'] ?? ''));
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';

// Validation
if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(["error" => "All fields are required"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "Invalid email"]);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(["error" => "Password must be at least 8 characters"]);
    exit;
}

// Check if email already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    echo json_encode(["error" => "Email already registered"]);
    exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Handle profile image (optional in ecommerce)
$profilePath = null;

if (!empty($_FILES['profile_picture']['name'])) {
    $upload = uploadFile($_FILES['profile_picture']);

    if (!$upload['success']) {
        echo json_encode(["error" => $upload['message']]);
        exit;
    }

    $profilePath = $upload['path'];
}

// Insert user (with role + timestamp)
try {
    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password, profile_picture, role, created_at)
        VALUES (?, ?, ?, ?, 'customer', NOW())
    ");

    $stmt->execute([$username, $email, $hashedPassword, $profilePath]);

    echo json_encode([
        "success" => true,
        "message" => "Registration successful",
        "user" => [
            "username" => $username,
            "email" => $email,
            "role" => "customer"
        ]
    ]);

}
catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Something went wrong"
    ]);
}
