<?php

require_once "config.php";
require_once "core.php";

$pdo = connectDB($db);

$device_mac = filter_input(INPUT_GET, 'device_mac', FILTER_SANITIZE_STRING);

$sql = "SELECT *
        FROM device
		WHERE MAC = :MAC LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":MAC", $device_mac, PDO::PARAM_STR);
$stmt->execute();

if ($stmt->rowCount() > 0) {

    $row = $stmt->fetch();

    if ($row['room_ID'] == NULL) {
        //Device not yet assigned to a room
        die("Device not yet assigned to a room");
    } else {
        echo $row['State'];
    }
    exit();
}