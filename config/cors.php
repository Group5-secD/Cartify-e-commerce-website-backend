<?php
// Get the origin of the request
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Just for local development: Allow any localhost or 127.0.0.1 port
if (str_contains($origin, 'localhost') || str_contains($origin, '127.0.0.1')) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept");
header("Access-Control-Allow-Credentials: true");
header("Vary: Origin");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}
?>
