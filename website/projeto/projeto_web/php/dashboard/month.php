<?php

if (!isset($_SESSION['uid'])) {
    session_start();
}
require_once '../../api/config.php';
require_once '../../api/core.php';

$pdo = connectDB($db);

$sql = "SELECT Month, statistics.ID
        FROM statistics
        INNER JOIN room
        ON room.ID = statistics.room_ID
        WHERE user_ID = :REST_ID AND Year = :YEAR
        GROUP BY Month
        ORDER BY Month DESC LIMIT 1;";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
$stmt->bindValue(":YEAR", $_SESSION['selected_year'], PDO::PARAM_INT);
$stmt->execute();
$month = $stmt->fetch();
$_SESSION['selected_month'] = $month['Month'];


$pdo = connectDB($db);

$sql = "SELECT Month, statistics.ID
        FROM statistics
        INNER JOIN room
        ON room.ID = statistics.room_ID
        WHERE user_ID = :REST_ID AND Year = :YEAR
        GROUP BY Month
        ORDER BY Month DESC;";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
$stmt->bindValue(":YEAR", $_SESSION['selected_year'], PDO::PARAM_INT);
$stmt->execute();
$i = 0;

while ($row = $stmt->fetch()) {
?>
    <h3 id="<?= $row['Month'] ?>" class="month_name<?= ($i == 0 ? ' active' : ''); ?>" onclick="selectMonth(this.id)"><?= $row['Month'] ?></h3>
<?php
    $i++;
}
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.month_name').click(function() {
            $.ajax({
                url: '../update.php',
                type: 'GET',
                data: {
                    month_NAME: this.id
                },
                success: function() {
                    $('.statistics-left').fadeOut(300, function() {
                        $('.statistics-left').load('chart.php');
                        $('.statistics-left').fadeIn(300);
                    });
                    $('.container-right').fadeOut(300, function() {
                        $('.container-right').load('graph.php');
                        $('.container-right').fadeIn(300);
                    });
                }
            });
        });
    });
</script>