<?php
// Since we are using Live Server for the frontend, we need to allow port 5501.
$allowed_origins = [
    "http://localhost:3000",
    "http://127.0.0.1:5501",
    "http://localhost:5501",
    "http://127.0.0.1:5500",
    "http://localhost:5500"
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}
?>
