<?php
session_start();

require_once '../api/config.php';
require_once '../api/core.php';

$login = filter_input(INPUT_POST, 'login');
$usernameErr = $passwordErr = "";
if ($login) {
    $pdo = connectDB($db);

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $password_hash_db = password_hash($password, PASSWORD_DEFAULT);

    $errors = false;
    if ($username == '') {
        //$html .= '<div class="alert-danger">Tem que definir um username.</div>';
        $usernameErr = "Username is required";
        $errors = true;
    }

    if (!$errors) {
        $sql = "SELECT * FROM `user` WHERE `Username` = :USERNAME LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":USERNAME", $username, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($stmt->rowCount() != 1) {
            //$html .= '<div class="container alert-danger">O email indicado não se encontra registado.</div>';
            $usernameErr = "Username is not registered";
            $errors = true;
        }

        if (!password_verify($password, $row['Password'])) {
            //$html .= '<div class="container alert-danger">Palavra-passe incorreta.</div>';
            $passwordErr = "Password is incorrect";
            sleep(random_int(1, 3));
        } else {
            $_SESSION['uid'] = $row['ID'];
            $_SESSION['email'] = $row['Email'];
            $_SESSION['username'] = $row['Username'];
            $_SESSION['name'] = $row['Name'];
            $_SESSION['avatar'] = is_file(AVATAR_PATH . $row['Avatar']) ? $row['Avatar'] : AVATAR_DEFAULT;
            $sql = "SELECT ID FROM room
                    WHERE user_ID = :REST_ID
                    ORDER BY ID ASC
                    LIMIT 1;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            if ($stmt->rowCount() > 0) {
                $_SESSION['selected_room'] = $row['ID'];
            } else {
                //No room
                $_SESSION['selected_room'] = '';
            }
            $sql = "SELECT ID FROM device
                    WHERE room_ID = :ROOM_ID
                    ORDER BY ID ASC
                    LIMIT 1;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":ROOM_ID", $_SESSION['selected_room'], PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();
            if ($stmt->rowCount() > 0) {
                $_SESSION['selected_device'] = $row['ID'];
            } else {
                //No device
                $_SESSION['selected_device'] = '';
            }

            $sql = "SELECT statistics.Year
                    FROM statistics
                    INNER JOIN room
                    ON room.ID = statistics.room_ID
                    WHERE room.user_ID = :REST_ID
                    GROUP BY Year
                    ORDER BY Year DESC
                    LIMIT 1;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();
            if ($stmt->rowCount() > 0) {
                $_SESSION['selected_year'] = $row['Year'];
            } else {
                //No year
                $_SESSION['selected_year'] = '';
            }
            $sql = "SELECT statistics.Month
                    FROM statistics
                    INNER JOIN room
                    ON room.ID = statistics.room_ID
                    WHERE room.user_ID = :REST_ID AND Year = :YEAR
                    ORDER BY Month DESC
                    LIMIT 1;";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":REST_ID", $_SESSION['uid'], PDO::PARAM_INT);
            $stmt->bindValue(":YEAR", $_SESSION['selected_year'], PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();
            if ($stmt->rowCount() > 0) {
                $_SESSION['selected_month'] = $row['Month'];
            } else {
                //No month
                $_SESSION['selected_month'] = '';
            }

            $html .= '<div class="container alert-success">Login com sucesso! <br> <b>' . $_SESSION['username'] . '</b></div>';
            $html .= '<div class="container alert-success"><a href="index.html" class="btn btn-primary">Continuar</a></div>';
            header('Location: ./dashboard/dashboard.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration</title>
    <link rel="stylesheet" type="text/css" href="../css/authentication.css">
</head>

<body>
    <!--Login & Register-->
    <div class="authentication">
        <div class="form-box">
            <form id="login" action="?" class="input-group" method="POST">
                <div class="form-title">Login</div>
                <div class="form-social-icons">
                    <img src="../images/icons/signin_fb.png" alt="Signin_FB">
                    <img src="../images/icons/signin_tw.png" alt="Signin_TW">
                    <img src="../images/icons/signin_go.png" alt="Signin_GO">
                </div>
                <label for="text">Username</label>
                <input type="text" class="input-field" name="username" id="username" placeholder="Type your username" required>
                <div class="error"><?= $usernameErr; ?></div>
                <label for="text">Password</label>
                <input type="password" class="input-field" name="password" id="password" placeholder="Type your password" required>
                <div class="error"><?= $passwordErr; ?></div>
                <input type="checkbox" class="checkbox"><span>Remember Me</span>
                <button type="submit" name="login" value="Login" class="submit-btn">Log In</button>
                <div class="change-form">Don't have an account? <a href="register.php">Signup now</a></div>
            </form>
        </div>
    </div>
</body>

</html>