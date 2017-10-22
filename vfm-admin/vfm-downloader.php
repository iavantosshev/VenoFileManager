<?php
/**
 * VFM - veno file manager downloader
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
require_once 'config.php';
require_once 'users/users.php';
require_once 'class.php';
require_once 'users/remember.php';

$cookies = new Cookies();
$encodeExplorer = new EncodeExplorer();
$encodeExplorer->init();

require_once 'translations/'.$encodeExplorer->lang.'.php';

$gateKeeper = new GateKeeper();
$gateKeeper->init();
$setUp = new SetUp();
$downloader = new Downloader();
$utils = new Utils();
$logger = new Logger();
$actions = new Actions();

$timeconfig = $setUp->getConfig('default_timezone');
$timezone = (strlen($timeconfig) > 0) ? $timeconfig : "UTC";
date_default_timezone_set($timezone);

$script_url = $setUp->getConfig('script_url');

$getzip = filter_input(INPUT_GET, 'zip', FILTER_SANITIZE_STRING);
$zipname = filter_input(INPUT_GET, 'n', FILTER_SANITIZE_STRING);

$getfile = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING);
$getfilelist = filter_input(INPUT_GET, 'dl', FILTER_SANITIZE_STRING);
$getcloud = filter_input(INPUT_GET, 'd', FILTER_SANITIZE_STRING);
$hash = filter_input(INPUT_GET, 'h', FILTER_SANITIZE_STRING);
$supah = filter_input(INPUT_GET, 'sh', FILTER_SANITIZE_STRING);
$playmp3 = filter_input(INPUT_GET, 'audio', FILTER_SANITIZE_STRING);
$getpass = filter_input(INPUT_GET, 'pw', FILTER_SANITIZE_STRING);
if ($getpass) {
    $getpass = urldecode($getpass);
}

$alt = $setUp->getConfig('salt');
$altone = $setUp->getConfig('session_name');
$maxfiles = $setUp->getConfig('max_zip_files');
$maxfilesize = $setUp->getConfig('max_zip_filesize');

$android = false;
$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);

if (stripos($useragent, 'android') !== false) {
    $android = true;
}

if ($getfile && $hash && $supah
    && $downloader->checkFile($getfile) == true
    && md5($hash.$alt.$getfile) === $supah
) {
    /**
    * Download single file 
    * (for non-logged users)
    */
    $headers = $downloader->getHeaders($getfile);

    if ($setUp->getConfig('direct_links')) {
        if ($headers['content_type'] == 'audio/mp3') {
            $logger->logPlay($headers['trackfile']);
        } else {
            $logger->logDownload($headers['trackfile']);
        }
        header('Location:'.$script_url.base64_decode($getfile));
        exit;
    }
        
    if ($downloader->download(
        $headers['file'], 
        $headers['filename'], 
        $headers['file_size'], 
        $headers['content_type'], 
        $headers['disposition'],
        $android
    ) === true ) {
        $logger->logDownload($headers['trackfile']);
    }
    exit;

} elseif ($getfile && $hash
    && $downloader->checkFile($getfile) == true
    && md5($alt.$getfile.$altone.$alt) === $hash
) {
    /**
    * Download single file, 
    * play Audio or show PDF 
    * (for logged users)
    */
    $headers = $downloader->getHeaders($getfile, $playmp3);

    if (($gateKeeper->isUserLoggedIn() 
        && $downloader->subDir($headers['dirname']) == true) 
        || $gateKeeper->isLoginRequired() == false
    ) {

        if ($setUp->getConfig('direct_links')) {
            if ($headers['content_type'] == 'audio/mp3') {
                $logger->logPlay($headers['trackfile']);
            } else {
                $logger->logDownload($headers['trackfile']);
            }
            header('Location:'.$script_url.base64_decode($getfile));
            exit;
        }

        if ($downloader->download(
            $headers['file'], 
            $headers['filename'], 
            $headers['file_size'], 
            $headers['content_type'], 
            $headers['disposition'],
            $android
        ) === true ) {
            if ($headers['content_type'] == 'audio/mp3') {
                $logger->logPlay($headers['trackfile']);
            } else {
                $logger->logDownload($headers['trackfile']);
            }
        }
        exit;
    }
    $_SESSION['error'] = '<i class="fa fa-ban"></i> Access denied';
    header('Location:'.$script_url);
    exit;

    /**
    * Download zipped folder
    */
} elseif ($getzip && $zipname) {

    $file = base64_decode($getzip);    
    $filename = base64_decode($zipname);
    $file_size = File::getFileSize($file);
    $content_type = 'application/zip';
    $disposition = 'attachment';

    if ($downloader->download(
        $file, 
        $filename, 
        $file_size, 
        $content_type, 
        $disposition,
        $android
    ) === true) {
        $logger->logDownload($filename, true);
    }
    unlink($file);
    exit;

    /**
    * Download multiple files 
    * as .zip archive
    */
} elseif ($getfilelist && file_exists('shorten/'.$getfilelist.'.json')) {
    $datarray = json_decode(file_get_contents('shorten/'.$getfilelist.'.json'), true);

    $hash = $datarray['hash'];
    $time = $datarray['time'];
    $pass = $datarray['pass'];
    $passa = true;

    if ($pass) { 
        $passa = false;
        if ($getpass && md5($getpass) === $pass) {
            $passa = true;
        }
    }

    if ($downloader->checkTime($time) == true && $passa === true) {

        $pieces = explode(",", $datarray['attachments']);

        $zippedfile = $downloader->createZip($pieces);

        if ($zippedfile['error'] !== false) {
            $_SESSION['error'] = $zippedfile['error'];
            header('Location:'.$script_url);
            exit;
        }

        $file = $zippedfile['file'];
        $logarray = $zippedfile['logarray'];
        $filename = $time.'.zip';
        $file_size = File::getFileSize($file);
        $content_type = 'application/zip';
        $disposition = 'attachment';

        if ($downloader->download(
            $file, 
            $filename, 
            $file_size, 
            $content_type, 
            $disposition,
            $android
        ) === true) {
            $logger->logDownload($logarray, true);
        }
        unlink($file);
        exit;
    }
    $_SESSION['error'] = '<i class="fa fa-ban"></i> Access denied';
    header('Location:'.$script_url);
    exit;
}
$_SESSION['error'] = $encodeExplorer->getString("link_expired");
header('Location:'.$script_url);
exit;
