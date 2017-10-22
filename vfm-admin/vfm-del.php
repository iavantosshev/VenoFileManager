<?php
/**
 * VFM - veno file manager remover
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
    die('Nothing here.');
}
require_once 'config.php';
require_once 'users/users.php';
require_once 'class.php';
require_once 'users/remember.php';

$cookies = new Cookies();
$encodeExplorer = new EncodeExplorer();
$encodeExplorer->init();
$gateKeeper = new GateKeeper();
$gateKeeper->init();

$setUp = new SetUp();
$timeconfig = $setUp->getConfig('default_timezone');
$timezone = (strlen($timeconfig) > 0) ? $timeconfig : "UTC";
date_default_timezone_set($timezone);

$downloader = new Downloader();
$utils = new Utils();
$logger = new Logger();
$actions = new Actions();

$getcloud = filter_input(INPUT_POST, "setdel", FILTER_SANITIZE_STRING);

$hash = filter_input(INPUT_POST, "h", FILTER_SANITIZE_STRING);
$doit = filter_input(INPUT_POST, "doit", FILTER_SANITIZE_STRING);
$time = filter_input(INPUT_POST, "t", FILTER_SANITIZE_STRING);

if ($doit != ($time * 12)) {
    die('Direct access not permitted');
}
$alt = $setUp->getConfig('salt');
$altone = $setUp->getConfig('session_name');

if ($hash && $time
    && $gateKeeper->isUserLoggedIn() 
    && $gateKeeper->isAllowed('delete_enable')
) {
    
    if (md5($alt.$time) === $hash
        && $downloader->checkTime($time) == true
        && $getcloud
    ) {
        $getcloud = explode(",", $getcloud);
        $totfiles = count($getcloud);
        foreach ($getcloud as $pezzo) {
            if ($downloader->checkFile($pezzo) == true) {
                $myfile = "../".urldecode(base64_decode($pezzo));
                $actions->deleteMulti($myfile);
            }
        }
        echo "ok";
    } else {
        echo "Action expired";
    }
} else {
    echo "Not enough data";
}