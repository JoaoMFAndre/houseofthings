<?php

require_once "config.php";
require_once "core.php";

//DHT11 DATA
if (isset($_GET['device_mac'])) {
    $pdo = connectDB($db);

    $device_mac = filter_input(INPUT_GET, 'device_mac', FILTER_SANITIZE_STRING);
    $temp = filter_input(INPUT_GET, 'temp', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $hum = filter_input(INPUT_GET, 'hum', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

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
            $sql = "UPDATE actions as a
        INNER JOIN device_has_actions as da
        ON da.actions_ID = a.ID
        INNER JOIN device as d
        ON d.device_has_actions_ID = da.ID
        SET a.Temperature = :TEMPERATURE, a.Humidity = :HUMIDITY
        WHERE d.MAC = :MAC;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":MAC", $device_mac, PDO::PARAM_STR);
            $stmt->bindValue(":TEMPERATURE", $temp, PDO::PARAM_STR);
            $stmt->bindValue(":HUMIDITY", $hum, PDO::PARAM_STR);
            $stmt->execute();
        }
        exit();
    }
}
//DEVICE HOURLY CONSUMPTION
if (isset($_GET['mac'])) {
    $pdo = connectDB($db);

    $mac = filter_input(INPUT_GET, 'mac', FILTER_SANITIZE_STRING);
    $day = filter_input(INPUT_GET, 'day', FILTER_SANITIZE_NUMBER_INT);
    $month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_STRING);
    $year = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT);

    $sql = "SELECT *
            FROM device
            WHERE MAC = :MAC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":MAC", $mac, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {

        $row = $stmt->fetch();

        if ($row['State'] == 'off') {
            //Device is not on
            die("Device is not on");
        } else {
            if ($row['Consumption'] == 0 || $row['Consumption'] == NULL) {
                //Device consumption has not been defined
                die("Device consumption has not been defined");
            } else {
                $sql = "SELECT *
                        FROM statistics AS s
                        CROSS JOIN (SELECT device.room_ID AS rID
                                FROM device
                                INNER JOIN room
                                ON room.ID = device.room_ID
                                WHERE device.MAC = :MAC) as d
                        INNER JOIN room
                        ON room.ID = s.room_ID
                        WHERE s.room_ID = d.rID AND s.Day = :DAY AND s.Month = :MONTH AND s.Year = :YEAR";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":MAC", $mac, PDO::PARAM_STR);
                $stmt->bindValue(":DAY", $day, PDO::PARAM_INT);
                $stmt->bindValue(":MONTH", $month, PDO::PARAM_STR);
                $stmt->bindValue(":YEAR", $year, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch();
                if ($stmt->rowCount() > 0) {
                    //UPDATE EXISTING ENTRY
                    $sql = "UPDATE statistics as s
                            CROSS JOIN (SELECT device.room_ID AS rID, device.Consumption AS dConsumption
                                        FROM device
                                        INNER JOIN room
                                        ON room.ID = device.room_ID
                                        WHERE device.MAC = :MAC) as d
                            INNER JOIN room
                            ON room.ID = s.room_ID
                            SET s.Consumption = s.Consumption + d.dConsumption
                            WHERE s.room_ID = d.rID AND s.Day = :DAY AND s.Month = :MONTH AND s.Year = :YEAR";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(":MAC", $mac, PDO::PARAM_STR);
                    $stmt->bindValue(":DAY", $day, PDO::PARAM_INT);
                    $stmt->bindValue(":MONTH", $month, PDO::PARAM_STR);
                    $stmt->bindValue(":YEAR", $year, PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    //CREATE NEW ENTRY AND UPDATE PREVIOUS ONE
                    $sql = "INSERT INTO statistics(Day, Month, Year, Consumption, room_ID)
                    VALUES (:DAY, :MONTH, :YEAR, 0,
                            (SELECT device.room_ID
                            FROM device
                            INNER JOIN room
                            ON room.ID = device.room_ID
                            WHERE device.MAC = :MAC Limit 1))";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(":MAC", $mac, PDO::PARAM_STR);
                    $stmt->bindValue(":DAY", $day, PDO::PARAM_INT);
                    $stmt->bindValue(":MONTH", $month, PDO::PARAM_STR);
                    $stmt->bindValue(":YEAR", $year, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        }
        exit();
    }
}
//CREATE ENTRY FOR NEW DAY
if (isset($_GET['macadress'])) {
    $pdo = connectDB($db);

    $mac = filter_input(INPUT_GET, 'macadress', FILTER_SANITIZE_STRING);
    $day = filter_input(INPUT_GET, 'day', FILTER_SANITIZE_NUMBER_INT);
    $month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_STRING);
    $year = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT);

    $sql = "SELECT *
            FROM device
            WHERE MAC = :MAC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":MAC", $mac, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {

        $row = $stmt->fetch();

        if ($row['State'] == 'off') {
            //Device is not on
            die("Device is not on");
        } else {
            if ($row['Consumption'] == 0 || $row['Consumption'] == NULL) {
                //Device consumption has not been defined
                die("Device consumption has not been defined");
            } else {
                $sql = "SELECT *
                        FROM statistics AS s
                        CROSS JOIN (SELECT device.room_ID AS rID
                                FROM device
                                INNER JOIN room
                                ON room.ID = device.room_ID
                                WHERE device.MAC = :MAC) as d
                        INNER JOIN room
                        ON room.ID = s.room_ID
                        WHERE s.room_ID = d.rID AND s.Day = :DAY AND s.Month = :MONTH AND s.Year = :YEAR";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":MAC", $mac, PDO::PARAM_STR);
                $stmt->bindValue(":DAY", $day, PDO::PARAM_INT);
                $stmt->bindValue(":MONTH", $month, PDO::PARAM_STR);
                $stmt->bindValue(":YEAR", $year, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch();
                if ($stmt->rowCount() > 0) {
                    //ENTRY FOR TODAY
                    die("Todays entry already exists");
                } else {
                    //CREATE NEW ENTRY
                    $sql = "INSERT INTO statistics(Day, Month, Year, Consumption, room_ID)
                    VALUES (:DAY, :MONTH, :YEAR, 0,
                            (SELECT device.room_ID
                            FROM device
                            INNER JOIN room
                            ON room.ID = device.room_ID
                            WHERE device.MAC = :MAC Limit 1))";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(":MAC", $mac, PDO::PARAM_STR);
                    $stmt->bindValue(":DAY", $day, PDO::PARAM_INT);
                    $stmt->bindValue(":MONTH", $month, PDO::PARAM_STR);
                    $stmt->bindValue(":YEAR", $year, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        }
        exit();
    }
}
