<?php

if (!isset($_SESSION['uid'])) {
    session_start();
}
require_once '../api/config.php';
require_once '../api/core.php';

//EDIT DEVICE
if (isset($_POST['edit'])) {

    $data = filter_input(INPUT_POST, 'edit');
    if ($data) {
        $pdo = connectDB($db);

        $device_id = filter_input(INPUT_POST, 'device', FILTER_SANITIZE_NUMBER_INT);
        $device_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $device_icon = filter_input(INPUT_POST, 'icon', FILTER_SANITIZE_STRING);
        $device_consumption = filter_input(INPUT_POST, 'consumption', FILTER_SANITIZE_STRING);

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

            //Update Device with the new values
            if (in_array($device_icon, TYPE)) {
                $type = 'output';
            } else {
                $type = 'input';
            }
            $sql = "UPDATE device
                    SET Name = :DEVICE_NAME, Icon = :DEVICE_ICON, Consumption = :CONSUMPTION, Type = :DEVICE_TYPE
                    WHERE ID = :DEVICE_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":DEVICE_NAME", $device_name, PDO::PARAM_STR);
            $stmt->bindValue(":DEVICE_ICON", $device_icon, PDO::PARAM_STR);
            $stmt->bindValue(":CONSUMPTION", $device_consumption, PDO::PARAM_STR);
            $stmt->bindValue(":DEVICE_TYPE", $type, PDO::PARAM_STR);
            $stmt->bindValue(":DEVICE_ID", $device_id, PDO::PARAM_INT);
            $stmt->execute();

            //Create Notification for the edited device
            $sql = "INSERT INTO log (Title, Description, Time, Date, Icon, user_ID)
            VALUES('Device Edited',
            (SELECT CONCAT('The name of the device ',
            (SELECT CONCAT(:OLD_NAME,
            (SELECT CONCAT(' in the ',
            (SELECT CONCAT(:ROOM_NAME,
            (SELECT CONCAT(' was changed to ', :DEVICE_NAME)))))))))),
            (SELECT TIME_FORMAT(CURRENT_TIME, '%H:%i')),
            (SELECT DATE_FORMAT(CURRENT_DATE(),'%d %b.')),
            :DEVICE_ICON, :REST_ID);";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":OLD_NAME", $oldname, PDO::PARAM_STR);
            $stmt->bindValue(":ROOM_NAME", $roomname, PDO::PARAM_STR);
            $stmt->bindValue(":DEVICE_NAME", $device_name, PDO::PARAM_STR);
            $stmt->bindValue(":DEVICE_ICON", $device_icon, PDO::PARAM_STR);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);

            $stmt->execute();
        }
    }
}

//EDIT ROOM
if (isset($_POST['edit_room'])) {

    $data = filter_input(INPUT_POST, 'edit_room');
    if ($data) {
        $pdo = connectDB($db);

        $room_id = filter_input(INPUT_POST, 'room', FILTER_SANITIZE_NUMBER_INT);
        $room_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

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

            //Update Room with the new values
            $sql = "UPDATE room
                    SET Name = :ROOM_NAME
                    WHERE ID = :ROOM_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":ROOM_NAME", $room_name, PDO::PARAM_STR);
            $stmt->bindValue(":ROOM_ID", $room_id, PDO::PARAM_INT);
            $stmt->execute();

            //Create Notification for the edited room
            $sql = "INSERT INTO log (Title, Description, Time, Date, Icon, user_ID)
            VALUES('Room Edited',
            (SELECT CONCAT('The name of the room ',
            (SELECT CONCAT(:OLD_NAME,
            (SELECT CONCAT(' was changed to ', :ROOM_NAME)))))),
            (SELECT TIME_FORMAT(CURRENT_TIME, '%H:%i')),
            (SELECT DATE_FORMAT(CURRENT_DATE(),'%d %b.')),
            'home.svg', :REST_ID);";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":OLD_NAME", $oldname, PDO::PARAM_STR);
            $stmt->bindValue(":ROOM_NAME", $room_name, PDO::PARAM_STR);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);

            $stmt->execute();
        }
    }
}

//MOVE DEVICE TO DIFFERENT ROOM
if (isset($_POST['move'])) {

    $data = filter_input(INPUT_POST, 'move');
    if ($data) {
        $pdo = connectDB($db);

        $device_id = filter_input(INPUT_POST, 'device', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $device_Str = implode("', '", $device_id);
        $room_name = filter_input(INPUT_POST, 'room', FILTER_SANITIZE_STRING);

        $errors = false;

        if (!$errors) {

            //Get chosen devices names
            $sql = "SELECT Name
                    FROM device
                    WHERE ID IN ('$device_Str')";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":ROOM_NAME", $room_name, PDO::PARAM_STR);
            $stmt->execute();

            while ($row = $stmt->fetch()) {
                $array[] = $row['Name'];
            }
            $devices = implode(', ', $array);

            //Move device to the new room
            $sql = "UPDATE device
                    SET room_ID = (SELECT ID FROM room WHERE Name = :ROOM_NAME)
                    WHERE ID IN ('$device_Str')";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":ROOM_NAME", $room_name, PDO::PARAM_STR);
            $stmt->execute();

            //Create Notification for the edited device

            if (count($array) > 1) {
                $str = 'The devices ';
                $str2 = ' were moved to the ';
            } else {
                $str = 'The device ';
                $str2 = ' was moved to the ';
            }

            $sql = "INSERT INTO log (Title, Description, Time, Date, Icon, user_ID)
            VALUES('Device(s) Moved',
            (SELECT CONCAT(:STRING ,
            (SELECT CONCAT(:DEVICE_NAMES,
            (SELECT CONCAT(:STRING2 , :ROOM_NAME)))))),
            (SELECT TIME_FORMAT(CURRENT_TIME, '%H:%i')),
            (SELECT DATE_FORMAT(CURRENT_DATE(),'%d %b.')),
            'home.svg', :REST_ID);";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":DEVICE_NAMES", $devices, PDO::PARAM_STR);
            $stmt->bindValue(":ROOM_NAME", $room_name, PDO::PARAM_STR);
            $stmt->bindValue(":STRING", $str, PDO::PARAM_STR);
            $stmt->bindValue(":STRING2", $str2, PDO::PARAM_STR);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);

            $stmt->execute();
        }
    }
}

//EDIT PROFILE NAME
if (isset($_POST['edit_name'])) {
    if ($_POST['edit_name'] != '') {
        $data = filter_input(INPUT_POST, 'edit_name');
        if ($data) {
            $pdo = connectDB($db);

            $name = filter_input(INPUT_POST, 'profile_name', FILTER_SANITIZE_STRING);

            $errors = false;

            if (!$errors) {

                //Store old room name
                $oldname = $_SESSION['name'];

                //Update profile name
                $sql = "UPDATE user
                    SET Name = :PROFILE_NAME
                    WHERE ID = :REST_ID;";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":PROFILE_NAME", $name, PDO::PARAM_STR);
                $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
                $stmt->execute();

                //Create Notification for the name update

                $sql = "INSERT INTO log (Title, Description, Time, Date, Icon, user_ID)
            VALUES('Profile Name Changed',
            (SELECT CONCAT('Your profile name was changed from ' ,
            (SELECT CONCAT(:OLD_NAME,
            (SELECT CONCAT(' to ' , :PROFILE_NAME)))))),
            (SELECT TIME_FORMAT(CURRENT_TIME, '%H:%i')),
            (SELECT DATE_FORMAT(CURRENT_DATE(),'%d %b.')),
            'user.svg', :REST_ID);";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":OLD_NAME", $oldname, PDO::PARAM_STR);
                $stmt->bindValue(":PROFILE_NAME", $name, PDO::PARAM_STR);
                $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
                $stmt->execute();
                $_SESSION['name'] = $name;

                header('Location: ./dashboard/dashboard.php?m=dashboard&a=settings');
                exit();
            }
        }
    }
}

//EDIT ACCOUNT EMAIL
if (isset($_POST['edit_email'])) {
    if ($_POST['edit_email'] != '') {
        $data = filter_input(INPUT_POST, 'edit_email');
        if ($data) {
            $pdo = connectDB($db);

            $email = filter_input(INPUT_POST, 'account_email', FILTER_SANITIZE_EMAIL);

            $errors = false;

            if (!$errors) {

                //Store old email
                $oldemail = $_SESSION['email'];

                //Update profile email
                $sql = "UPDATE user
                    SET Email = :ACCOUNT_EMAIL
                    WHERE ID = :REST_ID;";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":ACCOUNT_EMAIL", $email, PDO::PARAM_STR);
                $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
                $stmt->execute();

                //Create Notification for the name update

                $sql = "INSERT INTO log (Title, Description, Time, Date, Icon, user_ID)
            VALUES('Account Email Changed',
            (SELECT CONCAT('Your account email was changed from ' ,
            (SELECT CONCAT(:OLD_EMAIL,
            (SELECT CONCAT(' to ' , :ACCOUNT_EMAIL)))))),
            (SELECT TIME_FORMAT(CURRENT_TIME, '%H:%i')),
            (SELECT DATE_FORMAT(CURRENT_DATE(),'%d %b.')),
            'user.svg', :REST_ID);";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":OLD_EMAIL", $oldemail, PDO::PARAM_STR);
                $stmt->bindValue(":ACCOUNT_EMAIL", $email, PDO::PARAM_STR);
                $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
                $stmt->execute();
                $_SESSION['email'] = $email;

                header('Location: ./dashboard/dashboard.php?m=dashboard&a=settings');
                exit();
            }
        }
    }
}

//EDIT ACCOUNT USERNAME
if (isset($_POST['edit_username'])) {

    if ($_POST['edit_username'] != '') {
        $data = filter_input(INPUT_POST, 'edit_username');
        if ($data) {
            $pdo = connectDB($db);

            $username = filter_input(INPUT_POST, 'account_username', FILTER_SANITIZE_STRING);

            $errors = false;

            if (!$errors) {

                //Store old username
                $oldusername = $_SESSION['username'];

                //Update profile name
                $sql = "UPDATE user
                        SET Username = :ACCOUNT_USERNAME
                        WHERE ID = :REST_ID;";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":ACCOUNT_USERNAME", $username, PDO::PARAM_STR);
                $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
                $stmt->execute();

                //Create Notification for the name update

                $sql = "INSERT INTO log (Title, Description, Time, Date, Icon, user_ID)
                VALUES('Account Username Changed',
                (SELECT CONCAT('Your account username was changed from ' ,
                (SELECT CONCAT(:OLD_USERNAME,
                (SELECT CONCAT(' to ' , :ACCOUNT_USERNAME)))))),
                (SELECT TIME_FORMAT(CURRENT_TIME, '%H:%i')),
                (SELECT DATE_FORMAT(CURRENT_DATE(),'%d %b.')),
                'user.svg', :REST_ID);";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":OLD_USERNAME", $oldusername, PDO::PARAM_STR);
                $stmt->bindValue(":ACCOUNT_USERNAME", $username, PDO::PARAM_STR);
                $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
                $stmt->execute();
                $_SESSION['username'] = $username;

                header('Location: ./dashboard/dashboard.php?m=dashboard&a=settings');
                exit();
            }
        }
    }
}

header('Location: ./dashboard/dashboard.php');
exit();
