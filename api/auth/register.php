<?php

    //  HTTP Headers setup
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Content-Type: application/json");

    require_once 'upload_helper.php';

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    if($_SERVER["REQUEST_METHOD"] === "POST") {
        // Assigned person do validation and other things here 


        if(isset($_POST["username"])) {

            $username = $_POST["username"];
            $file = $_FILES["profile-image"];

            $imageUploadingResult = uploadProfile($username, $file);
            if($imageUploadingResult === "error") {
                echo json_encode([
                    "status" => "error", 
                    "field" => "profile-image", 
                    "message" => "unsupported file"
                ]);

                exit();
            } else {

                echo json_encode([
                        "status" => "success", 
                        "field" => "profile-image", 
                        "message" => "Profile image uploaded successfully"
                    ]);
            }
        }
    }
?>