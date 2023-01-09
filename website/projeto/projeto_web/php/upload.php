<?php

session_start();
require_once '../api/config.php';
require_once '../api/core.php';

$debug = '';
$html = '';

$form_submited = filter_input(INPUT_POST, 'submit');

if ($form_submited) {
    $upload_name = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_FILENAME));
    $debug .= "\t Uploaded name: " . $upload_name . "\n";

    $upload_extension = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
    $debug .= "\t Uploaded extension: " . $upload_extension . "\n";

    $upload_type = $_FILES["fileToUpload"]["type"];
    $debug .= "\t Uploaded type: " . $upload_type . "\n";

    $upload_tmp_name = $_FILES["fileToUpload"]["tmp_name"];
    $debug .= "\t Uploaded tmp_name: " . $upload_tmp_name . "\n";

    $upload_error = $_FILES["fileToUpload"]["error"];
    $debug .= "\t Uploaded error: " . $upload_error . "\n";

    $upload_size = $_FILES["fileToUpload"]["size"];
    $debug .= "\t Uploaded size: " . $upload_size . "\n";

    $filename = AVATAR_PATH . slugify($upload_name) . '.' . $upload_extension;
    $dbfilename = slugify($upload_name) . '.' . $upload_extension;

    if (is_file($filename) || is_dir($filename)) {
        $debug .= "File already exists on server: " . $filename . "\n";
        $html .= '<div class="alert alert-error">Ficheiro já existe: <b>' . $filename . '</b></div>';
    } else {
        if (@move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $filename)) {
            $debug .= "New file uploaded: " . $filename . "\n";
            $html .= '<div class="alert alert-error">Ficheiro enviado com sucesso: <b>' . $filename . '</b></div>';
            $pdo = connectDB($db);
            $sql = "UPDATE user
                    SET Avatar = :AVATAR
                    WHERE ID = :REST_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":AVATAR", $dbfilename, PDO::PARAM_STR);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
            $stmt->execute();
            $_SESSION['avatar'] = $dbfilename;

            header('Location: ./dashboard/dashboard.php?m=dashboard&a=settings');
            exit();
        } else {
            $debug .= "Error: " . error_get_last() . "\n";
            die();
        }
    }
}
