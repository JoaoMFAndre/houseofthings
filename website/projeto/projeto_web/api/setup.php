<?php

require_once "config.php";
require_once "core.php";

if (isset($_GET['name']) && isset($_GET['mac']) && isset($_GET['ip'])) {
	$pdo = connectDB($db);

	$name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);
	$mac = filter_input(INPUT_GET, 'mac', FILTER_SANITIZE_STRING);
	$ip = filter_input(INPUT_GET, 'ip', FILTER_SANITIZE_STRING);

	$sql = "SELECT ID FROM device
			WHERE IP = :IP AND MAC = :MAC LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(":IP", $ip, PDO::PARAM_STR);
	$stmt->bindValue(":MAC", $mac, PDO::PARAM_STR);
	$stmt->execute();
	if ($stmt->rowCount() == 0) {
		// begin the transaction
		$pdo->beginTransaction();
		// our SQL statements
		$pdo->exec("INSERT INTO actions (INPUT, OUTPUT, Temperature, Humidity, Brightness)
		VALUES ('', '', '', '', '')");
		$pdo->exec("INSERT INTO device_has_actions (actions_ID)
		VALUES ((SELECT MAX(ID) FROM actions))");
		$pdo->exec("INSERT INTO device (Name, IP, MAC, device_has_actions_ID)
		VALUES ('$name', '$ip', '$mac', (SELECT MAX(ID) FROM device_has_actions))");

		// commit the transaction
		$pdo->commit();
	} else {
		//Device already registered
	}
} else {
	$pdo = connectDB($db);

	$mac = filter_input(INPUT_GET, 'mac', FILTER_SANITIZE_STRING);

	$sql = "SELECT Type
			FROM device
			WHERE MAC = :MAC LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(":MAC", $mac, PDO::PARAM_STR);
	$stmt->execute();
	if ($stmt->rowCount() != 0) {
		$row = $stmt->fetch();

		if ($row['Type'] == NULL) {
			//Device not yet assigned a type
			die("Device not yet assigned a type");
		} else {
			echo $row['Type'];
		}
		exit();
	}
}
