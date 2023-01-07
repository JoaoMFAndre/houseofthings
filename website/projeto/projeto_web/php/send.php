<?php

require_once '../api/config.php';
require_once '../api/core.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Informação de debug pré-definida
 */
$debug = '<b>GET</b>:' . print_r($_GET, true) . '<br>' .
        '<b>POST</b>:' . print_r($_POST, true) . '<br>';

$response = '';

// Obter variáveis do formulário
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING);

if ($email != '' && $name != '' && $mensagem != '') {
// Carregar PHPMailer
    $debug .= "Enviar email: $email, $name, $mensagem";
    require '../objects/PHPMailer/PHPMailer.php';
    require '../objects/PHPMailer/SMTP.php';
    require '../objects/PHPMailer/Exception.php';

// Criação de um email. `true` ativa exceptions
    $mail = new PHPMailer(true);
    $body = 'Thank you for contacting House of Things Support Team. This is an automated message to inform you that your message has been received.
    '."<br>".'We appreciate your patience and apologize for the delay in responding to you directly.
    
    '."<br><br>".'House of Things Support Team
    '."<br>".'--------------------------------------
    
    '."<br><br>".'E-mail: '.$email.'
    '."<br>".'Name: '.$name.'
    '."<br>".'Message received: '.$mensagem;

    try {
        /**
          Mailer:SMTP
          From email:[dep]-[nome]@ua.pt
          From Name : [nome que aparece nos e-mail enviados]
          SMTP Authentication: YES
          SMTP Security: TLS
          SMTP Port: 25
          SMPT Username: [dep]-[nome]@ua.pt
          SMTP Password: [senha de acesso à conta referida no SMTP Username]
          SMTP Host: smtp-servers.ua.pt
         *
          Nome:       Projeto Desenvolvimento de Software | ESAN
          e-mail:     esan-tesp-ds-paw@ua.pt
          login:      esan-tesp-ds-paw@ua.pt
          password:   8ee83a66c46001b7ee7b3ee886bf8375

         */
        if (DEBUG) {
            $mail -> SMTPDebug = SMTP::DEBUG_SERVER;
        }
        $mail->Charset = EMAIL_CHARSET;                                   // Charset
        $mail->Encoding = EMAIL_ENCODING;                                 // Encode
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host = EMAIL_HOST;                  // Set the SMTP server to send through
        $mail->SMTPAuth = EMAIL_SMTPAUTH;                       // Enable SMTP authentication
        $mail->Username = EMAIL_USERNAME;                // SMTP username
        $mail->Password = EMAIL_PASSWORD;       // SMTP password
        $mail->SMTPSecure = PHPMAILER::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_STARTTLS`
        $mail->Port = EMAIL_PORT;                                           // TCP port to connect to, use 587 for gmail
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;          // Enable SSL encryption; `PHPMailer::ENCRYPTION_SMTPS`
        //$mail->Port = 465;                                        // TCP port to connect to, use 465 for gmail
        // Destinatários
        $mail->setFrom(EMAIL_USERNAME, 'House of Things Support Team');              // Set From
        $mail->addAddress($email);              // Add a recipient
        //$mail->addReplyTo(EMAIL_USERNAME);
        //$mail->addCC(EMAIL_USERNAME);
        $mail->addBCC(EMAIL_USERNAME);
        // Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        // Content
        $mail->isHTML(true);                                    // Set email format to HTML
        $mail->Subject = 'Notification from House of Things Support Team';                            // Set Subject
        $mail->Body = $body;                  // Set message body
        //$mail->AltBody = $body;

        $mail->send();                    // Send the email
        $response = '<div class="alert-success">Mensagem enviada!</div>';
    } catch (Exception $e) {
        $response = '<div class="alert-danger">Mensagem não enviada. Mailer Error: ' . $mail->ErrorInfo . '</div>';
    }
    header('Location: ../index.html#contact');
    exit();
}
