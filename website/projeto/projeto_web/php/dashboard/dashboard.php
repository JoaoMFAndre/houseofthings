<?php

session_start();
if (!isset($_SESSION['uid'])) {
    session_destroy();
    header('Location: ../index.html');
    exit();
}

require_once '../../api/config.php';
require_once '../../api/core.php';

// Carregar módulo ativo
$module = filter_input(INPUT_GET, 'm', FILTER_SANITIZE_STRING);

// Carregar ação
$action = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_STRING);

// Testar se existe ficheiro a carregar. caso contrário carregar HOME
if (!file_exists("../$module/$action.php")) {
    $module = 'dashboard';
    $action = 'homepage';
} else {
    // Ligar à base de dados
    $pdo = connectDB($db);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= 'Dashboard | ' . ucfirst($action) ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/navbar.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>

</head>

<body>
    <div class="container">
        <!--Side Menu-->
        <aside>
            <div class="top">
                <div class="profile">
                    <img src="<?= AVATAR_WEB_PATH . ($_SESSION['avatar'] != null ? $_SESSION['avatar'] : AVATAR_DEFAULT) ?>">
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">close</span>
                </div>
            </div>
            <div class="sidebar" id="sidebar">
                <a <?= $module == 'homepage' ? 'active' : '' ?> href="?m=dashboard&a=homepage" class="<?= $action == 'homepage' ? 'active' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Outline" viewBox="0 0 24 24">
                        <path d="M23.121,9.069,15.536,1.483a5.008,5.008,0,0,0-7.072,0L.879,9.069A2.978,2.978,0,0,0,0,11.19v9.817a3,3,0,0,0,3,3H21a3,3,0,0,0,3-3V11.19A2.978,2.978,0,0,0,23.121,9.069ZM15,22.007H9V18.073a3,3,0,0,1,6,0Zm7-1a1,1,0,0,1-1,1H17V18.073a5,5,0,0,0-10,0v3.934H3a1,1,0,0,1-1-1V11.19a1.008,1.008,0,0,1,.293-.707L9.878,2.9a3.008,3.008,0,0,1,4.244,0l7.585,7.586A1.008,1.008,0,0,1,22,11.19Z" />
                    </svg>
                    <h3>Home</h3>
                </a>
                <a <?= $module == 'notifications' ? 'active' : '' ?> href="?m=dashboard&a=notifications" class="<?= $action == 'notifications' ? 'active' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <g id="_01_align_center" data-name="01 align center">
                            <path d="M23.259,16.2l-2.6-9.371A9.321,9.321,0,0,0,2.576,7.3L.565,16.35A3,3,0,0,0,3.493,20H7.1a5,5,0,0,0,9.8,0h3.47a3,3,0,0,0,2.89-3.8ZM12,22a3,3,0,0,1-2.816-2h5.632A3,3,0,0,1,12,22Zm9.165-4.395a.993.993,0,0,1-.8.395H3.493a1,1,0,0,1-.976-1.217l2.011-9.05a7.321,7.321,0,0,1,14.2-.372l2.6,9.371A.993.993,0,0,1,21.165,17.605Z" />
                        </g>
                    </svg>
                    <h3>Notifications</h3>
                    <?php
                    $pdo = connectDB($db);

                    $sql = "SELECT *
                            FROM log
                            WHERE user_ID = :REST_ID;";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
                    $stmt->execute();
                    if ($stmt->rowCount() > 0) {
                    ?>
                        <span class="message-count"><?= ((int) $stmt->rowCount() > 99 ? '99+' : (int) $stmt->rowCount()) ?></span>
                    <?php
                    } ?>
                </a>
                <a <?= $module == 'schedules' ? 'active' : '' ?> href="?m=dashboard&a=schedules" class="<?= $action == 'schedules' ? 'active' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Outline" viewBox="0 0 24 24">
                        <path d="M12,0A12,12,0,1,0,24,12,12.013,12.013,0,0,0,12,0Zm0,22A10,10,0,1,1,22,12,10.011,10.011,0,0,1,12,22Z" />
                        <path d="M12,6a1,1,0,0,0-1,1v4.325L7.629,13.437a1,1,0,0,0,1.062,1.7l3.84-2.4A1,1,0,0,0,13,11.879V7A1,1,0,0,0,12,6Z" />
                    </svg>
                    <h3>Schedules</h3>
                </a>
                <a <?= $module == 'statistics' ? 'active' : '' ?> href="?m=dashboard&a=statistics" class="<?= $action == 'statistics' ? 'active' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                        <path d="M12,0A12,12,0,1,0,24,12,12.013,12.013,0,0,0,12,0Zm9.573,9.12-8.908,1.732a4.941,4.941,0,0,1-.368-.679l-3.34-7.7A9.987,9.987,0,0,1,21.573,9.12ZM12,22A9.995,9.995,0,0,1,7.124,3.278l3.338,7.691a7.011,7.011,0,0,0,2.167,2.772l6.653,5.092A9.966,9.966,0,0,1,12,22Zm8.5-4.755-6.125-4.688,7.581-1.473c.027.3.046.607.046.916A9.925,9.925,0,0,1,20.5,17.245Z" />
                    </svg>
                    <h3>Statistics</h3>
                </a>
                <a <?= $module == 'settings' ? 'active' : '' ?> href="?m=dashboard&a=settings" class="<?= $action == 'settings' ? 'active' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Outline" viewBox="0 0 24 24">
                        <path d="M12,8a4,4,0,1,0,4,4A4,4,0,0,0,12,8Zm0,6a2,2,0,1,1,2-2A2,2,0,0,1,12,14Z" />
                        <path d="M21.294,13.9l-.444-.256a9.1,9.1,0,0,0,0-3.29l.444-.256a3,3,0,1,0-3-5.2l-.445.257A8.977,8.977,0,0,0,15,3.513V3A3,3,0,0,0,9,3v.513A8.977,8.977,0,0,0,6.152,5.159L5.705,4.9a3,3,0,0,0-3,5.2l.444.256a9.1,9.1,0,0,0,0,3.29l-.444.256a3,3,0,1,0,3,5.2l.445-.257A8.977,8.977,0,0,0,9,20.487V21a3,3,0,0,0,6,0v-.513a8.977,8.977,0,0,0,2.848-1.646l.447.258a3,3,0,0,0,3-5.2Zm-2.548-3.776a7.048,7.048,0,0,1,0,3.75,1,1,0,0,0,.464,1.133l1.084.626a1,1,0,0,1-1,1.733l-1.086-.628a1,1,0,0,0-1.215.165,6.984,6.984,0,0,1-3.243,1.875,1,1,0,0,0-.751.969V21a1,1,0,0,1-2,0V19.748a1,1,0,0,0-.751-.969A6.984,6.984,0,0,1,7.006,16.9a1,1,0,0,0-1.215-.165l-1.084.627a1,1,0,1,1-1-1.732l1.084-.626a1,1,0,0,0,.464-1.133,7.048,7.048,0,0,1,0-3.75A1,1,0,0,0,4.79,8.992L3.706,8.366a1,1,0,0,1,1-1.733l1.086.628A1,1,0,0,0,7.006,7.1a6.984,6.984,0,0,1,3.243-1.875A1,1,0,0,0,11,4.252V3a1,1,0,0,1,2,0V4.252a1,1,0,0,0,.751.969A6.984,6.984,0,0,1,16.994,7.1a1,1,0,0,0,1.215.165l1.084-.627a1,1,0,1,1,1,1.732l-1.084.626A1,1,0,0,0,18.746,10.125Z" />
                    </svg>
                    <h3>Settings</h3>
                </a>
                <a href="../logout.php">
                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                        <path d="M22.763,10.232l-4.95-4.95L16.4,6.7,20.7,11H6.617v2H20.7l-4.3,4.3,1.414,1.414,4.95-4.95a2.5,2.5,0,0,0,0-3.536Z" />
                        <path d="M10.476,21a1,1,0,0,1-1,1H3a1,1,0,0,1-1-1V3A1,1,0,0,1,3,2H9.476a1,1,0,0,1,1,1V8.333h2V3a3,3,0,0,0-3-3H3A3,3,0,0,0,0,3V21a3,3,0,0,0,3,3H9.476a3,3,0,0,0,3-3V15.667h-2Z" />
                    </svg>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>

        <div class="top-mobile">

            <button id="menu-btn">
                <span class="material-icons-sharp">menu</span>
            </button>
            <div class="profile">
                <img src="<?= AVATAR_WEB_PATH . ($_SESSION['avatar'] != null ? $_SESSION['avatar'] : AVATAR_DEFAULT) ?>">
            </div>
        </div>

        <!--Main Menu-->
        <div class="main-menu">
            <?php
            if ($action != 'dashboard') {
                require_once "../$module/$action.php";
            } else {
                $module = 'dashboard';
                $action = 'homepage';
                require_once "../$module/$action.php";
            }
            ?>
        </div>

        <script src="../../js/navbar.js"></script>
</body>

</html>