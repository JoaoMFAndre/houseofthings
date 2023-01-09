<?php
if (!isset($_SESSION['uid'])) {
    session_start();
}
require_once '../../api/config.php';
require_once '../../api/core.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/statistics.css">
    <link rel="stylesheet" type="text/css" href="../../css/progresschart.css">
    <script src='https://cdn.plot.ly/plotly-latest.min.js'></script>
</head>

<body>
    <div class="container-statistics">
        <!--Statistics-->
        <main>
            <div class="top">
                <div class="text">
                    <h1 id="greeting">Statistics</h1>
                    <h2>Consumption</h2>
                </div>
                <div class="button">
                    <select name="year" id="year" class="select" required>
                        <option disabled selected value>Select an year</option>
                        <?php
                        $pdo = connectDB($db);

                        $sql = "SELECT Year
                                FROM statistics
                                INNER JOIN room
                                ON room.ID = statistics.room_ID
                                WHERE user_ID = :REST_ID
                                GROUP BY Year
                                ORDER BY Year DESC;";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
                        $stmt->execute();
                        while ($row = $stmt->fetch()) {
                        ?>
                            <option value="<?= $row['Year'] ?>"><?= $row['Year'] ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <svg xmlns="http://www.w3.org/2000/svg" id="Bold" viewBox="0 0 24 24">
                        <path d="M6.414,9H17.586a1,1,0,0,1,.707,1.707l-5.586,5.586a1,1,0,0,1-1.414,0L5.707,10.707A1,1,0,0,1,6.414,9Z" />
                    </svg>
                </div>
            </div>

            <div id="month" class="month">
                <?php include './month.php'; ?>
            </div>

            <div class="center">
                <div class="container">
                    <?php
                    if ($_SESSION['selected_year'] == '' || $_SESSION['selected_month'] == '') {
                    ?>
                        <div class="no-statistics">
                            <img src="../../images/icons/dashboard/no-statistics.png" alt="">
                            No results
                        </div>
                    <?php } else {
                    ?>
                    <div class="container-left">
                        Total kW by Room
                        <div class="statistics-left">
                            <?php include './chart.php'; ?>
                        </div>
                    </div>
                    <div class="container-right">
                        <?php include './graph.php'; ?>
                    </div>
                    <?php }
                    ?>
                </div>
            </div>
    </div>
    </main>
    </div>
    <script>
        function selectMonth(clicked_id) {

            const month_name = document.getElementById(clicked_id);

            if (!month_name.classList.contains('active')) {
                const allMonths = document.getElementsByClassName("month_name");
                for (var i = 0; i < allMonths.length; i++) {
                    allMonths[i].classList.remove('active');
                }
                month_name.classList.add('active');
            }
        }
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#year').change(function() {
                var selected_option_value = $("#year option:selected").val();
                $.ajax({
                    url: '../update.php',
                    type: 'GET',
                    data: {
                        year_NAME: selected_option_value
                    },
                    success: function() {
                        $('.month').fadeOut(300, function() {
                            $('.month').load('month.php');
                            $('.month').fadeIn(300);
                        });
                        $('.statistics-left').fadeOut(320, function() {
                            $('.statistics-left').load('chart.php');
                            $('.statistics-left').fadeIn(300);
                        });
                        $('.container-right').fadeOut(320, function() {
                            $('.container-right').load('graph.php');
                            $('.container-right').fadeIn(300);
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>