<?php 

session_start();

require_once '../api/config.php';
require_once '../api/core.php';

if ($_GET['room_ID']) {
        $_SESSION['selected_room'] = $_GET['room_ID'];
        $pdo = connectDB($db);
        $sql = "SELECT ID FROM device
                WHERE room_ID = :ROOM_ID
                ORDER BY ID ASC
                LIMIT 1;";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":ROOM_ID", $_GET['room_ID'], PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($stmt->rowCount() > 0) {
                $_SESSION['selected_device'] = $row['ID'];
        } else {
                //No device
                $_SESSION['selected_device'] = '';
        }
}
if ($_GET['device_ID']) {
        $_SESSION['selected_device'] = $_GET['device_ID'];
}
if ($_GET['month_NAME']) {
        $_SESSION['selected_month'] = $_GET['month_NAME'];
}
if ($_GET['year_NAME']) {
        $_SESSION['selected_year'] = $_GET['year_NAME'];
}
if ($_GET['state']) {

        $pdo = connectDB($db);
        $device_ID = $_GET['device_ID'];
        $state = $_GET['state'];

        $sql = "UPDATE device
        SET State = :STATE
        WHERE ID = :DEVICE_ID;";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":DEVICE_ID", $device_ID, PDO::PARAM_INT);
        $stmt->bindValue(":STATE", $state, PDO::PARAM_STR);
        $stmt->execute();

        //Create notification
        $sql = "INSERT INTO log (Title, Description, Time, Date, device_ID, Icon, user_ID)
        VALUES((SELECT CONCAT('Turned ', device.State) FROM device WHERE device.ID = :DEVICE_ID),
        (SELECT CONCAT(device.Name,
        (SELECT CONCAT(
        (SELECT CONCAT(' in ', LOWER(room.Name)) FROM room INNER JOIN device ON device.room_ID = room.ID WHERE device.ID = :DEVICE_ID), 
        (SELECT CONCAT(' was turned ', device.State) FROM device WHERE device.ID = :DEVICE_ID)))) FROM device WHERE device.ID = :DEVICE_ID), 
        (SELECT TIME_FORMAT(CURRENT_TIME, '%H:%i')),
        (SELECT DATE_FORMAT(CURRENT_DATE(),'%d %b.')),
        (SELECT ID FROM device WHERE ID = :DEVICE_ID), (SELECT Icon FROM device WHERE ID = :DEVICE_ID), :REST_ID);";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":DEVICE_ID", $device_ID, PDO::PARAM_INT);
        $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
        $stmt->execute();
        exit();
}
if ($_GET['Input']) {

        $pdo = connectDB($db);
        $input = $_GET['Input'];
        $device_ID = $_GET['ID'];

        $sql = "UPDATE actions as a
        INNER JOIN device_has_actions as da
        ON da.actions_ID = a.ID
        INNER JOIN device as d
        ON d.device_has_actions_ID = da.ID
        SET a.Input = :INPUT, a.Brightness = :INPUT
        WHERE d.ID = :DEVICE_ID;";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":DEVICE_ID", $device_ID, PDO::PARAM_INT);
        $stmt->bindValue(":INPUT", $input, PDO::PARAM_INT);
        $stmt->execute();
}
