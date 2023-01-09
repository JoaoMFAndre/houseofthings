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
    <link rel="stylesheet" type="text/css" href="../../css/notifications.css">
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script defer src="../../js/drag.js"></script>
</head>

<body>
    <div class="container-notifications">
        <!--Notifications-->
        <main>
            <div class="top">
                <div class="text">
                    <h1 id="greeting">Recent</h1>
                    <h2>Notifications</h2>
                </div>
            </div>

            <div class="container">
                <?php
                $pdo = connectDB($db);

                $sql = "SELECT *
                        FROM log
                        WHERE user_ID = :REST_ID
                        ORDER BY ID DESC;";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                ?>
                    <div class="top" id="clear_notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" filter="none">
                            <path d="M5 13h14v-2H5v2zm-2 4h14v-2H3v2zM7 7v2h14V7H7z" />
                        </svg>
                        clear all
                    </div>
                <?php
                } else {
                ?>
                    <div class="center-no-notifications">
                        <img src="../../images/icons/dashboard/no-notification.png" alt="">
                        No Notifications Right Now
                    </div>
                <?php
                } ?>
                <div class="center">
                    <div class="notification-container">
                        <div class="left">
                            <?php
                            $pdo = connectDB($db);

                            $sql = "SELECT *
                                FROM log
                                WHERE user_ID = :REST_ID
                                ORDER BY ID DESC;";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
                            $stmt->execute();
                            while ($row = $stmt->fetch()) {
                            ?>
                                <div class="item show" id="fade-in">
                                    <div class="icon-title">
                                        <div class="notification-icon">
                                            <?php
                                            $svg_file = file_get_contents(ICON_PATH . $row['Icon']);
                                            echo $svg_file;
                                            ?>
                                        </div>
                                        <div class="notification-title"><?php echo $row['Title']; ?></div>
                                    </div>
                                    <div class="notification-description">- <?php echo $row['Description']; ?>.</div>
                                    <div class="time-delete">
                                        <div class="notification-time"><?php echo $row['Time']; ?></div>
                                        <div class="notification-delete" id="<?php echo $row['ID']; ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" id="Outline" viewBox="0 0 24 24">
                                                <path d="M16,8a1,1,0,0,0-1.414,0L12,10.586,9.414,8A1,1,0,0,0,8,9.414L10.586,12,8,14.586A1,1,0,0,0,9.414,16L12,13.414,14.586,16A1,1,0,0,0,16,14.586L13.414,12,16,9.414A1,1,0,0,0,16,8Z" />
                                                <path d="M12,0A12,12,0,1,0,24,12,12.013,12.013,0,0,0,12,0Zm0,22A10,10,0,1,1,22,12,10.011,10.011,0,0,1,12,22Z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                            ?>
                        </div>
                        <div class="right">
                            <?php
                            $pdo = connectDB($db);

                            $sql = "SELECT *
                                FROM log
                                WHERE user_ID = :REST_ID
                                ORDER BY ID DESC;";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
                            $stmt->execute();
                            $i = 1;
                            while ($row = $stmt->fetch()) {
                            ?>
                                <div class="item show" id="fade-in">
                                    <div class="notification-misc">
                                        <div class="dot"></div>
                                        <?php
                                        if ($stmt->rowCount() > $i) {
                                        ?>
                                            <div class="line"></div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="notification-date">
                                        <div class="date"><?php echo $row['Date']; ?></div>
                                    </div>
                                </div>
                            <?php
                                $i++;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#clear_notifications').click(function() {
                $('[id=fade-in]').removeClass('show');

                $.ajax({
                    url: '../delete.php',
                    type: 'GET',
                    data: {
                        action: 'delete_notifications'
                    },
                    success: function() {
                        setTimeout(function() { // wait for 1 secs
                            location.reload(); // then reload the page
                        }, 1000);
                    }
                });
            });
        });

        $(document).ready(function() {
            $('.notification-delete').click(function() {
                $(this).parent(".time-delete").parent(".item").removeClass('show');
                $.ajax({
                    url: '../delete.php',
                    type: 'GET',
                    data: {
                        action: 'delete_notification',
                        notification_ID: this.id
                    },
                    success: function() {
                        setTimeout(function() { // wait for 1 secs
                            location.reload(); // then reload the page.
                        }, 1000);
                    }
                });
            });
        });
    </script>
</body>

</html>