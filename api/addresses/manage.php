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
        (country, city, zip_code, street_address, user_id)
        VALUES (?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $data['country'],
            $data['city'],
            $data['zip_code'],
            $data['street_address'],
            $data['user_id']
        ]);

        echo json_encode(["message" => "Address added"]);
    }

    // EDIT ADDRESS
    elseif ($action === "edit") {

        $sql = "UPDATE addresses
                SET country=?, city=?, zip_code=?, street_address=?
                WHERE id=?";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $data['country'],
            $data['city'],
            $data['zip_code'],
            $data['street_address'],
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

    // LIST USER ADDRESSES
    elseif ($action === "list") {

        $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id=?");
        $stmt->execute([$data['user_id']]);

        echo json_encode($stmt->fetchAll());
    }

    // SET DEFAULT ADDRESS
    elseif ($action === "default") {

        $pdo->prepare("UPDATE addresses SET is_default = 0 WHERE user_id=?")
            ->execute([$data['user_id']]);

        $pdo->prepare("UPDATE addresses SET is_default = 1 WHERE id=?")
            ->execute([$data['id']]);

        echo json_encode(["message" => "Default address updated"]);
    }

    else {
        echo json_encode(["message" => "Invalid action"]);
    }

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>