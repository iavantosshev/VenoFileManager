<?php
/**
 * VFM - veno file manager: ajax/usr-reg.php
 *
 * Send email to new pending user
 *
 * PHP version >= 5.3
 *
 * @category  PHP
 * @package   VenoFileManager
 * @author    Nicola Franchini <support@veno.it>
 * @copyright 2013 Nicola Franchini
 * @license   Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @link      http://filemanager.veno.it/
 */
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
    || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
) {
    exit;
}
require '../config.php';
session_name($_CONFIG["session_name"]);
session_start();

require_once '../class.php';

$timeconfig = SetUp::getConfig('default_timezone');
$timezone = (strlen($timeconfig) > 0) ? $timeconfig : "UTC";
date_default_timezone_set($timezone);

require_once '../users/users.php';
global $_USERS;

if (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = SetUp::getConfig("lang");
}
require "../translations/".$lang.".php";

if (file_exists('../users/users-new.php')) {
    include '../users/users-new.php';
} else {
    $newusers = array();
}

$encodeExplorer = new EncodeExplorer();
$updater = new Updater();

$setfrom = SetUp::getConfig('email_from');

if ($setfrom == null) {
    echo $encodeExplorer->getString("setup_email_application")."<br>";
    exit();
}

$filterType = array(
    'string' => FILTER_SANITIZE_STRING,
    'integer' => FILTER_VALIDATE_INT,
);

$post = array();

foreach ($_POST as $key => $value) {
    $filter = $filterType[gettype($value)];
    $value = filter_var($value, $filter);
    $post[$key] = $value;
}

$post = array_filter($post, 'strlen');

$postname = isset($post['user_name']) ? $post['user_name'] : false;
$postpass = isset($post['user_pass']) ? $post['user_pass'] : false;
$postpassconfirm = isset($post['user_pass_confirm']) ? $post['user_pass_confirm'] : false;
$postmail = isset($post['user_email']) ? $post['user_email'] : false;
$postcaptcha = isset($post['captcha']) ? $post['captcha'] : false;

unset($post['user_name'], $post['user_pass'], $post['user_pass_confirm'], $post['user_email'], $post['captcha']);

if (!$postname 
    || !$postmail
    || !$postpass 
    || !$postpassconfirm
) {
    echo '<div class="alert alert-warning" role="alert">'.$encodeExplorer->getString("fill_all_fields").' *</div>';
    exit();
}

$postname = preg_replace('/\s+/', '', $postname);

// minimum username lenght
if (strlen($postname) < 3) {
    echo '<div class="alert alert-danger" role="alert">'.$encodeExplorer->getString("minimum").'3 chars</div>';
    exit();
}

// passwords mismatch
if ($postpass !== $postpassconfirm) {
    echo '<div class="alert alert-danger" role="alert">'.$encodeExplorer->getString("passwords_dont_match").'</div>';
    exit();
}

// username already exists
if ($updater->findUser($postname)) {
    echo '<div class="alert alert-danger" role="alert"><strong>'.$postname.'</strong> '.$encodeExplorer->getString("file_exists").'</div>';
    exit();
}

// e-mail already exists
if ($updater->findEmail($postmail)) {
    echo '<div class="alert alert-warning" role="alert"><strong>'.$postmail.'</strong> '.$encodeExplorer->getString("file_exists").'</div>';
    exit();
}

// check capcha
if (Utils::checkCaptcha($postcaptcha, 'show_captcha_register') !== true) {
    echo '<div class="alert alert-warning" role="alert">'.$encodeExplorer->getString("wrong_captcha").'</div>';
    exit();
}
// if is already on pre-registration 
// send again an activation link
$resend = false;
$prereguser = $updater->findUserEmailPre($postmail);

// mail exist in pre-reg
if ($prereguser) {
    $resend = true;
    // username is different from the first associated to this e-mail
    // resend activation mail with first username chosen
    echo '<div class="alert alert-warning" role="alert"><strong>'.$postmail.'</strong> '.$encodeExplorer->getString("file_exists").'</div>';
    if ($prereguser !== $postname) {
        $postname = $prereguser;
    }
} else {
    // e-mail has never been used, check if username is alredy pre-registered 
    if ($updater->findUserPre($postname)) {
        echo '<div class="alert alert-warning" role="alert"><strong>'.$postname.'</strong> '.$encodeExplorer->getString("file_exists").'</div>';
        exit();
    }
}

$lifetime = strtotime("-1 day");
$newusers = $updater->removeOldReg($newusers, 'date', $lifetime);

$newuser = array();

$newuser['name'] = $postname;
$salt = SetUp::getConfig('salt');
$newuser['pass'] = crypt($salt.urlencode($postpass), Utils::randomString());
$newuser['email'] = $postmail;

foreach ($post as $custom => $value) {
    $newuser[$custom] = $value;
}

$date = date("Y-m-d", time());
$newuser['date'] = $date;

$activekey = md5($postname.$salt.$date);
$newuser['key'] = $activekey;

$appurl =  SetUp::getConfig('script_url');
$activationlink = $appurl."?act=".$activekey;

if (!$resend) {
    array_push($newusers, $newuser);
}
require '../mail/PHPMailerAutoload.php';

$mail = new PHPMailer();
$mail->CharSet = 'UTF-8';

if (SetUp::getConfig('smtp_enable') == true) {

    $mail->isSMTP();
    $mail->SMTPDebug = (SetUp::getConfig('debug_mode') ? 2 : 0);
    $mail->Debugoutput = 'html';

    $smtp_auth = SetUp::getConfig('smtp_auth');

    $mail->Host = SetUp::getConfig('smtp_server');
    $mail->Port = (int)SetUp::getConfig('port');
    if (version_compare(PHP_VERSION, '5.6.0', '>=')) {
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            )
        );
    }
    if (SetUp::getConfig('secure_conn') !== "none") {
        $mail->SMTPSecure = SetUp::getConfig('secure_conn');
    }
    
    $mail->SMTPAuth = $smtp_auth;

    if ($smtp_auth == true) {
        $mail->Username = SetUp::getConfig('email_login');
        $mail->Password = SetUp::getConfig('email_pass');
    }
}
$mail->setFrom($setfrom, SetUp::getConfig('appname'));
$mail->addAddress($postmail, '<'.$postmail.'>');

$mail->Subject = SetUp::getConfig('appname').": ".$encodeExplorer->getString('activate_account');

$altmessage = $encodeExplorer->getString('follow_this_link_to_activate')."/n"
    .$activationlink;

$mail->AddEmbeddedImage('../mail/mail-logo.png', 'logoimg', 'mail/mail-logo.png');

// Retrieve the email template required
$message = file_get_contents('../mail/template/template-activate-account.html');

// Replace the % with the actual information
$message = str_replace('%app_url%', $appurl, $message);
$message = str_replace('%app_name%', SetUp::getConfig('appname'), $message);

$message = str_replace(
    '%translate_follow_this_link_to_activate%', 
    $encodeExplorer->getString('follow_this_link_to_activate'), $message
);
$message = str_replace(
    '%activation_link%', 
    $activationlink, $message
);
$message = str_replace(
    '%translate_activate%', 
    $encodeExplorer->getString('activate'), $message
);

$message = str_replace(
    '%translate_username%', 
    $encodeExplorer->getString('username').": <strong>".$postname."<strong>", $message
);

$mail->msgHTML($message);

$mail->AltBody = $altmessage;

if (!$mail->send()) {
    echo '<div class="alert alert-danger" role="alert">Mailer Error: ' . $mail->ErrorInfo.'</div>';
} else {
    if ($updater->updateRegistrationFile($newusers, "../users/")) {
        echo '<div class="alert alert-success" role="alert">'.$encodeExplorer->getString('activation_link_sent').'</div>';   
    } else {
        echo '<div class="alert alert-danger" role="alert"><strong>users-new</strong> file update failed</div>';
    }
}
exit;
