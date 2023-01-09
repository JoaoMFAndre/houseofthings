<?php 
if (!isset($_SESSION['uid'])) {
    session_start();
}
require_once '../../api/config.php';
require_once '../../api/core.php';

?>
<div class="percentage-chart">
    <?php
    $pdo = connectDB($db);

    $sql = "SELECT room.user_ID, room.Name, statistics.room_ID, statistics.Month, statistics.Year,
            SUM(statistics.Consumption) AS room_Consumption,
            ROUND((SUM(statistics.Consumption) * 100 / t.Total), 0) AS Percentage,
            t.Total AS Total
            FROM room
            INNER JOIN statistics
            ON statistics.room_ID = room.ID
            CROSS JOIN (SELECT SUM(statistics.Consumption) AS Total
                        FROM statistics
                        INNER JOIN room
                        ON room.ID = statistics.room_ID
                        WHERE room.user_ID = :REST_ID AND Month = :MONTH_NAME) t
            WHERE user_ID = :REST_ID AND Month = :MONTH_NAME
            GROUP BY room.Name, statistics.room_ID
            ORDER BY room.ID ASC;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
    $stmt->bindValue(":MONTH_NAME", $_SESSION['selected_month'], PDO::PARAM_STR);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
    ?>
        <div role="progressbar" aria-valuenow="<?= $row['Percentage'] ?>" aria-valuemin="0" aria-valuemax="100" style="--value:<?= $row['Percentage'] ?>"></div>
    <?php }
    ?>

</div>
<div class="text">

    <?php
    $pdo = connectDB($db);

    $sql = "SELECT room.user_ID, room.Name, statistics.room_ID, statistics.Month, statistics.Year,
            SUM(statistics.Consumption) AS room_Consumption,
            ROUND((SUM(statistics.Consumption) * 100 / t.Total), 0) AS Percentage,
            t.Total AS Total
            FROM room
            INNER JOIN statistics
            ON statistics.room_ID = room.ID
            CROSS JOIN (SELECT SUM(statistics.Consumption) AS Total
                        FROM statistics
                        INNER JOIN room
                        ON room.ID = statistics.room_ID
                        WHERE room.user_ID = :REST_ID AND Month = :MONTH_NAME) t
            WHERE user_ID = :REST_ID AND Month = :MONTH_NAME
            GROUP BY room.Name, statistics.room_ID
            ORDER BY room.ID ASC;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
    $stmt->bindValue(":MONTH_NAME", $_SESSION['selected_month'], PDO::PARAM_STR);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
    ?>
        <div class="percentage-text">
            <div class="room-name"><?= $row['Name'] ?></div>
            <div class="room-consumption"><?= $row['room_Consumption'] ?> kW</div>
        </div>
    <?php }
    ?>

</div>