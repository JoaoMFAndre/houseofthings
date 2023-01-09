    <?php
    $guru = '10';
    $dsg_dbo = [
        'host' => 'mysql-sa.mgmt.ua.pt',
        'port' => '3306',
        'charset' => 'utf8',
        'dbname' => 'esan-dsg' . $guru,
        'username' => 'esan-dsg' . $guru . '-dbo',
        'password' => 'd0SyqTImPovUeIKE'
    ];
    $dsg_web = [
        'host' => 'mysql-sa.mgmt.ua.pt',
        'port' => '3306',
        'charset' => 'utf8',
        'dbname' => 'esan-dsg' . $guru,
        'username' => 'esan-dsg' . $guru . '-web',
        'password' => '88YvdxyHIM8CcNLA'
    ];

    //Descomentar o utilizador pretendido: DBO ou WEB
    #$db = $dsg_dbo;
    $db = $dsg_web;

    //UPLOAD
    define('WEB_SERVER', 'https://esan-tesp-ds-paw.web.ua.pt');
    //Grupo GURU
    define('WEB_ROOT', '/tesp-ds-g10/');

    define('SERVER_FILE_ROOT', '//ARCA.STORAGE.UA.PT/HOSTING/esan-tesp-ds-paw.web.ua.pt' . WEB_ROOT);
    define('UPLOAD_FOLDER', 'uploads/');
    define('PROJETO_FOLDER', 'projeto/projeto_web/');

    //UPLOAD PATH
    define('UPLOAD_PATH', SERVER_FILE_ROOT . UPLOAD_FOLDER);

    //AVATAR - User Profile Pic
    define('AVATAR_FOLDER', UPLOAD_FOLDER . 'avatar/');
    define('AVATAR_PATH', SERVER_FILE_ROOT . AVATAR_FOLDER);
    define('AVATAR_WEB_PATH', WEB_ROOT . AVATAR_FOLDER);
    define('AVATAR_DEFAULT', 'default.png');

    define('ATTACHMENTS_PATH', SERVER_FILE_ROOT . UPLOAD_FOLDER . 'attach/');

    define('DEBUG', true);

    //DEVICE ICON PATH
    define('ICON_FOLDER', 'images/icons/dashboard/');
    define('ICON_PATH', SERVER_FILE_ROOT . PROJETO_FOLDER . ICON_FOLDER);

    define('TYPE', array(
        'ac.svg'
    ));

    define('ENTRY_COLOR', array(
        'brown',
        'black',
        'blue',
        'green',
        'yellow',
        'orange',
        'red'
    ));

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
    define('EMAIL_CHARSET', 'UTF-8');
    define('EMAIL_ENCODING', 'base64');
    define('EMAIL_HOST', 'smtp-servers.ua.pt');
    define('EMAIL_SMTPAUTH', true);
    define('EMAIL_USERNAME', 'joaomfandre@ua.pt');
    define('EMAIL_PASSWORD', '!wswu1+Season');
    define('EMAIL_PORT', 25);
    define('EMAIL_FROM', 'Projeto Desenvolvimento de Software | ESAN');

    if (DEBUG) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }
