<?php

if (!isset($_SESSION['uid'])) {
    session_start();
}
require_once '../../api/config.php';
require_once '../../api/core.php';

$pdo = connectDB($db);

$sql = "SELECT device.ID
FROM device
INNER JOIN room ON device.room_ID = room.ID
WHERE room.user_ID = :REST_ID AND room.ID = :ROOM_ID
ORDER BY device.ID ASC LIMIT 1;";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
$stmt->bindValue(":ROOM_ID", $_SESSION['selected_room'], PDO::PARAM_INT);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $row2 = $stmt->fetch();
    $select = $row2['ID'];
    $_SESSION['selected_device'] = $select;
}

$sql = "SELECT device.ID, device.Name, device.State, device.Icon, actions.Output, actions.Input, actions.Brightness
        FROM (((device
        INNER JOIN room ON device.room_ID = room.ID)
        INNER JOIN device_has_actions ON device.device_has_actions_ID = device_has_actions.ID)
        INNER JOIN actions ON device_has_actions.actions_ID = actions.ID)
        WHERE room.user_ID = :REST_ID AND room.ID = :ROOM_ID;";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
$stmt->bindValue(":ROOM_ID", $_SESSION['selected_room'], PDO::PARAM_INT);
$stmt->execute();
while ($row = $stmt->fetch()) {
?>
    <div class="item<?php echo ($select == $row['ID'] ? ' active' : ''); ?>" id="item_<?php echo $row['ID'] ?>" onclick="selectDevice(this.id)">
        <div class="top">
            <?php
            $svg_file = file_get_contents(ICON_PATH . $row['Icon']);
            echo $svg_file;
            ?>
            <label class="switch">
                <input type="checkbox" class="on_off_btn" id="<?php echo $row['ID'] ?>" name="<?php echo $row['ID'] ?>" <?php echo ($row['State'] == 'on' ? 'checked' : ''); ?>>
                <span class="switch-slider round"></span>
            </label>
        </div>
        <div class="bottom">
            <h1>
                <?php echo $row['Name']; ?>
            </h1>
            <h2>
                <?php if ($row['Icon'] == 'bulb.svg') {
                ?>
                    <span class="range-count"><?php echo $row['Brightness'] ?></span><span>%</span>
                <?php
                } else {
                ?>
                    <span class="range-count"><?php echo $row['Input'] ?></span><span>&#176;C</span>
                <?php
                }
                ?>
            </h2>
        </div>
    </div>
<?php }
?>

<script type="text/javascript">
    $(document).ready(function() {
        $('.on_off_btn').click(function() {
            var state = $(this).is(':checked') ? 'on' : 'off';
            $.ajax({
                url: '../update.php',
                type: 'GET',
                data: {
                    state: state,
                    device_ID: this.id
                },
                success: function() {
                    $('.consumption').fadeOut(320, function() {
                        $('.consumption').load('consumption.php');
                        $('.consumption').fadeIn(300);
                    });
                }
            });
        });
    });

    $(document).ready(function() {
        $('.item').click(function() {
            var id = this.id.split("_").pop();
            $.ajax({
                url: '../update.php',
                type: 'GET',
                data: {
                    device_ID: id
                },
                success: function() {
                    $('.control').fadeOut(300, function() {
                        $('.control').load('detail.php');
                        $('.control').fadeIn(300);
                    });
                }
            });
        });
    });
</script>