<?php

function uploadProfile($username, $image)
{

    $allowedExtensions = ["jpg", "jpeg", "png", "gif", "webp"];

    if ($image["error"] === 0) {

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $image["tmp_name"]);

        $fileExt = strtolower(end(explode("/", $mimeType)));
        $tempLocation = $image["tmp_name"];

        if (!in_array($fileExt, $allowedExtensions)) {
            return "error";
        }

        $folder = "Profiles";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $destinationDirectory = $folder . "/" . $username . "." . $fileExt;
        move_uploaded_file($tempLocation, $destinationDirectory);

        return "success";
    }
}
?>