<?php
require_once "../../config/cors.php";
require_once "../../config/database.php";

header("Content-Type: application/json");

$database = new Database();
$pdo = $database->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

$action = $_GET['action'] ?? '';

try {

    // ADD ADDRESS
    if ($action === "add") {

        $sql = "INSERT INTO addresses 
        (user_id, full_name, phone, city, subcity, house_no, is_default)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $data['user_id'],
            $data['full_name'],
            $data['phone'],
            $data['city'],
            $data['subcity'],
            $data['house_no'],
            0
        ]);

        echo json_encode(["message" => "Address added"]);
    }

    // EDIT ADDRESS
    elseif ($action === "edit") {

        $sql = "UPDATE addresses 
        SET full_name=?, phone=?, city=?, subcity=?, house_no=?
        WHERE id=?";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $data['full_name'],
            $data['phone'],
            $data['city'],
            $data['subcity'],
            $data['house_no'],
            $data['id']
        ]);

        echo json_encode(["message" => "Address updated"]);
    }

    // DELETE ADDRESS
    elseif ($action === "delete") {

        $stmt = $pdo->prepare("DELETE FROM addresses WHERE id=?");
        $stmt->execute([$data['id']]);

        echo json_encode(["message" => "Address deleted"]);
    }

    // SET DEFAULT
    elseif ($action === "default") {

        $pdo->prepare("UPDATE addresses SET is_default=0 WHERE user_id=?")
            ->execute([$data['user_id']]);

        $pdo->prepare("UPDATE addresses SET is_default=1 WHERE id=?")
            ->execute([$data['id']]);

        echo json_encode(["message" => "Default address updated"]);
    }

    // GET USER ADDRESSES
    elseif ($action === "list") {

        $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id=?");
        $stmt->execute([$data['user_id']]);

        echo json_encode($stmt->fetchAll());
    }

    else {
        echo json_encode(["message" => "Invalid action"]);
    }

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>