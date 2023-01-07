<?php
if (!isset($_SESSION['uid'])) {
    session_start();
}
require_once '../../api/config.php';
require_once '../../api/core.php';

?>

<span class="title">Consumption Per Day</span>
<span class="title-month-year"><?= $_SESSION['selected_month'] ?> <?= $_SESSION['selected_year'] ?></span>
<div class="graph">
    <!--https://chartscss.org/charts/area/#multiple-datasets-->
    <div id="consumption-graph" class="consumption-graph"></div>
    <?php
    $pdo = connectDB($db);

    $sql = "SELECT *,
        GROUP_CONCAT(statistics.Consumption ORDER BY statistics.Day ASC SEPARATOR ', ') AS Y_AXIS,
        GROUP_CONCAT(statistics.Day ORDER BY statistics.Day ASC SEPARATOR ', ') AS X_AXIS
        FROM room
        INNER JOIN statistics
        ON statistics.room_ID = room.ID
        WHERE user_ID = :REST_ID AND Month = :MONTH_NAME
        GROUP BY room.Name
        ORDER BY room.ID ASC;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
    $stmt->bindValue(":MONTH_NAME", $_SESSION['selected_month'], PDO::PARAM_STR);
    $stmt->execute();
    $i = 0;
    $array = [];
    $x = '';
    $y = '';
    while ($row = $stmt->fetch()) {
        $x = $row['X_AXIS'];
        $y = $row['Y_AXIS'];
        $x_arr = explode(",", $x);
        $y_arr = explode(",", $y);
        $array[$i] = [
            "x" => $x_arr,
            "y" => $y_arr,
            "name" => $row['Name'],
            "type" => 'scatter'
        ];
    ?>
    <?php
        $i++;
    }
    $json = json_encode($array);

    echo "<script type='text/javascript'>
Graph($json);
console.log($json);

function Graph(data) {

    var layout = {
        font: { size: 14 },
        yaxis: { title: 'Consumption (kW)' },
        yaxis2: {
        titlefont: { color: 'rgb(148, 103, 189)' },
        tickfont: { color: 'rgb(148, 103, 189)' },
        overlaying: 'y',
        side: 'right'
        }
    };
    
    var config = { responsive: true }
    
    
    Plotly.newPlot('consumption-graph', data, layout, config);
    }
</script>" ?>

</div>