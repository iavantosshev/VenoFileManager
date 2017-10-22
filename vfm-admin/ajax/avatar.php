<?php
/**
 * VFM - veno file manager: ajax/avatar.php
 *
 * Save a new avatar image
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
/**
* Create a thumb from the uploaded image canvas
*
* @param string  $base64_string base64 string file
* @param boolean $output_file   final file
*
* @return null/$new_image
*/
function base64ToJpg($base64_string, $output_file) 
{
    $ifp = fopen($output_file, "wb"); 
    $data = explode(',', $base64_string);

    fwrite($ifp, base64_decode($data[1])); 
    fclose($ifp); 
    //return $output_file; 
}

$relativepath = '../images/avatars';
if (!is_dir($relativepath)) {
    mkdir($relativepath, 0755);         
}
$finalavatar = 'vfm-admin/images/avatars/'.$_POST['imgName'].'.png';
$relative = $relativepath.'/'.$_POST['imgName'].'.png';

base64ToJpg($_POST['imgData'], $relative);

echo $finalavatar;
