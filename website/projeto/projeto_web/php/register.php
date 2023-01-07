<?php

require_once '../api/config.php';
require_once '../api/core.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Verificar se o formulário foi submetido
$register = filter_input(INPUT_POST, 'register');
$nameErr = $usernameErr = $emailErr = $passwordErr = $success = "";
if ($register) {
    $pdo = connectDB($db);
    //$html .= debug() ? '<p>Utilizador: <code>' . $db['username'] . '</code> Base de Dados: <code>' . $db['dbname'] . '</code></p>' : '';

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $password_hash_db = password_hash($password, PASSWORD_DEFAULT);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    //$html .= debug() ? "<code>FORMULÁRIO:<br>email: $email <br> username: $username <br> pwd: $password <br> hash: $password_hash_db</code>" : '';

    $errors = false;


    if ($email == '') {
        $emailErr = "Email is required";
        $errors = true;
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            //$html .= '<div class="alert-danger">O email não é válido.</div>';
            $emailErr = "Invalid email format";
            $errors = true;
        }
    }
    if ($name == '') {
        //$html .= '<div class="alert-danger">Tem que definir um username.</div>';
        $nameErr = "Name is required";
        $errors = true;
    } else {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $username)) {
            $nameErr = "Only letters and white space allowed";
            $errors = true;
        }
        if (strlen($name) > 16) {
            $nameErr = "Name can't have more than 16 characters";
            $errors = true;
        }
    }
    if ($username == '') {
        //$html .= '<div class="alert-danger">Tem que definir um username.</div>';
        $usernameErr = "Username is required";
        $errors = true;
    } else {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $username)) {
            $usernameErr = "Only letters and white space allowed";
            $errors = true;
        }
        if (strlen($username) > 16) {
            $usernameErr = "Username can't have more than 16 characters";
            $errors = true;
        }
    }
    if ($password == '') {
        $passwordErr = "Password is required";
        $errors = true;
    } else {
        if (strlen($password) < 8) {
            //$html .= '<div class="alert-danger">Palavra-passe tem menos de 8 caracteres.</div>';
            $passwordErr = "Password needs to have atleast 8 characters";
            $errors = true;
        }
    }


    $sql = "SELECT ID FROM user WHERE Email = :EMAIL LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":EMAIL", $email, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        //$html .= '<div class="alert-danger">O email indicado já se encontra registado.</div>';
        $emailErr = "Email is already in use";
        $errors = true;
    }

    if (!$errors) {
        //$html .= '<p>Informação válida proceder ao registo.</p>';
        $sql = "INSERT INTO user(Name,Username,Email,Password) VALUES(:NAME,:USERNAME,:EMAIL,:PASSWORD)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":NAME", $name, PDO::PARAM_STR);
        $stmt->bindValue(":USERNAME", $username, PDO::PARAM_STR);
        $stmt->bindValue(":EMAIL", $email, PDO::PARAM_STR);
        $stmt->bindValue(":PASSWORD", $password_hash_db, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            require '../objects/PHPMailer/PHPMailer.php';
            require '../objects/PHPMailer/SMTP.php';
            require '../objects/PHPMailer/Exception.php';
            $mail = new PHPMailer(true);
            $body = 'Hello, and welcome to House of Things!'
                . "<br><br>" . 'Sign up now and start your DIY smart home today.
            ' . "<br>" . 'If you have not signed up for a House of Things account, and have received this email by mistake, please ignore it.
            ' . "<br>" . 'This is an automated message to inform you that your email has been registered successfully.
            
            ' . "<br><br>" . 'House of Things Support Team'
            ."<br>".'--------------------------------------';


            try {
                $mail->Charset = EMAIL_CHARSET;                                   // Charset
                $mail->Encoding = EMAIL_ENCODING;                                 // Encode
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host = EMAIL_HOST;                  // Set the SMTP server to send through
                $mail->SMTPAuth = EMAIL_SMTPAUTH;                       // Enable SMTP authentication
                $mail->Username = EMAIL_USERNAME;                // SMTP username
                $mail->Password = EMAIL_PASSWORD;       // SMTP password
                $mail->SMTPSecure = PHPMAILER::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_STARTTLS`
                $mail->Port = EMAIL_PORT;                                           // TCP port to connect to, use 587 for gmail
                // Destinatários
                $mail->setFrom(EMAIL_USERNAME, 'House of Things Support Team');              // Set From
                $mail->addAddress($email);              // Add a recipient
                $mail->addBCC(EMAIL_USERNAME);
                // Content
                $mail->isHTML(true);                                    // Set email format to HTML
                $mail->Subject = 'Notification from House of Things Support Team';                            // Set Subject
                $mail->Body = $body;                  // Set message body

                $mail->send();                    // Send the email
            } catch (Exception $e) {
                //
            }
            $success = "Thank you for registering. An email confirming your registration has been sent.
            <br>You will shortly be redirected to the login page.";
            header('Refresh: 5; url = login.php');
        } else {
            //$html .= '<div class="container alert-danger">Erro ao inserir na Base de Dados.</div>';
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
    <div class="login-register">
        <div class="form-box">
            <form id="signup" action="?" class="input-group" method="POST">
                <div class="form-title">Create Account</div>
                <div class="form-social-icons">
                    <img src="../images/icons/signin_fb.png" alt="Signin_FB">
                    <img src="../images/icons/signin_tw.png" alt="Signin_TW">
                    <img src="../images/icons/signin_go.png" alt="Signin_GO">
                </div>
                <label for="text">Name</label>
                <input type="text" class="input-field" name="name" id="name" placeholder="Type your name" required>
                <div class="error"><?= $nameErr; ?></div>
                <label for="text">Username</label>
                <input type="text" class="input-field" name="username" id="username" placeholder="Type your username" required>
                <div class="error"><?= $usernameErr; ?></div>
                <label for="text">Email</label>
                <input type="email" class="input-field" name="email" id="email" placeholder="Type your email" required>
                <div class="error"><?= $emailErr; ?></div>
                <label for="text">Password</label>
                <input type="password" class="input-field" name="password" id="password" placeholder="Type your password" required>
                <div class="error"><?= $passwordErr; ?></div>
                <input type="checkbox" required name="checkbox" value="check" class="checkbox"><span>I agree to the terms & conditions</span>
                <button type="submit" name="register" value="Registar" class="submit-btn">Register</button>
                <div class="change-form">Already have an account? <a href="login.php">Login now</a></div>
                <div class="success"><?= $success; ?></div>
            </form>
        </div>
    </div>
</body>

</html>