<?php
/**
 * VFM - veno file manager: ajax/zip.php
 *
 * Generate zip archive
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

@set_time_limit(0);
require_once '../config.php';
require_once '../class.php';

$downloader = new Downloader();
$setUp = new SetUp();
$encodeExplorer = new EncodeExplorer();
session_name($setUp->getConfig('session_name'));
session_start();

if (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = $setUp->getConfig('lang');
}

require_once '../translations/'.$lang.'.php';

$getfolder = filter_input(INPUT_POST, 'fold', FILTER_SANITIZE_STRING);
$hash = filter_input(INPUT_POST, 'dash', FILTER_SANITIZE_STRING);

$alt = $setUp->getConfig('salt');
$altone = $setUp->getConfig('session_name');

if ($getfolder && $hash
    && md5($alt.$getfolder.$altone) === $hash
) {

    $folder = base64_decode($getfolder);

    $zippedfile = $downloader->createZip(false, '.'.$folder, true);

    if ($zippedfile['error'] !== false) {
        echo json_encode($zippedfile);
        exit;
    }

    $folderpathinfo = Utils::mbPathinfo($folder);
    $folderbasename = Utils::normalizeStr(Utils::checkMagicQuotes($folderpathinfo['filename']));

    $filename = $folderbasename.'.zip';

    $file = $zippedfile['file'];

    if ($setUp->getConfig('enable_prettylinks') == true) {
        $downlink = "download/zip/".base64_encode($file)."/n/".base64_encode($filename);
    } else {
        $downlink = "vfm-admin/vfm-downloader.php?zip=".base64_encode($file)."&n=".base64_encode($filename);
    }
    $zippedfile['link'] = $downlink;

    $zippedfile['filename'] = $filename;

    echo json_encode($zippedfile);
}
exit;
