<?php
/*require_once __DIR__ . '/../../config/cors.php';
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
*/
session_start();
header("Content-Type: application/json");

require_once "../../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];
    
    $db = new Database();
    $conn = $db->connect();
    
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);

    $user = $stmt->fetch();

    if ($user) {

        // check password
        if (password_verify($password, $user["password"])) {

            // create session
            $_SESSION["user_id"] = $user["id"];

            echo json_encode([
                "status" => "success",
                "message" => "Login successful"
            ]);

        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Wrong password"
            ]);
        }

    } else {
        echo json_encode([
            "status" => "error",
            "message" => "User not found"
        ]);
    }
}

?>
