<?php 

require_once "../../config/Database.php";

class Register {
    private $pdo;

    private $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/";
    private $emailRegex = "/^[^\s@]+@[^\s@]+\.[^\s@]+$/";
    private $usernameRegex = "/^[a-zA-Z0-9]{3,16}$/";

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    // USERNAME VALIDATION
    public function testUsername($username) {
        try {
            $query = "SELECT username FROM users WHERE username = :username";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([":username" => $username]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return [
                    "status" => "error",
                    "field" => "reg-username", 
                    "message" => "Username already exists"
                ];
            }

            if (preg_match($this->usernameRegex, $username)) {
                return [
                    "status" => "success",
                    "message" => "Valid username"
                ];
            }

            return [
                "status" => "error",
                "field" => "reg-username",
                "message" => "Username must be 3–16 alphanumeric characters"
            ];

        } catch(PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // EMAIL VALIDATION
    public function testEmail($email) {
        try {
            $query = "SELECT email FROM users WHERE email = :email";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([":email" => $email]);

            if ($stmt->fetch()) {
                return [
                    "status" => "error",
                    "field" => "reg-email",
                    "message" => "Email already exists"
                ];
            }

            if (preg_match($this->emailRegex, $email)) {
                return [
                    "status" => "success",
                    "message" => "Valid email"
                ];
            }

            return [
                "status" => "error",
                "field" => "reg-email",
                "message" => "Invalid email format"
            ];

        } catch(PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // PASSWORD VALIDATION
    public function testPassword($password) {
        if (preg_match($this->passwordRegex, $password)) {
            return [
                "status" => "success",
                "message" => "Valid password"
            ];
        }

        return [
            "status" => "error",
            "field" => "reg-password",
            "message" => "Password must contain uppercase, lowercase, number & special character"
        ];
    }

    // CONFIRM PASSWORD
    public function confirmPassword($password, $confirm) {
        if ($password === $confirm) {
            return [
                "status" => "success",
                "message" => "Password matched"
            ];
        }

        return [
            "status" => "error",
            "field" => "reg-confirm-password",
            "message" => "Passwords do not match"
        ];
    }

    // REGISTER USER
    public function registerUser($username, $password, $email) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO users (username, email, password, created_at) 
                      VALUES (:username, :email, :password, :created_at)";

            $stmt = $this->pdo->prepare($query);

            $stmt->execute([
                ":username" => $username,
                ":email" => $email,
                ":password" => $hashedPassword,
                ":created_at" => date('Y-m-d H:i:s')
            ]);

            return [
                "status" => "success",
                "user_id" => $this->pdo->lastInsertId()
            ];

        } catch(PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // PROFILE IMAGE UPLOAD
    public function uploadProfile($image, $user_id) {
        $allowedExtensions = ["jpg", "jpeg", "png", "gif", "webp"];

        if (!$image || $image["error"] !== 0) {
            return ["status" => "error", "message" => "Image upload failed"];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $image["tmp_name"]);

        $parts = explode("/", $mimeType);
        $fileExt = strtolower(end($parts));

        if (!in_array($fileExt, $allowedExtensions)) {
            return ["status" => "error", "field" => "user-profile", "message" => "Invalid image type"];
        }

        if ($image["size"] > 2 * 1024 * 1024) {
            return ["status" => "error", "field" => "user-profile", "message" => "Image too large"];
        }

        $folder = "Profiles";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $fileName = uniqid() . "." . $fileExt;
        $path = $folder . "/" . $fileName;

        move_uploaded_file($image["tmp_name"], $path);
        $stmt = $this->pdo->prepare("UPDATE users SET profilePicture = :profilePicture WHERE id = :user_id");
        $stmt->execute([":profilePicture" => $path, ":user_id" => $user_id]);

        return ["status" => "success", "path" => $path];
    }
}