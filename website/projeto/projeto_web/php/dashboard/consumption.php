<?php
if (!isset($_SESSION['uid'])) {
    session_start();
}
require_once '../../api/config.php';
require_once '../../api/core.php';
?>

<div id="my-pie-chart-container">
    <?php
    $pdo = connectDB($db);
    $sql = "SELECT SUM(device.Consumption) as sum
                FROM device
                INNER JOIN room
                ON room.ID = device.room_ID
                WHERE room.user_ID = :REST_ID
                AND device.room_ID = :ROOM_ID
                AND device.State = 'on';";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
    $stmt->bindValue(":ROOM_ID", $_SESSION['selected_room'], PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();
    if ($row['sum'] == '') { ?>
        <div class="no-consumption">
            <img src="../../images/icons/dashboard/power.png" alt="">
            No devices on
        </div>
    <?php } else { ?>
        <div id="my-pie-chart">
            <div class="hole"><?= ($row['sum'] == '' ? 0 : $row['sum']) ?><span class="text">kWh</span></div>
        </div>
    <?php }
    ?>
    <div class="legenda" id="legenda">
        <div class="device-name">
            <?php
            $pdo = connectDB($db);
            $sql = "SELECT device.Name
                    FROM device
                    INNER JOIN room ON device.room_ID = room.ID
                    WHERE device.room_ID = :ROOM_ID AND room.user_ID = :REST_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
            $stmt->bindValue(":ROOM_ID", $_SESSION['selected_room'], PDO::PARAM_INT);
            $stmt->execute();
            $index = -1;
            while ($row = $stmt->fetch()) {
                $index++;
            ?>
                <div class="entry">
                    <div id="<?php echo ENTRY_COLOR[$index] ?>" class="entry-color"></div>
                    <div class="entry-text"><?php echo $row['Name']; ?></div>
                </div>
            <?php
            }
            ?>
        </div>
        <div class="device-consumption">
            <?php
            $pdo = connectDB($db);
            $sql = "SELECT device.State, device.Consumption, ROUND((device.Consumption * 100 / t.s), 0) AS Percentage
                    FROM device
                    INNER JOIN room
                    ON room.ID = device.room_ID
                    CROSS JOIN (SELECT SUM(device.Consumption) AS s
                                FROM device
                                INNER JOIN room
                                ON room.ID = device.room_ID
                                WHERE room.user_ID = :REST_ID AND device.room_ID = :ROOM_ID AND device.State = 'on') t
                    WHERE room.user_ID = :REST_ID AND device.room_ID = :ROOM_ID;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
            $stmt->bindValue(":ROOM_ID", $_SESSION['selected_room'], PDO::PARAM_INT);
            $stmt->execute();
            $arr = [];
            $i = -1;
            $string = '';
            while ($row = $stmt->fetch()) {
                $i++;
                $arr[] = ($row['State'] == 'on' ? $row['Percentage'] : '0');
                $string .= ($row['State'] == 'on' ? ENTRY_COLOR[$i] . ' ' : '') .
                    ($i == 0 ? ($row['State'] == 'on' ? '0% ' : '') : ($row['State'] == 'on' ? $arr[$i - 1] . '% ' : '')) .
                    ($row['State'] == 'on' ? ($arr[$i] + ($i == 0 ? 0 : $arr[$i - 1]) . '%, ') : '');
            ?>
                <div class="entry-text"><?= ($row['State'] == 'on' ? $row['Percentage'] : 0); ?>%</div>
            <?php
            }
            $s = substr($string, 0, -2);
            ?>
        </div>
    </div>
</div>
<script>
    if (document.getElementById('my-pie-chart')) {
        document.getElementById('my-pie-chart').style.background = 'conic-gradient(<?= $s ?>)';
    }
</script>