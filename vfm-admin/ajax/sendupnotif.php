<?php
/**
 * VFM - veno file manager: ajax/sedupnotif.php
 *
 * Sends upload notification e-mail to selected users
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
require '../users/users.php';
require '../class.php';

$lang = filter_input(INPUT_POST, 'thislang', FILTER_SANITIZE_STRING);
$senduser = filter_input(INPUT_POST, 'senduser', FILTER_VALIDATE_EMAIL, FILTER_REQUIRE_ARRAY);
$postpath = filter_input(INPUT_POST, 'path', FILTER_SANITIZE_STRING);
$postfilename = filter_input(INPUT_POST, 'filename', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

require '../translations/'.$lang.'.php';

if ($senduser) {
    $encodeExplorer = new EncodeExplorer();

    $setfrom = SetUp::getConfig('email_from');

    if ($setfrom == null) {
        echo $encodeExplorer->getString("setup_email_application")."<br>";
        exit;
    }

    $timeconfig = SetUp::getConfig('default_timezone');
    $timezone = (strlen($timeconfig) > 0) ? $timeconfig : "UTC";
    date_default_timezone_set($timezone);

    $fullpath = urldecode($postpath);
    $path = str_replace(SetUp::getConfig('starting_dir'), '', $fullpath);

    $appname = SetUp::getConfig('appname');
    $time = SetUp::formatModTime(time());
    $appurl = SetUp::getConfig('script_url');
    $title = $encodeExplorer->getString("new_upload")." - ".$appname;
    $name = GateKeeper::getUserInfo('name');

    $altmessage = $time."\n\n";
    $altmessage .= $appurl."\n\n";
    $altmessage .= $encodeExplorer->getString('from')." : ".$name."\n\n";
    $upfiles = $time."<br><ul>";
    foreach ($postfilename as $filename) {
        $upfiles .= "<li> ".$path.$filename."</li>";
        $altmessage .= " - ".$path.$filename."\n";
    }
    $upfiles .= "</ul>";

    include_once '../mail/PHPMailerAutoload.php';
    $mail = new PHPMailer();
    $mail->CharSet = 'UTF-8';

    if (SetUp::getConfig('smtp_enable') == true) {

        $mail->isSMTP();
        $mail->SMTPDebug = ($setUp->getConfig('debug_mode') ? 2 : 0);
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

    $mail->setFrom($setfrom, $appname);
    $mail->Subject = $title;

    $mail->AddEmbeddedImage('../mail/mail-logo.png', 'logoimg', 'mail/mail-logo.png');

    // Retrieve the email template required
    $message = file_get_contents('../mail/template/template-uploaded-files.html');

    $message = str_replace('%app_url%', $appurl, $message);
    $message = str_replace('%app_name%', $appname, $message);
    $message = str_replace('%translate_from%', $encodeExplorer->getString('from'), $message);
    $message = str_replace('%username%', $name, $message);
    $message = str_replace('%upfiles%', $upfiles, $message);
    
    $mail->msgHTML($message);

    $mail->AltBody = $altmessage;

    // send notification mail to each selected user
    foreach ($senduser as $sender) {

        $mail->addAddress($sender, '<'.$sender.'>');
        if (!$mail->send()) {
            echo "Error sending mail";
        }
        $mail->ClearAddresses();
    }
    // // send notification mail to the uploader user
    // $mail->addAddress(GateKeeper::getUserInfo('email'), '<'.GateKeeper::getUserInfo('email').'>');
    // $mail->send();
    // $mail->ClearAddresses();
}