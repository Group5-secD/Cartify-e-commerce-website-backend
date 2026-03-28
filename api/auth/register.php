<?php

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "Register.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = htmlspecialchars(trim($_POST['username'])) ?? null;
    $email = $_POST["email"] ?? null;
    $password = $_POST["password"] ?? null;
    $confirm = $_POST["confirm-password"] ?? null;
    $file = $_FILES["profile-image"] ?? null;

    $register = new Register();

    // Validate username
    $res = $register->testUsername($username);
    if ($res["status"] === "error") exit(json_encode($res));

    // Validate email
    $res = $register->testEmail($email);
    if ($res["status"] === "error") exit(json_encode($res));

    // Validate password
    $res = $register->testPassword($password);
    if ($res["status"] === "error") exit(json_encode($res));

    // Confirm password
    $res = $register->confirmPassword($password, $confirm);
    if ($res["status"] === "error") exit(json_encode($res));

    // Register user
    $result = $register->registerUser($username, $password, $email, $upload["path"]);

    if ($result["status"] === "error") {
        exit(json_encode($result));
    }

    $user_id = $result["user_id"];

    // Upload image if exists
    if (!$file === null) {
        $upload = $register->uploadProfile($file, $user_id);

        if ($upload["status"] === "error") {
            exit(json_encode($upload));
        }
    }

    echo json_encode([
        "status" => "success",
        "message" => "User registered successfully"
    ]);
}