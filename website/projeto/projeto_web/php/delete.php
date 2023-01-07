<?php

if (!isset($_SESSION['uid'])) {
    session_start();
}
require_once '../api/config.php';
require_once '../api/core.php';

//DELETE NOTIFICATIONS
if (isset($_GET['action'])) {
    $action = filter_input(INPUT_GET, 'action');
    if ($action == "delete_notifications") {
        $pdo = connectDB($db);

        $sql = "DELETE FROM log
            WHERE user_ID = :REST_ID;";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
        $stmt->execute();
    }

    if ($action == "delete_notification") {

        $notification_ID = $_GET['notification_ID'];

        $pdo = connectDB($db);

        $sql = "DELETE log
            FROM log
            WHERE user_ID = :REST_ID AND ID = :NOTIFICATION_ID;";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
        $stmt->bindValue(":NOTIFICATION_ID", $notification_ID, PDO::PARAM_INT);
        $stmt->execute();
    }
}


//DELETE DEVICE
if (isset($_POST['delete'])) {

    $data = filter_input(INPUT_POST, 'delete');
    if ($data) {
        $pdo = connectDB($db);

        $device_id = filter_input(INPUT_POST, 'device', FILTER_SANITIZE_NUMBER_INT);

        $errors = false;

        if (!$errors) {

            //Store old device name
            $sql = "SELECT Name
                    FROM device
                    WHERE ID = :DEVICE_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":DEVICE_ID", $device_id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();
            $oldname = $row['Name'];
            //Store room name
            $sql = "SELECT room.Name
                    FROM device
                    INNER JOIN room ON room.ID = device.room_ID
                    WHERE device.ID = :DEVICE_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":DEVICE_ID", $device_id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();
            $roomname = $row['Name'];

            //Update Device to the default values
            $sql = "UPDATE device
                    SET Name = 'Default', room_ID = NULL, Icon = NULL, Type = NULL, Consumption = 0
                    WHERE ID = :DEVICE_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":DEVICE_ID", $device_id, PDO::PARAM_INT);
            $stmt->execute();

            $sql = "SELECT *
                    FROM device
                    INNER JOIN room
                    ON room.ID = device.room_ID
                    WHERE user_ID = :REST_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            if (!$stmt->rowCount() > 0) {
                $_SESSION['selected_device'] = '';
            }

            //Create Notification for the removed device
            $sql = "INSERT INTO log (Title, Description, Time, Date, Icon, user_ID)
            VALUES('Device Removed',
            (SELECT CONCAT('The device ',
            (SELECT CONCAT(:OLD_NAME,
            (SELECT CONCAT(' was removed from the ', :ROOM_NAME)))))),
            (SELECT TIME_FORMAT(CURRENT_TIME, '%H:%i')),
            (SELECT DATE_FORMAT(CURRENT_DATE(),'%d %b.')),
            'device-alt.svg', :REST_ID);";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":OLD_NAME", $oldname, PDO::PARAM_STR);
            $stmt->bindValue(":ROOM_NAME", $roomname, PDO::PARAM_STR);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);

            $stmt->execute();

            header('Location: ./dashboard/dashboard.php');
            exit();
        }
    }
}

//DELETE ROOM
if (isset($_POST['delete_room'])) {

    $data = filter_input(INPUT_POST, 'delete_room');
    if ($data) {
        $pdo = connectDB($db);

        $room_id = filter_input(INPUT_POST, 'room', FILTER_SANITIZE_NUMBER_INT);

        $errors = false;

        if (!$errors) {

            //Store old room name
            $sql = "SELECT Name
                    FROM room
                    WHERE ID = :ROOM_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":ROOM_ID", $room_id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();
            $oldname = $row['Name'];

            //Remove devices from room
            $sql = "UPDATE device
                    SET Name = 'Default', room_ID = NULL, Icon = NULL, Type = NULL
                    WHERE room_ID = :ROOM_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":ROOM_ID", $room_id, PDO::PARAM_INT);
            $stmt->execute();

            //Remove room and statistics from db
            $sql = "DELETE FROM statistics
                    WHERE room_ID = :ROOM_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":ROOM_ID", $room_id, PDO::PARAM_INT);
            $stmt->execute();
            $sql = "DELETE FROM room
                    WHERE ID = :ROOM_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":ROOM_ID", $room_id, PDO::PARAM_INT);
            $stmt->execute();

            //Reset session values if no rooms exist
            $sql = "SELECT *, room.ID AS rID, device.ID AS dID
                    FROM room
                    INNER JOIN device
                    ON device.room_ID = room.ID
                    WHERE user_ID = :REST_ID
                    ORDER BY room.ID ASC LIMIT 1;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            if ($stmt->rowCount() > 0) {
                $_SESSION['selected_room'] = $row['rID'];
                $_SESSION['selected_device'] = $row['dID'];
            } else {
                $_SESSION['selected_room'] = '';
                $_SESSION['selected_device'] = '';
                $_SESSION['selected_year'] = '';
                $_SESSION['selected_month'] = '';
            }

            //Create Notification for the removed room
            $sql = "INSERT INTO log (Title, Description, Time, Date, Icon, user_ID)
            VALUES('Room Removed',
            (SELECT CONCAT('The room ',
            (SELECT CONCAT(:OLD_NAME, ' was removed from your house')))),
            (SELECT TIME_FORMAT(CURRENT_TIME, '%H:%i')),
            (SELECT DATE_FORMAT(CURRENT_DATE(),'%d %b.')),
            'home.svg', :REST_ID);";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":OLD_NAME", $oldname, PDO::PARAM_STR);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);

            $stmt->execute();

            header('Location: ./dashboard/dashboard.php');
            exit();
        }
    }
}

//DELETE AVATAR
if ($_GET['delete'] == "avatar") {

    $filename = '';

    $pdo = connectDB($db);
    $sql = "SELECT Avatar
        FROM user
        WHERE ID = :REST_ID;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
    $stmt->execute();

    $filename = $stmt->fetch();

    if ($filename['Avatar']) {

        $filepath = AVATAR_PATH . $filename['Avatar'];

        if (is_file($filepath)) {
            unlink($filepath);

            $pdo = connectDB($db);
            $sql = "UPDATE user
                SET Avatar = 'default.png'
                WHERE ID = :REST_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    $_SESSION['avatar'] = 'default.png';
    header('Location: ./dashboard/dashboard.php?m=dashboard&a=settings');
    exit();
}
