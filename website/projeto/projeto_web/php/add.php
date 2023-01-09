<?php

if (isset($_POST['add'])) {

    if (!isset($_SESSION['uid'])) {
        session_start();
    }
    require_once '../api/config.php';
    require_once '../api/core.php';

    $data = filter_input(INPUT_POST, 'add');
    if ($data) {
        $pdo = connectDB($db);

        $room_name = filter_input(INPUT_POST, 'room', FILTER_SANITIZE_STRING);
        $device_id = filter_input(INPUT_POST, 'device', FILTER_SANITIZE_NUMBER_INT);
        $device_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $device_icon = filter_input(INPUT_POST, 'icon', FILTER_SANITIZE_STRING);
        $device_consumption = filter_input(INPUT_POST, 'consumption', FILTER_SANITIZE_STRING);

        $errors = false;
        //Check if choosen room exists in db
        if (!$errors) {
            $sql = "SELECT *
                    FROM room
                    WHERE Name = :ROOM_NAME AND user_ID = :REST_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":ROOM_NAME", $room_name, PDO::PARAM_STR);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
            $stmt->execute();
            //If not create one
            if ($stmt->rowCount() != 1) {

                $sql = "INSERT INTO room(Name, user_ID)
                        VALUES(:ROOM_NAME, :REST_ID);";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":ROOM_NAME", $room_name, PDO::PARAM_STR);
                $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
                $stmt->execute();

                $sql = "INSERT INTO statistics( Day, Month, Year, Consumption, room_ID)
                VALUES ((SELECT DATE_FORMAT(CURRENT_DATE(),'%d')),
                        (SELECT DATE_FORMAT(CURRENT_DATE(),'%M')),
                        (SELECT DATE_FORMAT(CURRENT_DATE(),'%Y')),
                        '0', (SELECT MAX(ID) FROM room));";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                if ($_SESSION['selected_room'] == '') {

                    $sql = "SELECT *
                    FROM room
                    WHERE user_ID = :REST_ID Limit 1;";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
                    $stmt->execute();
                    $row = $stmt->fetch();
                    $_SESSION['selected_room'] = $row['ID'];
                }

                //Create Notification for new room
                $sql = "INSERT INTO log (Title, Description, Time, Date, Icon, user_ID)
                VALUES('New Room Added',
                (SELECT CONCAT('A new room named ',
                (SELECT CONCAT(:ROOM_NAME, ' was added to your house')))),
                (SELECT TIME_FORMAT(CURRENT_TIME, '%H:%i')),
                (SELECT DATE_FORMAT(CURRENT_DATE(),'%d %b.')),
                'home.svg', :REST_ID);";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":ROOM_NAME", $room_name, PDO::PARAM_STR);
                $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
                $stmt->execute();
            }
        }

        //Check if choosen room exists in db
        if (!$errors) {
            $sql = "SELECT *
                    FROM room
                    WHERE Name = :ROOM_NAME AND user_ID = :REST_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":ROOM_NAME", $room_name, PDO::PARAM_STR);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
            $stmt->execute();

            //Create a device in the chosen room
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch();

                if (in_array($row['Icon'], TYPE)) {
                    $type = 'output';
                } else {
                    $type = 'input';
                }

                $sql = "UPDATE device
                        SET Name = :DEVICE_NAME, room_ID = :ROOM_ID, Icon = :DEVICE_ICON, Type = :DEVICE_TYPE
                        WHERE ID = :DEVICE_ID;";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":DEVICE_NAME", $device_name, PDO::PARAM_STR);
                $stmt->bindValue(":ROOM_ID", $row['ID'], PDO::PARAM_INT);
                $stmt->bindValue(":DEVICE_ID", $device_id, PDO::PARAM_STR);
                $stmt->bindValue(":DEVICE_ICON", $device_icon, PDO::PARAM_STR);
                $stmt->bindValue(":DEVICE_TYPE", $type, PDO::PARAM_STR);
                $stmt->execute();

                if ($device_consumption) {
                    $sql = "UPDATE device
                    SET Consumption = :CONSUMPTION
                    WHERE ID = :DEVICE_ID;";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(":CONSUMPTION", $device_consumption, PDO::PARAM_STR);
                    $stmt->bindValue(":DEVICE_ID", $device_id, PDO::PARAM_STR);
                    $stmt->execute();
                }

                if ($_SESSION['selected_device'] == '') {

                    $sql = "SELECT device.ID
                            FROM device
                            INNER JOIN room
                            ON room.ID = device.room_ID
                            WHERE user_ID = :REST_ID Limit 1;";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
                    $stmt->execute();
                    $row = $stmt->fetch();
                    $_SESSION['selected_device'] = $row['ID'];
                }

                //Create Notification for new device
                $sql = "INSERT INTO log (Title, Description, Time, Date, device_ID, Icon, user_ID)
                VALUES('New Device Added',
                (SELECT CONCAT('A new device name ',
                (SELECT CONCAT(:DEVICE_NAME,
                (SELECT CONCAT(' was added to the ', :ROOM_NAME)))))),
                (SELECT TIME_FORMAT(CURRENT_TIME, '%H:%i')),
                (SELECT DATE_FORMAT(CURRENT_DATE(),'%d %b.')),
                :DEVICE_ID, :DEVICE_ICON, :REST_ID);";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":DEVICE_NAME", $device_name, PDO::PARAM_STR);
                $stmt->bindValue(":ROOM_NAME", $room_name, PDO::PARAM_STR);
                $stmt->bindValue(":DEVICE_ID", $device_id, PDO::PARAM_INT);
                $stmt->bindValue(":DEVICE_ICON", $device_icon, PDO::PARAM_STR);
                $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
                $stmt->execute();

                header('Location: ./dashboard/dashboard.php');
                exit();
            }
        }
    }
}

if (isset($_POST['add_room'])) {

    if (!isset($_SESSION['uid'])) {
        session_start();
    }
    require_once '../api/config.php';
    require_once '../api/core.php';

    $data = filter_input(INPUT_POST, 'add_room');
    if ($data) {
        $pdo = connectDB($db);

        $room_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

        $errors = false;
        //Check if choosen room exists in db
        if (!$errors) {
            $sql = "SELECT *
                    FROM room
                    WHERE Name = :ROOM_NAME AND user_ID = :REST_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":ROOM_NAME", $room_name, PDO::PARAM_STR);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
            $stmt->execute();
            //If not create one
            if ($stmt->rowCount() != 1) {

                $sql = "INSERT INTO room(Name, user_ID)
                        VALUES(:ROOM_NAME, :REST_ID);";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":ROOM_NAME", $room_name, PDO::PARAM_STR);
                $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
                $stmt->execute();

                $sql = "INSERT INTO statistics( Day, Month, Year, Consumption, room_ID)
                VALUES ((SELECT DATE_FORMAT(CURRENT_DATE(),'%d')),
                        (SELECT DATE_FORMAT(CURRENT_DATE(),'%M')),
                        (SELECT DATE_FORMAT(CURRENT_DATE(),'%Y')),
                        '0', (SELECT MAX(ID) FROM room));";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                if ($_SESSION['selected_room'] == '') {

                    $sql = "SELECT *
                    FROM room
                    WHERE user_ID = :REST_ID Limit 1;";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
                    $stmt->execute();
                    $row = $stmt->fetch();
                    $_SESSION['selected_room'] = $row['ID'];
                }

                //Create Notification for new room
                $sql = "INSERT INTO log (Title, Description, Time, Date, Icon, user_ID)
                VALUES('New Room Added',
                (SELECT CONCAT('A new room named ',
                (SELECT CONCAT(:ROOM_NAME, ' was added to your house')))),
                (SELECT TIME_FORMAT(CURRENT_TIME, '%H:%i')),
                (SELECT DATE_FORMAT(CURRENT_DATE(),'%d %b.')),
                'home.svg', :REST_ID);";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":ROOM_NAME", $room_name, PDO::PARAM_STR);
                $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
                $stmt->execute();
            }
        }

        header('Location: ./dashboard/dashboard.php');
        exit();
    }
}
