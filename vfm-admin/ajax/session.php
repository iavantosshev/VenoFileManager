<?php
/**
 * VFM - veno file manager: ajax/session.php
 *
 * Set session vars
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
require_once '../config.php';
session_name($_CONFIG["session_name"]);
session_start();
require_once '../class.php'; 
if (!GateKeeper::isAccessAllowed()) {
    die();
}
// update list view
$listview = filter_input(
    INPUT_POST, "lilstview", FILTER_SANITIZE_STRING
);
if ($listview) {
    $listdefault = SetUp::getConfig('list_view') ? SetUp::getConfig('list_view') : 'list';
    $listtype = $listview ? $listview : $listdefault;
    $_SESSION['listview'] = $listtype;
}

// update table paging lenght
$ilenght = filter_input(
    INPUT_POST, "iDisplayLength", FILTER_VALIDATE_INT
);
if ($ilenght) {
    $_SESSION['ilenght'] = $ilenght;
}
exit();