<?php
/**
 * VFM - veno file manager move files
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
if (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = SetUp::getConfig('lang');
}
require 'translations/'.$lang.'.php';
$gateKeeper = new GateKeeper();
$gateKeeper->init();

$setUp = new SetUp();
$timeconfig = $setUp->getConfig('default_timezone');
$timezone = (strlen($timeconfig) > 0) ? $timeconfig : 'UTC';
date_default_timezone_set($timezone);

$downloader = new Downloader();
$utils = new Utils();
$logger = new Logger();
$actions = new Actions();
$copy = filter_input(INPUT_POST, 'copy', FILTER_VALIDATE_BOOLEAN);
$getcloud = filter_input(INPUT_POST, 'setmove', FILTER_SANITIZE_STRING);
$dest = filter_input(INPUT_POST, 'dest', FILTER_SANITIZE_STRING);
$hash = filter_input(INPUT_POST, 'h', FILTER_SANITIZE_STRING);
$doit = filter_input(INPUT_POST, 'doit', FILTER_SANITIZE_STRING);
$time = filter_input(INPUT_POST, 't', FILTER_SANITIZE_STRING);

if ($doit != ($time * 12)) {
    die('Direct access not permitted');
}
$alt = $setUp->getConfig('salt');
$altone = $setUp->getConfig('session_name');

if ($hash && $time && $dest
    && $gateKeeper->isUserLoggedIn() 
    && $gateKeeper->isAllowed('move_enable')
) { 
    if (md5($alt.$time) === $hash
        && $downloader->checkTime($time) == true
        && $getcloud
    ) {
        $getcloud = explode(',', $getcloud);
        $dest = urldecode($dest);
        if (strlen($dest) > strlen($setUp->getConfig('starting_dir'))) {
            $cleandest = str_replace($setUp->getConfig('starting_dir'), '', $dest);
        } else {
            $cleandest = $dest;
        }
        $counter = 0;
        $total = count($getcloud);

        foreach ($getcloud as $pezzo) {
            if ($downloader->checkFile($pezzo) == true) {
                $filename = urldecode(base64_decode($pezzo));
                $myfile = '../'.$filename;
                $filepathinfo = $utils->mbPathinfo($filename);
                $basename = $filepathinfo['basename'];
                
                if ($copy) {
                    $filesize = filesize($myfile);

                    if ($actions->checkUserSpace($myfile, $filesize) == false) {

                        if ($counter > 0) {
                            $_SESSION['success'] = '<strong>'.$counter.'</strong> '.$encodeExplorer->getString('files_copied_to').': <strong>'.$cleandest.'</strong>';
                        }
                        $_SESSION['error'] = '<i class="fa fa-times"></i> '
                        .$encodeExplorer->getString('available_space_exhausted')
                        .': <strong>'.$basename.'</strong> ('.$setUp->formatSize($filesize).') ';
                        echo "ok";
                        exit;
                    }
                }
                if ($actions->renameFile($myfile, '.'.$dest.'/'.$basename, $basename, true, $copy)) {
                    $counter++;
                }
            }
        }



        if ($counter > 0) {

            if ($total == 1) {
                $counter = $basename;
            }

            if ($copy) {
                $_SESSION['success'] = '<strong>'.$counter.'</strong> '.$encodeExplorer->getString('files_copied_to').': <strong>'.$cleandest.'</strong>';
            } else {
                $_SESSION['success'] = '<strong>'.$counter.'</strong> '.$encodeExplorer->getString('files_moved_to').': <strong>'.$cleandest.'</strong>';
            }
        }
        echo "ok";
    } else {
        echo "Action expired";
    }
} else {
    echo "Not enough data";
}
