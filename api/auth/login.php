<?php
/*require_once __DIR__ . '/../../config/cors.php'; session_start();
 header('Content-Type: application/json');
 // This is a dummy login for testing session logic. // In a real app, you would verify credentials against the database. $_SESSION['user_id'] = 1;  $_SESSION['username'] = 'testuser';
 echo json_encode([
 'message' => 'Logged in successfully (Mock)',
 'user_id' => $_SESSION['user_id'] ]);  */
require_once __DIR__ . '/../../config/cors.php';
session_start();
header("Content-Type: application/json");

require_once __DIR__ . "/../../config/Database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Read JSON payload if frontend sends json
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $_POST["email"] ?? $data["email"] ?? '';
    $password = $_POST["password"] ?? $data["password"] ?? '';

    // Mock mode removed, using real database connection below
    $db = new Database();
    $conn = $db->getConnection();

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);

    $user = $stmt->fetch();

    if ($user) {

        // check password
        if (password_verify($password, $user["password"])) {

            // create session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["profile_picture"] = $user["profilePicture"];

            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "username" => $user["username"],
                "profile_picture" => $user["profilePicture"]
            ]);

        }
        else {
            echo json_encode([
                "status" => "error",
                "message" => "Wrong password"
            ]);
        }

    }
    else {
        echo json_encode([
            "status" => "error",
            "message" => "User not found"
        ]);
    }
}

?>
