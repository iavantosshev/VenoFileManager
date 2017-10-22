<?php
/**
 * VFM - Veno File Manager 2 - main classes
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

/**
 * Some classes are based on Encode Explorer 6.3
 * http://encode-explorer.siineiolekala.net (GPL Licence)
 */
$vfm_version = '2.6.3';
/**
 * Displays images (icons and thumbnails)
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class ImageServer
{
    /**
    * Checks if an image is requested and displays one if needed
    *
    * @return true/false
    */
    public static function showImage()
    {
        $thumb = filter_input(INPUT_GET, 'thumb', FILTER_SANITIZE_STRING);
        if ($thumb) {
            $inline = (isset($_GET['in']) ? true : false);
            if (strlen($thumb) > 0
                && (SetUp::getConfig('thumbnails') == true
                || SetUp::getConfig('inline_thumbs') == true)
            ) {
                ImageServer::showThumbnail(base64_decode($thumb), $inline);
            }
            return true;
        }
        return false;
    }

    /**
    * Checks if isEnabledPdf()
    *
    * @return true/false
    */
    public static function isEnabledPdf()
    {
        if (class_exists('Imagick')) {
            return true;
        }
        return false;
    }

    /**
    * Preapre PDF for thumbnail
    *
    * @param string $file the file to convert
    *
    * @return false | $image
    */
    public static function openPdf($file)
    {
        if (!ImageServer::isEnabledPdf()) {
            return false;
        }
        $file = urldecode($file);
        $img = new Imagick($file.'[0]');
        $img->setImageFormat('png');
        $str = $img->getImageBlob();
        $im2 = imagecreatefromstring($str);
        $image = $im2 ? $im2 : imagecreatefromjpeg('vfm-admin/images/placeholder.jpg');
        return $im2;
    }

    /**
    * Creates and returns a thumbnail image object from an image file
    *
    * @param string  $file   file to convert
    * @param boolean $inline thumbs or zoom
    *
    * @return null | $new_image
    */
    public static function createThumbnail($file, $inline = false)
    {
        if ($inline == true) {
            // $thumbsize = SetUp::getConfig('inline_tw');
            $thumbsize = 420;

            $max_width = $thumbsize;
            $max_height = $thumbsize;
        } else {
            if (is_int(SetUp::getConfig('thumbnails_width'))) {
                $max_width = SetUp::getConfig('thumbnails_width');
            } else {
                $max_width = 760;
            }
            if (is_int(SetUp::getConfig('thumbnails_height'))) {
                $max_height = SetUp::getConfig('thumbnails_height');
            } else {
                $max_height = 800;
            }
        }
        if (File::isPdfFile($file)) {
            $image = ImageServer::openPdf($file);
        } else {
            $image = ImageServer::openImage($file);
        }
        if ($image == false) {
            return;
        }

        $width = imagesx($image);
        $height = imagesy($image);

        $new_width = $max_width;
        $new_height = $max_height;

        // set background color for transparent images
        $bgR = 240;
        $bgG = 240;
        $bgB = 240;

        if ($inline == true) {
            // crop to square thumbnail
            if ($width > $height) {
                $y = 0;
                $x = ($width - $height) / 2;
                $smallestSide = $height;
            } else {
                $x = 0;
                $y = ($height - $width) / 2;
                $smallestSide = $width;
            }
            $thumb = imagecreatetruecolor($new_width, $new_height);
            $bgcolor = imagecolorallocate($thumb, $bgR, $bgG, $bgB);
            imagefilledrectangle($thumb, 0, 0, $new_width, $new_height, $bgcolor);
            imagecopyresampled($thumb, $image, 0, 0, $x, $y, $new_width, $new_height, $smallestSide, $smallestSide);
        } else {
            // resize mantaining aspect ratio
            if (($width/$height) > ($new_width/$new_height)) {
                $new_height = $new_width * ($height / $width);
            } else {
                $new_width = $new_height * ($width / $height);
            }
            $new_width = ($new_width >= $width ? $width : $new_width);
            $new_height = ($new_height >= $height ? $height : $new_height);
            $thumb = imagecreatetruecolor($new_width, $new_height);
            $bgcolor = imagecolorallocate($thumb, $bgR, $bgG, $bgB);
            imagefilledrectangle($thumb, 0, 0, $new_width, $new_height, $bgcolor);
            imagecopyresampled($thumb, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        }
        return $thumb;
    }

    /**
    * Function for displaying the thumbnail.
    * Includes attempts at cacheing it so that generation is minimised.
    *
    * @param string  $file   file to convert
    * @param boolean $inline thumbs or zoom
    *
    * @return $image
    */
    public static function showThumbnail($file, $inline = false)
    {
        $thumbsdir = 'vfm-admin/thumbs';
        
        if (!is_dir($thumbsdir)) {
            if (!mkdir($thumbsdir, 0755)) {
                Utils::setError('error creating /vfm-admin/thumbs/ directory');
                return false;
            }
        }

        if ($inline === true) {
            $thumbname = md5($file).'.jpg';
        } else {
            $thumbname = md5($file).'-big.jpg';
        }

        $thumbpath = $thumbsdir.'/'.$thumbname;

        if (!file_exists($thumbpath)) {
            $file = EncodeExplorer::extraChars($file);
            $image = ImageServer::createThumbnail($file, $inline);
            imagejpeg($image, $thumbpath, 80);
            imagedestroy($image); 
        }

        if ($inline) {
            return $thumbpath;
        } else {
            header('Location: '.$thumbpath);
            exit;
        }
    }
    /**
    * A helping function for opening different types of image files
    *
    * @param string $file the file to convert
    *
    * @return $img
    */
    public static function openImage($file)
    {
        $file = urldecode($file);
        $imageInfo = getimagesize($file);
        $memoryNeeded = (($imageInfo[0] * $imageInfo[1]) * $imageInfo['bits']);
        $memoryLimit = (strlen(ini_get('memory_limit')) > 0 ? ImageServer::returnBytes(ini_get('memory_limit')) : false);
        $lowmemory = false;

        /**
        * Try to set the needed memory_limit
        */
        if ($memoryLimit && $memoryNeeded > $memoryLimit) {
            $lowmemory = true;
            $formatneeded = (round($memoryNeeded/1024/1024)+10).'M';
            if (ini_set('memory_limit', $formatneeded)) {
                $lowmemory = false;
            }
        } 

        if ($lowmemory === false) {
            switch ($imageInfo['mime']) {
            case 'image/jpeg':
                $img = imagecreatefromjpeg($file);
                break;
            case 'image/gif':
                $img = imagecreatefromgif($file);
                break;
            case 'image/png':
                $img = imagecreatefrompng($file);
                break;
            default:
                $img = imagecreatefromjpeg($file);
                break;
            }
        } else {
            imagecreatefromjpeg('vfm-admin/images/placeholder.jpg');
        }
        return $img;
    }

    /**
    * Convert M K G in bytes
    *
    * @param string $size_str original size
    *
    * @return converted size
    */
    public static function returnBytes($size_str)
    {
        switch (substr($size_str, -1)) {
        case 'M':
        case 'm':
            return (int)$size_str * 1048576;
        case 'K':
        case 'k':
            return (int)$size_str * 1024;
        case 'G':
        case 'g':
            return (int)$size_str * 1073741824;
        default:
            return $size_str;
        }
    }
}

/**
 * The class for logging user activity
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class Logger
{
    /**
    * Print log file
    *
    * @param string $message the message to log
    * @param string $relpath relative path of log file
    *
    * @return $message
    */
    public static function log($message, $relpath = 'vfm-admin/')
    {
        if (SetUp::getConfig('log_file') == true) {
            $logjson = $relpath.'log/'.date('Y-m-d').'.json';

            if (Location::isFileWritable($logjson)) {

                $message['time'] = date('H:i:s');

                if (file_exists($logjson)) {
                    $oldlog = json_decode(file_get_contents($logjson), true);
                } else {
                    $oldlog = array();
                }

                $daily = date('Y-m-d');
                $oldlog[$daily][] = $message;

                file_put_contents($logjson, json_encode($oldlog, JSON_FORCE_OBJECT));

            } else {
                Utils::setError('The script does not have permissions to write inside "vfm-admin/log" folder. check CHMOD');
                return;
            }
        }
    }
    /**
    * Log user login
    *
    * @return $message
    */
    public static function logAccess()
    {
        $message = '<td>'.GateKeeper::getUserInfo('name').'</td>'
        .'<td><span class="label label-warning">ACCESS</span></td>'
        .'<td>--</td><td class="wordbreak">--</td>';
        Logger::log($message);
    }

    /**
    * Log user creation of folders and files
    *
    * @param string $path  the path to set
    * @param string $isDir may be 'dir' or 'file'
    *
    * @return $message
    */
    public static function logCreation($path, $isDir)
    {
        $path = addslashes($path);

        $message = array(
            'user' => GateKeeper::getUserInfo('name'),
            'action' => 'ADD',
            'type' => $isDir ? 'folder':'file',
            'item' => $path
        );
        Logger::log($message);
        if (!$isDir && SetUp::getConfig('notify_upload')) {
            Logger::emailNotification($path, 'upload');
        }
        if ($isDir && SetUp::getConfig('notify_newfolder')) {
            Logger::emailNotification($path, 'newdir');
        }
    }

    /**
    * Log user deletion of folders and files
    *
    * @param string  $path   the path to set
    * @param boolean $isDir  file or directory
    * @param boolean $remote true if called inside vfm-admin
    *
    * @return $message
    */
    public static function logDeletion($path, $isDir, $remote = false)
    {
        $path = addslashes($path);
        $message = array(
            'user' => GateKeeper::getUserInfo('name'),
            'action' => 'REMOVE',
            'type' => $isDir ? 'folder':'file',
            'item' => $path
        );
        if ($remote == false) {
            Logger::log($message);
        } else {
            Logger::log($message, '');
        }
    }
    
    /**
    * Log download of single files
    *
    * @param string $path   the path to set
    * @param bool   $folder if is folder
    *
    * @return $message
    */
    public static function logDownload($path, $folder = false)
    {
        $user = GateKeeper::getUserInfo('name') ? GateKeeper::getUserInfo('name') : '--';
        $mailmessage = '';
        $type = $folder ? 'folder' : 'file';
        if (is_array($path)) {
            foreach ($path as $value) {
                $path = addslashes($value);
                $message = array(
                    'user' => $user,
                    'action' => 'DOWNLOAD',
                    'type' => $type,
                    'item' => $path
                );
                $mailmessage .= $path."\n";
                Logger::log($message, "");
            }
        } else {
            $path = addslashes($path);
            $message = array(
                'user' => $user,
                'action' => 'DOWNLOAD',
                'type' => $type,
                'item' => $path
            );
            $mailmessage = $path;
            Logger::log($message, "");
        }
        if (SetUp::getConfig('notify_download')) {
            Logger::emailNotification($mailmessage, 'download');
        }
    }

    /**
    * Log play of single track
    *
    * @param string $path the path to set
    *
    * @return $message
    */
    public static function logPlay($path)
    {
        $path = addslashes($path);
        $message = array(
            'user' =>  GateKeeper::getUserInfo('name') ? GateKeeper::getUserInfo('name') : '--',
            'action' => 'PLAY',
            'type' => 'file',
            'item' => $path
        );
        Logger::log($message, '');
    }

    /**
    * Send email notfications for activity logs
    *
    * @param string $path   the path to set
    * @param string $action may be 'download' | 'upload' | 'newdir' | 'login'
    *
    * @return $message
    */
    public static function emailNotification($path, $action = false)
    {
        global $encodeExplorer;

        if (strlen(SetUp::getConfig('upload_email')) > 5) {

            $time = SetUp::formatModTime(time());
            $appname = SetUp::getConfig('appname');
            switch ($action) {
            case 'download':
                $title = $encodeExplorer->getString('new_download');
                break;
            case 'upload':
                $title = $encodeExplorer->getString('new_upload');
                break;
            case 'newdir':
                $title = $encodeExplorer->getString('new_directory');
                break;
            case 'login':
                $title = $encodeExplorer->getString('new_access');
                break;
            default:
                $title = $encodeExplorer->getString('new_activity');
                break;
            }
            $message = $time."\n\n";
            $message .= "IP   : ".$_SERVER['REMOTE_ADDR']."\n";
            $message .= $encodeExplorer->getString('user')." : ".GateKeeper::getUserInfo('name')."\n";
            $message .= $encodeExplorer->getString('path')." : ".$path."\n";

    // send to multiple recipients
            // $sendTo = SetUp::getConfig('upload_email').',cc1@example.com,cc2@example.com';            
            $sendTo = SetUp::getConfig('upload_email');
            $from = "=?UTF-8?B?".base64_encode($appname)."?=";
            mail(
                $sendTo,
                "=?UTF-8?B?".base64_encode($title)."?=",
                $message,
                "Content-type: text/plain; charset=UTF-8\r\n".
                "From: ".$from." <noreply@{$_SERVER['SERVER_NAME']}>\r\n".
                "Reply-To: ".$from." <noreply@{$_SERVER['SERVER_NAME']}>"
            );
        }
    }
}

/**
 * The class controls single user update panel
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class Updater
{
    /**
    * Call update user functions
    *
    * @return $message
    */
    public static function init()
    {
        global $updater;

        $posteditname = filter_input(INPUT_POST, 'user_new_name', FILTER_SANITIZE_STRING);
        $postoldname = filter_input(INPUT_POST, 'user_old_name', FILTER_SANITIZE_STRING);
        $posteditpass = filter_input(INPUT_POST, 'user_new_pass', FILTER_SANITIZE_STRING);
        $posteditpasscheck = filter_input(INPUT_POST, 'user_new_pass_confirm', FILTER_SANITIZE_STRING);
        $postoldpass = filter_input(INPUT_POST, 'user_old_pass', FILTER_SANITIZE_STRING);
        $posteditmail = filter_input(INPUT_POST, 'user_new_email', FILTER_VALIDATE_EMAIL);
        $postoldmail = filter_input(INPUT_POST, 'user_old_email', FILTER_VALIDATE_EMAIL);

        if ($postoldpass && $posteditname) {
            $updater->updateUser(
                $posteditname,
                $postoldname,
                $posteditpass,
                $posteditpasscheck,
                $postoldpass,
                $posteditmail,
                $postoldmail
            );
        }
    }

    /**
    * Update username or password
    *
    * @param string $posteditname      new username
    * @param string $postoldname       current username
    * @param string $posteditpass      new password
    * @param string $posteditpasscheck check password
    * @param string $postoldpass       old password
    * @param string $posteditmail      new email
    * @param string $postoldmail       old email
    *
    * @return global $users updated
    */
    public function updateUser(
        $posteditname,
        $postoldname,
        $posteditpass,
        $posteditpasscheck,
        $postoldpass,
        $posteditmail,
        $postoldmail
    ) {
        global $encodeExplorer;
        global $updater;
        global $_USERS;
        global $users;
        $users = $_USERS;
        $passa = true;

        if (GateKeeper::isUser($postoldname, $postoldpass)) {

            if ($posteditname != $postoldname) {
                if ($updater->findUser($posteditname)) {
                        Utils::setError(
                            '<strong>'.$posteditname.'</strong> '
                            .$encodeExplorer->getString('file_exists')
                        );
                        $passa = false;
                        return;
                }
                Cookies::removeCookie($postoldname);
                Updater::updateAvatar($postoldname, $posteditname);
                $updater->updateUserData($postoldname, 'name', $posteditname);
            }
            if ($posteditmail != $postoldmail) {
                if ($updater->findEmail($posteditmail)) {
                        Utils::setError(
                            '<strong>'.$posteditmail.'</strong> '
                            .$encodeExplorer->getString('file_exists')
                        );
                        $passa = false;
                        return;
                }
                $updater->updateUserData($postoldname, 'email', $posteditmail);
            }
            if ($posteditpass) {
                if ($posteditpass === $posteditpasscheck) {
                    $updater->updateUserPwd($postoldname, $posteditpass);
                } else {
                    $encodeExplorer->setErrorString('wrong_pass');
                    $passa = false;
                    return;
                }
            }
            if ($passa == true) {
                $updater->updateUserFile('', $posteditname);
            }
        } else {
            $encodeExplorer->setErrorString('wrong_pass');
        }

    }

    /**
    * Update user password
    *
    * @param string $checkname  username
    * @param string $changepass new pass
    *
    * @return global $users updated
    */
    public function updateUserPwd($checkname, $changepass)
    {
        global $_USERS;
        global $users;
        $utenti = $_USERS;

        foreach ($utenti as $key => $value) {
            if ($value['name'] === $checkname) {
                $salt = SetUp::getConfig('salt');
                $users[$key]['pass'] = crypt($salt.urlencode($changepass), Utils::randomString());
                break;
            }
        }
    }

    /**
    * Update user data
    *
    * @param string $checkname username to find
    * @param string $type      info to change
    * @param string $changeval new value
    *
    * @return global $users updated
    */
    public function updateUserData($checkname, $type, $changeval)
    {
        global $updater;
        global $_USERS;
        global $users;
        $utenti = $_USERS;

        foreach ($utenti as $key => $value) {
            if ($value['name'] === $checkname) {
                if ($changeval) {
                    $users[$key][$type] = $changeval;
                } else {
                    unset($users[$key][$type]);
                }
                break;
            }
        }
    }

    /**
    * Update user Avatar if user changes name or delete it
    *
    * @param string $checkname username to find
    * @param string $newname   new username to assign
    * @param string $dir       relative path to /images/avatars/
    *
    * @return global $users updated
    */
    public static function updateAvatar($checkname = false, $newname = false, $dir = 'vfm-admin/')
    {
        $avatars = glob($dir.'images/avatars/*.png');
        $filename = md5($checkname);

        foreach ($avatars as $avatar) {

            $fileinfo = Utils::mbPathinfo($avatar);
            $avaname = $fileinfo['filename'];

            if ($avaname === $filename) {
                
                if ($newname) {
                    $newname = md5($newname);
                    rename($dir.'images/avatars/'.$avaname.'.png', $dir.'images/avatars/'.$newname.'.png');
                } else {
                    unlink($dir.'images/avatars/'.$avaname.'.png');
                }
                break;
            }
        }
    }

    /**
    * Delete user
    *
    * @param string $checkname username to find
    *
    * @return global $users updated
    */
    public function deleteUser($checkname)
    {
        global $_USERS;
        global $users;
        $utenti = $_USERS;

        foreach ($utenti as $key => $value) {
            if ($value['name'] === $checkname) {
                unset($users[$key]);
                Cookies::removeCookie($checkname, '');
                Updater::updateAvatar($checkname, false, '');
                break;
            }
        }
    }
    /**
    * Look if email exists
    *
    * @param string $userdata email to look for
    *
    * @return true/false
    */
    public function findEmail($userdata)
    {
        global $_USERS;
        $utenti = $_USERS;
        
        if (is_array($utenti)) {
            foreach ($utenti as $value) {
                if (isset($value['email']) && $value['email'] === $userdata) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
    * Look if user exists
    *
    * @param string $userdata username to look for
    *
    * @return true/false
    */
    public function findUser($userdata)
    {
        global $_USERS;
        $utenti = $_USERS;

        if (is_array($utenti)) {
            foreach ($utenti as $value) {
                if ($value['name'] === $userdata) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
    * Look if user exists inside users-new
    *
    * @param string $userdata username to look for
    *
    * @return true/false
    */
    public function findUserPre($userdata)
    {
        global $newusers;
        $utenti = $newusers;

        if (is_array($utenti)) {
            foreach ($utenti as $value) {
                if ($value['name'] === $userdata) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
    * Look if same username and email are taken
    *
    * @param string $usermail email to look for
    *
    * @return true/false
    */
    public function findUserEmailPre($usermail)
    {
        global $newusers;
        $utenti = $newusers;
        
        if (is_array($utenti)) {
            foreach ($utenti as $value) {
                if (isset($value['email']) && isset($value['name'])) {
                    if ($value['email'] === $usermail) {
                        return $value['name'];
                    }
                }
            }
        }
        return false;
    }

    /**
    * Look if user exists inside users-new
    *
    * @param string $userdata username to look for
    *
    * @return $thisuser array or false
    */
    public function findUserKey($userdata)
    {
        global $newusers;
        $utenti = array();
        $utenti = $newusers;
        $defaultfolders = SetUp::getConfig('registration_user_folders');

        foreach ($utenti as $utente) {
            if ($utente['key'] === $userdata) {
                $thisuser = array();
                foreach ($utente as $attrkey => $userattr) {
                    $thisuser[$attrkey] = $userattr;
                }
                $thisuser['role'] = SetUp::getConfig('registration_role');

                if ($defaultfolders) {
                    $arrayfolders = json_decode($defaultfolders, false);

                    if (in_array('vfm_reg_new_folder', $arrayfolders)) {
                        
                        $userfolderpath = $value['name'];

                        $newpath = SetUp::getConfig('starting_dir').$userfolderpath;

                        if (!is_dir($newpath)) {
                            mkdir($newpath);
                        }

                        $arrayfolders = array_diff($arrayfolders, array('vfm_reg_new_folder'));
                        $arrayfolders[] = $userfolderpath;
                        $userdir = json_encode(array_values($arrayfolders));
                    } else {
                        $userdir = $defaultfolders;
                    }

                    $thisuser['dir'] = $userdir;
                    if (strlen(SetUp::getConfig('registration_user_quota')) > 0) {
                        $thisuser['quota'] = SetUp::getConfig('registration_user_quota');
                    }
                }
                unset($thisuser['key']);
                return $thisuser;
            }
        }
        return false;
    }

    /**
    * Update users file
    *
    * @param string $option   what has been updated
    * @param string $postname username updated
    *
    * @return response
    */
    public function updateUserFile($option = '', $postname = false)
    {
        global $encodeExplorer;
        global $users;
        $usrs = '$_USERS = ';

        if (false == (file_put_contents(
            'vfm-admin/users/users.php',
            "<?php\n\n $usrs".var_export($users, true).";\n"
        ))
        ) {
            Utils::setError('error updating users list');
        } else {
            if ($option == 'password') {
                Utils::setSuccess($encodeExplorer->getString('password_reset'));
            } else {
                if ($postname) {
                    $edited = '<strong>'.$postname.'</strong> ';
                    Utils::setSuccess($edited.$encodeExplorer->getString('updated'));
                }
            }
            $_SESSION['vfm_user_name'] = null;
            $_SESSION['vfm_logged_in'] = null;
            $_SESSION['vfm_user_space'] = null;
            $_SESSION['vfm_user_used'] = null;
            // session_destroy();
        }
    }

    /**
    * Prepare registration user
    *
    * @param array $newusers new users list
    * @param array $path     relative path to file
    *
    * @return response
    */
    public function updateRegistrationFile($newusers, $path = '')
    {
        global $encodeExplorer;
        $usrs = '$newusers = ';

        if (false == (file_put_contents(
            $path.'users-new.php',
            "<?php\n\n $usrs".var_export($newusers, true).";\n"
        ))
        ) {
            return false;
        } else {
            return true;
        }
    }

    /**
    * Prepare registration user
    *
    * @param array $newusers new users list
    *
    * @return response
    */
    public function confirmRegistration($newusers)
    {
        global $encodeExplorer;
        $usrs = '$newusers = ';

        if (false == (file_put_contents(
            '../users-new.php',
            "<?php\n\n $usrs".var_export($newusers, true).";\n"
        ))
        ) {
            return false;
        } else {
            return true;
        }
    }

    /**
    * Remove user from value
    *
    * @param array  $array array where to search
    * @param key    $key   key to search
    * @param string $value vluue to search
    *
    * @return null/$new_image
    */
    public function removeUserFromValue($array, $key, $value)
    {
        foreach ($array as $subKey => $subArray) {
            if ($subArray[$key] == $value) {
                unset($array[$subKey]);
            }
        }
        return $array;
    }

    /**
    * Remove old standby registrations
    *
    * @param array  $array    array where to search
    * @param key    $key      key to search
    * @param string $lifetime max lifetime
    *
    * @return null/$new_image
    */
    public function removeOldReg($array, $key, $lifetime)
    {
        foreach ($array as $subKey => $subArray) {
            $data = strtotime($subArray[$key]);

            if ($data <= $lifetime) {
                unset($array[$subKey]);
            }
        }
        return $array;
    }

    /**
    * Update .htaccess
    *
    * @param string  $starting_dir selected uploads directory
    * @param boolean $direct_links give or not the access
    *
    * @return void
    */
    public function updateHtaccess($starting_dir, $direct_links = false)
    {
        $htaccess = '.'.$starting_dir.".htaccess";

        $start_marker = "# begin VFM rules";
        $end_marker   = "# end VFM rules";

        // Split out the existing file into the preceeding lines, and those that appear after the marker
        $pre_lines = $post_lines = $existing_lines = array();

        $found_marker = $found_end_marker = false;

        if (file_exists($htaccess)) {
            $hta = file_get_contents($htaccess);  // Read the whole .htaccess file into mem
            $lines = explode(PHP_EOL, $hta); // Use newline to differentiate between records

            foreach ( $lines as $line ) {
                if (!$found_marker && false !== strpos($line, $start_marker)) {
                    $found_marker = true;
                    continue;
                } elseif (!$found_end_marker && false !== strpos($line, $end_marker) ) {
                    $found_end_marker = true;
                    continue;
                }
                if (!$found_marker) {
                    $pre_lines[] = $line;
                } elseif ($found_marker && $found_end_marker) {
                    $post_lines[] = $line;
                } else {
                    $existing_lines[] = $line;
                }
            }
        }

        $insertion = array();
        $insertion[] = "php_flag engine off";
        if (!$direct_links && strlen($starting_dir) > 2) {
            $insertion[] = "Order Deny,Allow";
            $insertion[] = "Deny from all";
        }
        // Check to see if there was a change
        if ($existing_lines === $insertion) {
            return true;
        }
        // Generate the new file data
        $new_file_data = implode(
            "\n", array_merge(
                $pre_lines,
                array( $start_marker ),
                $insertion,
                array( $end_marker ),
                $post_lines
            )
        );
        $fpp = fopen($htaccess, "w+");
        if ($fpp === false) {
            return false;
        }
        fwrite($fpp, $new_file_data);
        fclose($fpp);
        return true;
    }

}

/**
 * The class controls cookies for remember me
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class Cookies
{
    /**
    * Set remember me cookie
    *
    * @param string $postusername user name
    * @param string $path         relative path to /users/
    *
    * @return updated remember.php file
    */
    public static function removeCookie($postusername = false, $path = 'vfm-admin/')
    {
        global $_REMEMBER;

        if (array_key_exists($postusername, $_REMEMBER)) {
            unset($_REMEMBER[$postusername]);
        
            $rmb = '$_REMEMBER = ';
            if (false == (file_put_contents(
                $path.'users/remember.php',
                "<?php\n\n $rmb".var_export($_REMEMBER, true).";\n"
            ))
            ) {
                Utils::setError('error removing remember key');
                return false;
            }
        }
    }

    /**
    * Set remember me cookie
    *
    * @param string $postusername user name
    *
    * @return cookie and key set
    */
    public function setCookie($postusername = false)
    {
        global $_REMEMBER;

        $rewrite = false;
        $salt = SetUp::getConfig('salt');
        $rmsha = md5($salt.sha1($postusername.$salt));
        $rmshaved = md5($rmsha);

        setcookie('rm', $rmsha, time()+ (60*60*24*365));
        setcookie('vfm_user_name', $postusername, time()+ (60*60*24*365));

        if (array_key_exists($postusername, $_REMEMBER)
            && $_REMEMBER[$postusername] !== $rmshaved
        ) {
            $rewrite = true;
        }

        if (!array_key_exists($postusername, $_REMEMBER)
            || $rewrite == true
        ) {
            $_REMEMBER[$postusername] = $rmshaved;
            $rmb = '$_REMEMBER = ';
            if (false == (file_put_contents(
                'vfm-admin/users/remember.php',
                "<?php\n\n $rmb".var_export($_REMEMBER, true).";\n"
            ))
            ) {
                Utils::setError('error setting your remember key');
                return false;
            }
        }
    }


    /**
    * Check remember me key
    *
    * @param string $name user name
    * @param string $key  rememberme key
    *
    * @return login via cookie
    */
    public function checkKey($name, $key)
    {
        global $_REMEMBER;
        global $gateKeeper;
        
        if (array_key_exists($name, $_REMEMBER)) {
            if ($_REMEMBER[$name] === md5($key)) {
                $_SESSION['vfm_user_name'] = $name;
                $_SESSION['vfm_logged_in'] = 1;

                $usedspace = $gateKeeper->getUserSpace();

                if ($usedspace !== false) {
                    $userspace = $gateKeeper->getUserInfo('quota')*1024*1024;
                    $_SESSION['vfm_user_used'] = $usedspace;
                    $_SESSION['vfm_user_space'] = $userspace;
                } else {
                    $_SESSION['vfm_user_used'] = null;
                    $_SESSION['vfm_user_space'] = null;
                }
            }
        }
        return false;
    }

    /**
    * Check rememberme cookie
    *
    * @return checkKey() | false
    */
    public function checkCookie()
    {
        global $cookies;

        if (isset($_COOKIE['rm']) && isset($_COOKIE['vfm_user_name'])) {
            $name = $_COOKIE['vfm_user_name'];
            $key = $_COOKIE['rm'];
            return $cookies->checkKey($name, $key);
        }
        return false;
    }
}

/**
 * The class controls logging in and authentication
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class GateKeeper
{
    /**
    * Check user satus
    *
    * @return $message
    */
    public static function init()
    {
        global $encodeExplorer;
        global $gateKeeper;
        global $cookies;

        if (isset($_GET['logout'])) {
            setcookie('rm', '', time() -(60*60*24*365));
            $_SESSION['vfm_user_name'] = null;
            $_SESSION['vfm_logged_in'] = null;            
            $_SESSION['vfm_user_space'] = null;
            $_SESSION['vfm_user_used'] = null;
            // session_destroy();
        } else {
            $cookies->checkCookie();
        }

        $postusername = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING);
        $postuserpass = filter_input(INPUT_POST, 'user_pass', FILTER_SANITIZE_STRING);
        $postcaptcha = filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_STRING);
        $rememberme = filter_input(INPUT_POST, 'vfm_remember', FILTER_SANITIZE_STRING);

        if ($postusername && $postuserpass) {

            if (Utils::checkCaptcha($postcaptcha) == true) {

                if (GateKeeper::isUser($postusername, $postuserpass)) {
                    if ($rememberme == 'yes') {
                        $cookies->setCookie($postusername);
                    }
                    $_SESSION['vfm_user_name'] = $postusername;
                    $_SESSION['vfm_logged_in'] = 1;

                    $usedspace = $gateKeeper->getUserSpace();

                    if ($usedspace !== false) {
                        $userspace = $gateKeeper->getUserInfo('quota')*1024*1024;
                        $_SESSION['vfm_user_used'] = $usedspace;
                        $_SESSION['vfm_user_space'] = $userspace;
                    } else {
                        $_SESSION['vfm_user_used'] = null;
                        $_SESSION['vfm_user_space'] = null;
                    }
                    if (SetUp::getConfig('notify_login')) {
                        Logger::emailNotification('--', 'login');
                    }
                    header('location:?dir=');
                    exit;
                } else {
                    $encodeExplorer->setErrorString('wrong_pass');
                }
            } else {
                $encodeExplorer->setErrorString('wrong_captcha');
            }
        }
    }

    /**
    * Delete multifile
    *
    * @return updates total available space
    */
    public function getUserSpace()
    {
        global $gateKeeper;

        if ($gateKeeper->getUserInfo('dir') !== null
            && $gateKeeper->getUserInfo('quota') !== null
        ) {
            $totalsize = 0;
            $userfolders = json_decode($gateKeeper->getUserInfo('dir'), true);
            $userfolders = $userfolders ? $userfolders : array();

            foreach ($userfolders as $myfolder) {
                $checkfolder = urldecode(SetUp::getConfig('starting_dir').$myfolder);
                if (file_exists($checkfolder)) {
                    $ritorno = FileManager::getDirSize($checkfolder);
                    $totalsize += $ritorno['size'];
                }
            }
            return $totalsize;
        }
        return false;
    }

    /**
    * Login validation
    *
    * @param string $userName user name
    * @param string $userPass password
    *
    * @return true/false
    */
    public static function isUser($userName, $userPass)
    {
        $salt = SetUp::getConfig('salt');
        foreach (SetUp::getUsers() as $user) {
            if ($user['name'] === $userName) {
                $passo = $salt.urlencode($userPass);
                if (crypt($passo, $user['pass']) == $user['pass']) {
                    return true;
                }
                break;
            }
        }
        return false;
    }

    /**
    * Check if login is required to view lists
    *
    * @return true/false
    */
    public static function isLoginRequired()
    {
        if (SetUp::getConfig('require_login') == false) {
            return false;
        }
        return true;
    }

    /**
    * Check if user is logged in
    *
    * @return true/false
    */
    public static function isUserLoggedIn()
    {
        if (isset($_SESSION['vfm_user_name'])
            && isset($_SESSION['vfm_logged_in'])
            && $_SESSION['vfm_logged_in'] == 1
        ) {
            return true;
        }
        return false;
    }

    /**
    * Check if target action is allowed
    *
    * @param string $action action to check, available values:
    *
    * 'sendfiles_enable'
    * 'upload_enable'
    * 'newdir_enable'
    * 'move_enable'
    * 'delete_enable'
    * 'rename_enable'
    * 'delete_dir_enable'
    * 'rename_dir_enable'
    *
    * @return true/false
    */
    public static function isAllowed($action)
    {
        if (GateKeeper::isAccessAllowed()) {
            if ((SetUp::getConfig($action) == true && GateKeeper::getUserInfo('role') == 'admin')
                || GateKeeper::getUserInfo('role') == 'superadmin'
            ) {
                return true;
            }
        }
        return false;
    }

    /**
    * Check if user can access
    *
    * @return true/false
    */
    public static function isAccessAllowed()
    {
        if (!GateKeeper::isLoginRequired() || GateKeeper::isUserLoggedIn()) {
            return true;
        }
        return false;
    }

    /**
    * Get user info ('name', 'pass', 'role', 'dir', 'email')
    *
    * @param int $info index of corresponding user info
    *
    * @return info requested
    */
    public static function getUserInfo($info)
    {
        if (GateKeeper::isUserLoggedIn() == true
            && isset($_SESSION['vfm_user_name'])
            && strlen($_SESSION['vfm_user_name']) > 0
        ) {
            $username = $_SESSION['vfm_user_name'];
            $curruser = Utils::getCurrentUser($username);

            if (isset($curruser[$info]) && strlen($curruser[$info]) > 0) {
                return $curruser[$info];
            }
            return null;
        }
    }

    /**
    * Get user's avatar image, or return default
    *
    * @param strnig $username  user to search
    * @param strnig $adminarea relative
    *
    * @return image path
    */
    public static function getAvatar($username, $adminarea = 'vfm-admin/')
    {
        $avaimg = md5($username).'.png';
        
        if (!file_exists($adminarea.'images/avatars/'.$avaimg)) {
            $avaimg = 'default.png';
        }
        return SetUp::getConfig('script_url').'vfm-admin/images/avatars/'.$avaimg;
    }

    /**
    * Check if user is SuperAdmin
    *
    * @return true/false
    */
    public static function isSuperAdmin()
    {
        if (GateKeeper::getUserInfo('role') == 'superadmin') {
            return true;
        }
        return false;
    }

    /**
    * Show login box
    *
    * @return true/false
    */
    public static function showLoginBox()
    {
        if (!GateKeeper::isUserLoggedIn()
            && count(SetUp::getUsers()) > 0
        ) {
            return true;
        }
        return false;
    }
}


/**
 * The class for any kind of file managing (new folder, upload, etc).
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class FileManager
{
    /**
    * The main function, checks if the user wants to perform any supported operations
    *
    * @param string $location current location
    *
    * @return checks if any action is required
    */
    public function run($location)
    {
        $postuserdir = filter_input(INPUT_POST, 'userdir', FILTER_SANITIZE_STRING);
        $postnewname = filter_input(INPUT_POST, 'newname', FILTER_SANITIZE_STRING);

        if ($postuserdir) {
            // add new folder
            $dirname = Utils::normalizeStr($postuserdir);
            Actions::newFolder($location, $dirname);

        } elseif (isset($_FILES['userfile']['name'])) {
            // upload files
            $this->uploadMulti($_FILES['userfile']);
            die();
        } elseif ($postnewname) {
            // rename files or folders
            $postoldname = filter_input(INPUT_POST, 'oldname', FILTER_SANITIZE_STRING);

            $postnewname = Utils::normalizeStr($postnewname);
            $this->setRename($postoldname, $postnewname);

        } else {
            // no post action
            $getdel = filter_input(INPUT_GET, 'del', FILTER_SANITIZE_STRING);
            // delete files or folders
            if ($getdel
                && GateKeeper::isUserLoggedIn()
                && GateKeeper::isAllowed('delete_enable')
            ) {
                $getdel = str_replace(' ', '+', $getdel);
                $getdel = urldecode(base64_decode($getdel));
                
                $getdel = EncodeExplorer::extraChars($getdel);

                $this->setDel($getdel);
            }
        }
    }

    /**
     * Get the directory size
     *
     * @param string $path directory
     *
     * @return integer
     */
    public static function getDirSize($path)
    {
        $bytestotal = 0;
        $path = realpath($path);
        if ($path !== false) {
            foreach (
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
                ) as $object
            ) {
                $bytestotal += $object->getSize();
            }
        }
        $total['size'] = $bytestotal;
        return $total;
    }

    /**
    * Setup file to delete
    *
    * @param string $getdel path to delete
    *
    * @return call deleteFile()
    */
    public function setDel($getdel)
    {
        global $gateKeeper;

        if (Utils::checkDel($getdel) == false) {
            Utils::setError('<i class="fa fa-ban"></i> Permission denied');
            return;
        }
        if (is_dir($getdel)) {
            if ($gateKeeper->getUserSpace() !== false) {
                $ritorno = FileManager::getDirSize("./".$getdel);
                $totalsize = $ritorno['size'];

                if ($totalsize > 0) {
                    Actions::updateUserSpaceDeep($totalsize);
                }
            }
            Actions::deleteDir($getdel);

            Utils::setWarning('<i class="fa fa-trash-o"></i> '.substr($getdel, strrpos($getdel, '/') + 1));
            // Directory successfully deleted, sending log notification
            Logger::logDeletion('./'.$getdel, true);

        } elseif (is_file($getdel)) {
            Actions::deleteFile($getdel);
        }
    }


    /**
    * Setup file renaming
    *
    * @param string $postoldname original file or directory name
    * @param string $postnewname new file or directory name
    *
    * @return call renameFile();
    */
    public function setRename($postoldname, $postnewname)
    {
        if (GateKeeper::isAccessAllowed()
            && GateKeeper::isAllowed('rename_enable')
        ) {
            $postthisext = filter_input(INPUT_POST, "thisext", FILTER_SANITIZE_STRING);
            $postthisdir = filter_input(INPUT_POST, "thisdir", FILTER_SANITIZE_STRING);

            if ($postoldname && $postnewname) {
                if ($postthisext) {
                    $oldname = $postthisdir.$postoldname.".".$postthisext;
                    $newname = $postthisdir.Utils::normalizeStr($postnewname).".".$postthisext;
                } else {
                    $oldname = $postthisdir.$postoldname;
                    $newname = $postthisdir.Utils::normalizeStr($postnewname);
                }
                Actions::renameFile($oldname, $newname, $postnewname);
            }
        }
    }

    /**
    * Prepare multiple files for upload
    *
    * @param array $coda $_FLES['userfile']
    *
    * @return call uploadFIle()
    */
    public function uploadMulti($coda)
    {
        global $location;
        if ($location->editAllowed()
            && GateKeeper::isUserLoggedIn()
            && GateKeeper::isAccessAllowed()
            && GateKeeper::isAllowed('upload_enable')
        ) {
            // Number of files to uploaded
            $num_files = count($coda['tmp_name']);
            $totnames = array();
            for ($i=0; $i < $num_files; $i++) {
                
                $filepathinfo = Utils::mbPathinfo($coda['name'][$i]);

                $filename = $filepathinfo['filename'];
                $filex = $filepathinfo['extension'];
                $thename = $filepathinfo['basename'];
                $tempname = $coda['tmp_name'][$i];
                $tipo = $coda['type'][$i];
                $filerror = $coda['error'][$i];

                if (in_array($thename, $totnames)) {
                    $thename = $filename.$i.".".$filex;
                }

                if (Utils::notList(
                    $thename,
                    array('.htaccess','.htpasswd','.ftpquota')
                ) == true) {

                    array_push($totnames, $thename);

                    if ($thename) {
                        Actions::uploadFile($location, $thename, $tempname, $tipo);
                        // check uplad errors
                        FileManager::upLog($filerror);
                    }
                }
            }
        }
    }

    /**
    * Add log uploading errors
    *
    * @param num $filerr array value of $_FILES['userfile']['error'][$i]
    *
    * @return error response
    */
    public static function upLog($filerr)
    {
        $error_types = array(
        0=>'OK',
        1=>'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        2=>'The uploaded file exceeds the MAX_FILE_SIZE specified in the HTML form.',
        3=>'The uploaded file was only partially uploaded.',
        4=>'No file was uploaded.',
        6=>'Missing a temporary folder.',
        7=>'Failed to write file to disk.',
        8=>'A PHP extension stopped the file upload.',
        'post_max_size' => 'The uploaded file exceeds the post_max_size directive in php.ini',
        'max_file_size' => 'File is too big',
        'min_file_size' => 'File is too small',
        'accept_file_types' => 'Filetype not allowed',
        'max_number_of_files' => 'Maximum number of files exceeded',
        'max_width' => 'Image exceeds maximum width',
        'min_width' => 'Image requires a minimum width',
        'max_height' => 'Image exceeds maximum height',
        'min_height' => 'Image requires a minimum height',
        'abort' => 'File upload aborted',
        'image_resize' => 'Failed to resize image'
        );

        $error_message = $error_types[$filerr];
        if ($filerr > 0) {
            Utils::setError(' :: '.$error_message);
        }
    }

    /**
    * Append .txt to extension
    *
    * @param string $name      name to modify
    * @param string $extension extension to check
    *
    * @return string $name filename with .txt appended
    */
    public static function safeExtension($name, $extension)
    {
        $evil = array(
            'php','php3','php4','php5','htm','html','phtm','phtml',
            'shtm','shtml','asp','pl','py','jsp','sh','cgi','htaccess',
            'htpasswd','386','bat','cmd','pl','ddl','bin'
            );
        if (in_array($extension, $evil)) {
            $name = $name.'.txt';
        }
        return $name;
    }
}


/**
 * Main actions
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class Actions
{
    /**
    * Rename files
    *
    * @param string $oldname    original file path
    * @param string $newname    new file path
    * @param string $thenewname new name
    * @param bool   $move       move file
    * @param bool   $copy       copy file
    *
    * @return boolean
    */
    public static function renameFile($oldname, $newname, $thenewname, $move = false, $copy = false)
    {

        global $encodeExplorer;

        $oldname = $encodeExplorer->extraChars($oldname);
        $newname = $encodeExplorer->extraChars($newname);

        if (!file_exists($oldname)) {
            Utils::setError('<i class="fa fa-exclamation-circle"></i> <strong>' .$oldname. '</strong> does not exist');
            return false;
        }

        if (Actions::fileExists($newname)) {
            Utils::setWarning(
                '<i class="fa fa-info-circle"></i> <strong>' .$thenewname. '</strong> '
                .$encodeExplorer->getString('file_exists')
            );
            return false;
        }
        if ($copy) {
            if (!copy($oldname, $newname)) {
                Utils::setError('<i class="fa fa-exclamation-circle"></i> <strong>' .$thenewname. '</strong> can\'t be copied');
                return false;
            } else {
                Actions::updateUserSpace($oldname, true);
                Utils::setSuccess(
                    '<strong>'.$thenewname. '</strong> '
                    .$encodeExplorer->getString('copied')
                );
                return true;
            }
        } else {
            if (!rename($oldname, $newname)) {
                Utils::setError('<i class="fa fa-exclamation-circle"></i> <strong>' .$thenewname. '</strong> can\'t be edited');
                return false;
            } else {
                if ($move === true) {
                    Actions::deleteThumb(substr($oldname, 3), true);
                } else {
                    Actions::deleteThumb($oldname);  
                }
                Utils::setSuccess(
                    '<strong>'.$thenewname. '</strong> '
                    .$encodeExplorer->getString('updated')
                );
                return true;
            }
        }
    }

    /**
    * Show available directories
    *
    * @param string $dir        base path
    * @param string $currentdir current directory browsing
    *
    * @return directories tree
    */
    public static function walkDir($dir, $currentdir)
    {
        $relativedir = $dir;

        if (is_dir($relativedir)) {
            if ($dh = opendir($relativedir)) {
                echo "<ul>";
                while (false !== ($file = readdir($dh))) {
                    if (($file !== '.') && ($file !== '..')) {
                        if (is_dir($relativedir.$file)) {
                            echo '<li>';
                            if ($relativedir.$file."/" === $currentdir) {
                                echo '<i class="fa fa-folder-open-o"></i> '.$file;
                            } else {
                                echo '<a href="#" data-dest="'.urlencode($dir.$file).'" class="movelink">';
                                echo '<i class="fa fa-folder-o"></i> '.$file.'</a>';
                            }
                            Actions::walkDir($dir.$file.'/', $currentdir);
                            echo '</li>';
                        }
                    }
                }
                echo '</ul>';
            }
        }
    }

    /**
    * Check if folder exists (case insensitive)
    *
    * @param string $path          what to search
    * @param bool   $caseSensitive case sensitive search
    *
    * @return adds new folder
    */
    public static function fileExists($path, $caseSensitive = false)
    {

        $pathinfo = Utils::mbPathinfo($path);

        $filename = $pathinfo['filename'];
        $dirname = $pathinfo['dirname'];

        if (file_exists($path)) {
            return true;
        }
        if ($caseSensitive) {
            return false;
        }
        // Handle case insensitive requests
        $fileArray = glob($dirname . '/*', GLOB_NOSORT);
        $fileNameLowerCase = strtolower($path);

        foreach ($fileArray as $file) {
            if (strtolower($file) == $fileNameLowerCase) {
                return true;
            }
        }
        return false;
    }

    /**
    * Create new folder
    *
    * @param string $location where to create new folder
    * @param string $dirname  new dir name
    *
    * @return adds new folder
    */
    public static function newFolder($location, $dirname)
    {
        global $encodeExplorer;

        if (GateKeeper::isAllowed('newdir_enable')) {
            if (strlen($dirname) > 0) {

                if (!$location->editAllowed()) {
                    // The system configuration does not allow uploading here
                    $encodeExplorer->setErrorString('upload_not_allowed');
                    return false;
                }
                if (!$location->isWritable()) {
                    // The target directory is not writable
                    $encodeExplorer->setErrorString('upload_dir_not_writable');
                    return false;
                }
                if (Actions::fileExists($location->getDir(true, false, false, 0).$dirname)) {
                    Utils::setWarning(
                        '<i class="fa fa-folder"></i>  <strong>'.$dirname.'</strong> '
                        .$encodeExplorer->getString('file_exists')
                    );
                    return false;
                }
                if (!mkdir($location->getDir(true, false, false, 0).$dirname, 0755)) {
                    // Error creating a new directory
                    $encodeExplorer->setErrorString('new_dir_failed');
                    return false;
                }
                Utils::setSuccess(
                    '<i class="fa fa-folder"></i> <strong>'.$dirname.'</strong> '
                    .$encodeExplorer->getString('created')
                );
                // Directory successfully created, sending e-mail notification
                Logger::logCreation($location->getDir(true, false, false, 0).$dirname, true);
                return true;
            }
            $encodeExplorer->setErrorString('new_dir_failed');
            return false;
        }
    }

    /**
    * Upload file
    *
    * @param string $location where to upload
    * @param string $thename  file name
    * @param string $tempname temp name
    * @param string $tipo     file type
    *
    * @return uploads file
    */
    public static function uploadFile($location, $thename, $tempname, $tipo)
    {
        global $encodeExplorer;

        $extension = File::getFileExtension($thename);

        $filepathinfo = Utils::mbPathinfo($thename);
        $name = Utils::normalizeStr($filepathinfo['filename']).'.'.$extension;

        $upload_dir = $location->getFullPath();

        $upload_file = $upload_dir.$name;

        if (Actions::fileExists($upload_file)) {
            Utils::setWarning(
                '<span><i class="fa fa-info-circle"></i> '.$name.' '
                .$encodeExplorer->getString('file_exists').'</span> '
            );
        } else {

            $mime_type = $tipo;
            $clean_file = $upload_dir.FileManager::safeExtension($name, $extension);

            if (!$location->editAllowed() || !$location->isWritable()) {
                Utils::setError(
                    '<span><i class="fa fa-exclamation-triangle"></i> '
                    .$encodeExplorer->getString('upload_not_allowed').'</span> '
                );

            } elseif (Utils::notList($extension, SetUp::getConfig('upload_allow_type')) == true
                || Utils::inList($extension, SetUp::getConfig('upload_reject_extension')) == true
            ) {
                Utils::setError(
                    '<span><i class="fa fa-exclamation-triangle"></i> '
                    .$name.'<strong>.'.$extension.'</strong> '
                    .$encodeExplorer->getString('upload_type_not_allowed').'</span> '
                );

            } elseif (!is_uploaded_file($tempname)) {
                $encodeExplorer->setErrorString('failed_upload');

            } elseif (!move_uploaded_file($tempname, $clean_file)) {
                $encodeExplorer->setErrorString('failed_move');

            } elseif (Actions::checkUserSpace($clean_file) == false) {
                $encodeExplorer->setErrorString('upload_exceeded');
                unlink($clean_file);

            } else {
                chmod($clean_file, 0755);
                Utils::setSuccess('<span><i class="fa fa-check-circle"></i> '.$name.'</span> ');
                // file successfully uploaded, sending log notification
                Logger::logCreation($location->getDir(true, false, false, 0).$name, false);
            }
        }
    }

    /**
    * Delete directory
    *
    * @param string $dir directory to delete
    *
    * @return deletes directory
    */
    public static function deleteDir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir.'/'.$object) == 'dir') {
                        Actions::deleteDir($dir.'/'.$object);
                    } else {
                        unlink($dir.'/'.$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
    * Delete file
    *
    * @param string $file file to delete
    *
    * @return deletes file
    */
    public static function deleteFile($file)
    {
        if (is_file($file)) {

            Actions::updateUserSpace($file, false);
            unlink($file);
            Actions::deleteThumb($file);
            Utils::setWarning('<i class="fa fa-trash-o"></i> '.basename($file));
            Logger::logDeletion('./'.$file, false);
        }
    }

    /**
    * Delete multifile
    *
    * @param string $file file to delete
    *
    * @return deletes file
    */
    public static function deleteMulti($file)
    {
        if (is_file($file)) {

            Actions::updateUserSpace($file, false);
            unlink($file);
            Actions::deleteThumb(substr($file, 3), true);
            Utils::setWarning('<i class="fa fa-trash-o"></i> '.basename($file).' | ');
            Logger::logDeletion(substr($file, 1), false, true);
        }
    }

    /**
    * Delete thumbnail
    *
    * @param string $file  file to delete
    * @param bool   $multi called from vfm-del.php or vfm-move.php
    *
    * @return deletes file
    */
    public static function deleteThumb($file, $multi = false)
    {
        if ($multi == false) {
            $thumbdir = 'vfm-admin/thumbs/';
        } else {
            $thumbdir = 'thumbs/';
        }
        $thumbname = md5($file);

        $thumb = $thumbdir.$thumbname.'.jpg';
        $thumb_big = $thumbdir.$thumbname.'-big.jpg';

        if (is_file($thumb)) {
            unlink($thumb);
        }
        if (is_file($thumb_big)) {
            unlink($thumb_big);
        }
    }

    /**
    * Check if user has space to upload
    *
    * @param string $file     file to check
    * @param string $thissize size to check
    *
    * @return true/false
    */
    public static function checkUserSpace($file, $thissize = false)
    {
        if (isset($_SESSION['vfm_user_used'])
            && isset($_SESSION['vfm_user_space'])
        ) {
            if (!$thissize) {
                $thissize = File::getFileSize($file);
            }
            $oldused = $_SESSION['vfm_user_used'];
            $newused = $oldused + $thissize;
            $freespace = $_SESSION['vfm_user_space'];
            
            if ($newused > $freespace) {
                return false;
            } else {
                return true;
            }
        }
        return true;
    }

    /**
    * Update user used space by file (add or subtract)
    *
    * @param string  $file file to add/subtract
    * @param boolean $add  true/false add or subtract
    *
    * @return updates total used space
    */
    public static function updateUserSpace($file, $add = false)
    {
        if (isset($_SESSION['vfm_user_used'])) {

            $thissize = File::getFileSize($file);
            $usedspace = $_SESSION['vfm_user_used'];

            if ($add == true) {
                $usedspace = $usedspace + $thissize;
            } else {
                $usedspace = $usedspace - $thissize;
            }
            $_SESSION['vfm_user_used'] = $usedspace;
        }
    }

    /**
    * Update user used space by size (subtract)
    *
    * @param string $size size to add/subtract
    *
    * @return updates total used space
    */
    public static function updateUserSpaceDeep($size)
    {
        if (isset($_SESSION['vfm_user_used'])) {

            $thissize = $size;
            $usedspace = $_SESSION['vfm_user_used'];
            $usedspace = $usedspace - $thissize;

            $_SESSION['vfm_user_used'] = $usedspace;
        }
    }
}


/**
 * Dir class holds the information about one directory in the list
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class Dir
{
    public $name;
    public $location;

    /**
    * Constructor
    *
    * @param string $name     path name
    * @param string $location current location
    *
    * @return directory name and location
    */
    public function __construct($name, $location)
    {
        $this->name = $name;
        $this->location = $location;
    }

    /**
    * Get directory location
    *
    * @return directory location
    */
    public function getLocation()
    {
        return $this->location->getDir(true, false, false, 0);
    }
    
    /**
    * Get directory name
    *
    * @return directory name
    */
    public function getName()
    {
        return Utils::normalizeName($this->name);
    }

    /**
    * Get directory HTML name
    *
    * @return directory name
    */
    public function getNameHtml()
    {
        return htmlspecialchars(Utils::normalizeName($this->name));
    }

    /**
    * Get directory name urlencoded
    *
    * @return directory name
    */
    public function getNameEncoded()
    {
        return rawurlencode(Utils::normalizeName($this->name));
    }
}

/**
 * File class that holds the information about one file in the list
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class File
{
    public $name;
    public $location;
    public $size;
    public $type;
    public $modTime;
    /**
    * Constructor
    *
    * @param string $name     path name
    * @param string $location current location
    *
    * @return all file data
    */
    public function __construct($name, $location)
    {
        $this->name = $name;
        $this->location = $location;

        $this->type = File::getFileType(
            $this->location->getDir(true, false, false, 0).$this->getName()
        );
        $this->size = File::getFileSize(
            $this->location->getDir(true, false, false, 0).$this->getName()
        );
        $this->modTime = filemtime(
            $this->location->getDir(true, false, false, 0).$this->getName()
        );
    }

    /**
    * Get name
    *
    * @return name
    */
    public function getName()
    {
        return  Utils::normalizeName($this->name);
    }

    /**
    * Get name encoded
    *
    * @return name urlencoded
    */
    public function getNameEncoded()
    {
        return rawurlencode(Utils::normalizeName($this->name));
    }

    /**
    * Get name html formatted
    *
    * @return HTML name
    */
    public function getNameHtml()
    {
        return htmlspecialchars(Utils::normalizeName($this->name));
    }

    /**
    * Get file size
    *
    * @return size
    */
    public function getSize()
    {
        return $this->size;
    }

    /**
    * Get type
    *
    * @return file type
    */
    public function getType()
    {
        return $this->type;
    }

    /**
    * Get time
    *
    * @return mod time
    */
    public function getModTime()
    {
        return $this->modTime;
    }

    /**
    * Determine the size of a file
    *
    * @param string $file file to calculate
    *
    * @return sizeInBytes
    */
    public static function getFileSize($file)
    {
        $sizeInBytes = filesize($file);
        /**
        * If filesize() fails (with larger files),
        * try to get the size with fseek
        */
        if (!$sizeInBytes || $sizeInBytes < 0) {
            $fho = fopen($file, 'r');
            $size = '0';
            $char = '';
            fseek($fho, 0, SEEK_SET);
            $count = 0;
            while (true) {
                //jump 1 MB forward in file
                fseek($fho, 1048576, SEEK_CUR);
                //check if we actually left the file
                if (($char = fgetc($fho)) !== false) {
                    $count ++;
                } else {
                    //else jump back where we were before leaving and exit loop
                    fseek($fho, -1048576, SEEK_CUR);
                    break;
                }
            }
            $size = bcmul('1048577', $count);
            $fine = 0;
            while (false !== ($char = fgetc($fho))) {
                $fine ++;
            }
            //and add them
            $sizeInBytes = bcadd($size, $fine);
            fclose($fho);
        }
        return $sizeInBytes;
    }

    /**
    * Determine the type of a file
    *
    * @param string $filepath file to calculate
    *
    * @return call getFileExtension
    */
    public static function getFileType($filepath)
    {
        return File::getFileExtension($filepath);
    }

    /**
    * Determine extension of a file
    *
    * @param string $filepath file to check
    *
    * @return ext
    */
    public static function getFileExtension($filepath)
    {
        return pathinfo($filepath, PATHINFO_EXTENSION);
    }

    /**
    * Check if file is image
    *
    * @return true/false
    */
    public function isImage()
    {
        $types = array(
            'jpg',
            'jpeg',
            'gif',
            'png',
        );
        $type = strtolower($this->getType());
        
        if (in_array($type, $types)) {
            return true;
        }
        return false;
    }

    /**
    * Check if file is audio playable
    *
    * @return true/false
    */
    public function isAudio()
    {
        $types = array(
            'mp3',
            'wav',
        );
        $type = strtolower($this->getType());

        if (in_array($type, $types)) {
            return true;
        }
        return false;
    }

    /**
    * Check if file is video playable
    *
    * @return true/false
    */
    public function isVideo()
    {
        $types = array(
            'mp4',
            'webm',
            'flv',
            // 'ogv',
        );
        $type = strtolower($this->getType());

        if (in_array($type, $types)) {
            return true;
        }
        return false;
    }

    /**
    * Check if file is a pdf
    *
    * @return true/false
    */
    public function isPdf()
    {
        if (strtolower($this->getType()) == 'pdf') {
            return true;
        }
        return false;
    }

    /**
    * Check if target file is a pdf
    *
    * @param string $file file to calculate
    *
    * @return true/false
    */
    public static function isPdfFile($file)
    {
        if (strtolower(File::getFileType($file)) == 'pdf') {
            return true;
        }
        return false;
    }

    /**
    * Check if file is valid for creating thumbnail
    *
    * @return true/false
    */
    public function isValidForThumb()
    {
        if (SetUp::getConfig('thumbnails') !== true && SetUp::getConfig('inline_thumbs') !== true) {
            return false;
        }
        if ($this->isImage() || ($this->isPdf() && ImageServer::isEnabledPdf())
        ) {
            return true;
        }
        return false;
    }

    /**
    * Check if file is valid for playing audio
    *
    * @return true/false
    */
    public function isValidForAudio()
    {
        if (SetUp::getConfig('playmusic') == true
            && $this->isAudio()
        ) {
            return true;
        }
        return false;
    }

    /**
    * Check if file is valid for playing video
    *
    * @return true/false
    */
    public function isValidForVideo()
    {
        if (SetUp::getConfig('playvideo') == true
            && $this->isVideo()
        ) {
            return true;
        }
        return false;
    }

}


/**
 * Location class holds the information about path location
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class Location
{
    public $path;

    /**
    * Set the current directory
    *
    * @return current directory
    */
    public function init()
    {
        $getdir = filter_input(INPUT_GET, 'dir', FILTER_SANITIZE_STRING);

        if (!$getdir || !is_dir($getdir)) {
            $this->path = $this->splitPath(SetUp::getConfig('starting_dir'));
        } else {
            $this->path = $this->splitPath($getdir);
        }
    }

    /**
    * Split a file path into array elements
    *
    * @param string $dir directory to split
    *
    * @return $path2
    */
    public static function splitPath($dir)
    {
        $dir = stripslashes($dir);
        $path1 = preg_split("/[\\\\\/]+/", $dir);
        $path2 = array();

        if (is_dir($dir)) {
            for ($i = 0; $i < count($path1); $i++) {
                if ($path1[$i] == '..' || $path1[$i] == '.' || $path1[$i] == '') {
                    continue;
                }            
                $path2[] = $path1[$i];
            }
        }

        if (count($path2) < 1 && strlen(SetUp::getConfig('starting_dir')) > 2) {
            $path2[] = SetUp::getConfig('starting_dir');
        }
        return $path2;
    }

    /**
    * Get the current directory.
    *
    * @param boolean $prefix  Include the prefix ("./")
    * @param boolean $encoded URL-encode the string
    * @param boolean $html    HTML-encode the string
    * @param int     $upper   return directory n-levels up
    *
    * @return $dir
    */
    public function getDir($prefix, $encoded, $html, $upper)
    {
        $dir = '';
        if ($prefix == true) {
            $dir .= './';
        }
        for ($i = 0; $i < ((count($this->path) >= $upper
            && $upper > 0) ? count($this->path)-$upper : count($this->path)); $i++) {

            $temp = $this->path[$i];

            if ($encoded) {
                $temp = rawurlencode($temp);
            }
            if ($html) {
                $temp = htmlspecialchars($temp);
            }
            $dir .= $temp.'/';
        }
        $dir = EncodeExplorer::extraChars($dir);
        return $dir;
    }

    /**
    * Get directory link for breadcrumbs
    *
    * @param int     $level breadcrumb level
    * @param boolean $html  HTML-encode the name
    *
    * @return path name
    */
    public function getPathLink($level, $html)
    {
        if ($html) {
            return htmlspecialchars($this->path[$level]);
        } else {
            return $this->path[$level];
        }
    }

    /**
    * Get full directory path
    *
    * @return path name
    */
    public function getFullPath()
    {
        $fullpath = (strlen(
            SetUp::getConfig('basedir')
        ) > 0 ? SetUp::getConfig('basedir'):
        str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME'])))
        ."/".$this->getDir(false, false, false, 0);

        $fullpath = EncodeExplorer::extraChars($fullpath);
        return $fullpath;
    }

    /**
    * Checks if the current directory is below the input path
    *
    * @param string $checkPath path to check
    *
    * @return true/false
    */
    public function isSubDir($checkPath)
    {
        for ($i = 0; $i < count($this->path); $i++) {
            if (strcmp($this->getDir(true, false, false, $i), $checkPath) == 0) {
                return true;
            }
        }
        return false;
    }

    /**
    * Check if editing is allowed into the current directory,
    * based on configuration settings
    *
    * @return true/false
    */
    public function editAllowed()
    {
        global $encodeExplorer;
        global $location;

        $totdirs = count($location->path);

        $father = $location->getDir(false, true, false, $totdirs -1);

        if (in_array(basename($father), SetUp::getConfig('hidden_dirs'))) {
            return false;
        }

        if (GateKeeper::getUserInfo('dir') == null
            || $encodeExplorer->checkUserDir($location) == true
        ) {
            return true;
        }
        return false;
    }

    /**
    * Check if current directory is writeable
    *
    * @return true/false
    */
    public function isWritable()
    {
        return is_writable($this->getDir(true, false, false, 0));
    }

    /**
    * Check if target directory is writeable
    *
    * @param string $dir path to check
    *
    * @return true/false
    */
    public static function isDirWritable($dir)
    {
        return is_writable($dir);
    }

    /**
    * Check if target file is writeable
    *
    * @param string $file path to check
    *
    * @return true/false
    */
    public static function isFileWritable($file)
    {
        if (file_exists($file)) {
            if (is_writable($file)) {
                return true;
            } else {
                return false;
            }
        } elseif (Location::isDirWritable(dirname($file))) {
            return true;
        } else {
            return false;
        }
    }
}


/**
 * Main engine based on EncodeExplorer
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class EncodeExplorer
{
    public $location;
    public $dirs;
    public $files;
    public $spaceUsed;
    public $lang;
    /**
    * Calculate space, actions, set language
    *
    * @return set lang session
    */
    public function init()
    {
        if (strlen(SetUp::getConfig('session_name')) > 0) {
            session_name(SetUp::getConfig('session_name'));
        }
        if (count(SetUp::getUsers()) > 0) {
            session_start();
        } else {
            return;
        }

        if (isset($_GET['lang'])
            && file_exists('vfm-admin/translations/'.$_GET['lang'].'.php')
        ) {
            $this->lang = $_GET['lang'];
            $_SESSION['lang'] = $_GET['lang'];
        }
        if (isset($_SESSION['lang'])) {
            $this->lang = $_SESSION['lang'];
        } else {
            $this->lang = SetUp::getConfig('lang');
        }
    }

    /**
    * Print languages list
    *
    * @param string $dir realtive path to translations
    *
    * @return Languages list
    */
    public function printLangMenu($dir = '')
    {
        global $translations_index;

        $directory = 'translations';
        $menu = '<ul class="dropdown-menu">';
        $files = glob($dir.$directory.'/*.php');

        foreach ($files as $item) {
            //$langvar = substr($item, 0, -4);
            $fileinfo = Utils::mbPathinfo($item);
            $langvar = $fileinfo['filename'];
            $menu .= '<li><a href="?lang='.$langvar.'">';
            $out = isset($translations_index[$langvar]) ? $translations_index[$langvar] : $langvar;
            $menu .= '<span>'.$out.'</span></a></li>';
        }
        $menu .= '</ul>';
        return $menu;
    }

    /**
    * Return languages list as array
    *
    * @param string $dir realtive path to translations
    *
    * @return $languages array
    */
    public function getLanguages($dir = '')
    {

        global $translations_index;

        $directory = 'translations';
        $files = glob($dir.$directory.'/*.php');
        $languages = array();

        foreach ($files as $item) {
            // $langvar = substr($item, 0, -4);
            $fileinfo = Utils::mbPathinfo($item);

            $langvar = $fileinfo['filename'];
            $langname = isset($translations_index[$langvar]) ? $translations_index[$langvar] : $langvar;
            $languages[$langvar] = $langname;
            //array_push($languages, $langvar);
        }
        return $languages;
    }

    /**
    * Read the file list from the directory
    *
    * @return Reading the data of files and directories
    */
    public function readDir()
    {
        global $encodeExplorer;
        global $downloader;

        $fullpath = $this->location->getFullPath();
        $totdirs = count($this->location->path);
        $father = $this->location->getDir(false, true, false, $totdirs -1);

        if (in_array(basename($father), SetUp::getConfig('hidden_dirs'))) {
            $encodeExplorer->setErrorString('unable_to_read_dir');
            return false;
        }
        $startingdir = SetUp::getConfig('starting_dir');
        $hidefiles = false;

        if (strlen($startingdir) < 3 && $startingdir === $this->location->getDir(true, true, false, 0)) {
            $hidefiles = true;
        }
        if (is_dir($fullpath)) {
            $files = glob($fullpath.'/*');
            $this->dirs = array();
            $this->files = array();
            if (is_array($files)) {
                foreach ($files as $item) {
                    $mbitem = Utils::mbPathinfo($item);
                    $item_basename = $mbitem['basename'];

                    if (is_dir($item)) {
                        if (!$hidefiles || ($hidefiles && !in_array($item_basename, SetUp::getConfig('hidden_dirs')))) {
                            $this->dirs[] = new Dir($item_basename, $this->location);
                        }
                    } else {
                        if (!$hidefiles || ($hidefiles && !in_array($item_basename, SetUp::getConfig('hidden_files')))) {
                            $this->files[] = new File($item_basename, $this->location);
                        }
                    }
                }
            }
        }
    }

    /**
    * Read the assigned folder list from the root directory
    *
    * @return directiories listing
    */
    public function readFolders()
    {
        global $encodeExplorer;
        $fullpath = $this->location->getFullPath();

        if (is_dir($fullpath)) {

            if ($open_dir = opendir($fullpath)) {
                $this->dirs = array();
                $this->files = array();
                while ($object = readdir($open_dir)) {
                    if ($object != '.' && $object != '..') {
                        if (is_dir($this->location->getDir(true, false, false, 0).'/'.$object)
                            && !in_array($object, SetUp::getConfig('hidden_dirs'))
                            && in_array($object, json_decode(GateKeeper::getUserInfo('dir'), true))
                        ) {
                            $this->dirs[] = new Dir($object, $this->location);
                        }
                    }
                }
                closedir($open_dir);
            } else {
                $encodeExplorer->setErrorString('unable_to_read_dir');
            }
        }
    }

    /**
    * Create links to logout, delete and open directory
    *
    * @param boolean $logout set logout
    * @param string  $delete path to delete
    * @param string  $dir    path to link
    *
    * @return link
    */
    public function makeLink($logout, $delete, $dir)
    {
        $link = '?';

        if ($logout == true) {
            $link .= 'logout';
            return $link;
        }
        $link .= 'dir='.$dir;
        if ($delete != null) {
            $link .= '&amp;del='.base64_encode($delete);
        }
        return $link;
    }

    /**
    * Get string in current language
    *
    * @param string $stringName string to translate
    *
    * @return translated string
    */
    public function getString($stringName)
    {
        return SetUp::getLangString($stringName, $this->lang);
    }

    /**
    * Set success with translated string
    *
    * @param string $stringName translation string
    *
    * @return outputs error message
    */
    public function setSuccessString($stringName)
    {
        Utils::setSuccess($this->getString($stringName));
    }

    /**
    * Set error with translated string
    *
    * @param string $stringName translation string
    *
    * @return outputs error message
    */
    public function setErrorString($stringName)
    {
        Utils::setError($this->getString($stringName));
    }

    /**
    * Check if directory is available for user
    *
    * @param string $location to check
    *
    * @return true/false
    */
    public function checkUserDir($location)
    {
        $this->location = $location;
        $startdir = SetUp::getConfig('starting_dir');
        $thispath = $this->location->getDir(true, false, false, 0);

        if (GateKeeper::getUserInfo('dir') == null) {
            return true;
        }
        if (!is_dir(realpath($thispath))) {
            return false;
        }
        $userpatharray = json_decode(GateKeeper::getUserInfo('dir'), true);
        $userpatharray = $userpatharray ? $userpatharray : array();
        foreach ($userpatharray as $value) {
            $userpath = substr($startdir.$value, 2);
            $pos = strpos($thispath, $userpath);
            if ($pos !== false) {
                return true;
            }
        }
        return false;
    }

    /**
    * Replace some chars from string
    *
    * @param string $str string to clean
    *
    * @return $str
    */
    public static function extraChars($str)
    {
        $apici = array('&#34;', '&#39;');
        $realapici = array('"', '\'');
        $str = str_replace($apici, $realapici, $str);
        return $str;
    }

    /**
    * Main function, check what to see
    *
    * @param string $location current location
    *
    * @return genral output
    */
    public function run($location)
    {
        global $encodeExplorer;

        $this->location = $location;

        if ($encodeExplorer->checkUserDir($location) == true) {
            $this->readDir();
        } else {
            $this->readFolders();
        }
    }
}


/**
 * Utilities
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class Utils
{
    /**
    * Generate random string
    *
    * @param string $length string lenght
    *
    * @return $randomString random string
    */
    public static function randomString($length = 9)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return '$1$'.$randomString;
    }

    /**
    * Check captcha code
    *
    * @param string $postcaptcha code to check
    * @param string $feat        captcha to check
    *
    * @return true / false
    */
    public static function checkCaptcha($postcaptcha, $feat = 'show_captcha')
    {
        if (SetUp::getConfig($feat) !== true) {
            return true;
        }
        if ($postcaptcha) {
            $postcaptcha = strtolower($postcaptcha);

            if (isset($_SESSION['captcha'])
                && $postcaptcha === $_SESSION['captcha']
            ) {
                return true;
            }
        }
        return false;
    }
    
    /**
    * Get pathinfo in UTF-8
    *
    * @param string $filepath to search
    *
    * @return array $ret
    */
    public static function mbPathinfo($filepath)
    {
        preg_match(
            '%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im',
            $filepath,
            $node
        );

        if (isset($node[1])) {
            $ret['dirname'] = $node[1];
        } else {
            $ret['dirname'] = '';
        }

        if (isset($node[2])) {
            $ret['basename'] = $node[2];
        } else {
            $ret['basename'] = '';
        }

        if (isset($node[3])) {
            $ret['filename'] = $node[3];
        } else {
            $ret['filename'] = '';
        }

        if (isset($node[5])) {
            $ret['extension'] = $node[5];
        } else {
            $ret['extension'] = '';
        }
        return $ret;
    }

    /**
    * Check path to delete
    *
    * @param string $path to search
    *
    * @return true/false
    */
    public static function checkDel($path)
    {
        $startdir = SetUp::getConfig('starting_dir');

        $cash = filter_input(INPUT_GET, 'h', FILTER_SANITIZE_STRING);
        $del = filter_input(INPUT_GET, 'del', FILTER_SANITIZE_STRING);

        $del = str_replace(' ', '+', $del);

        if (md5($del.SetUp::getConfig('salt').SetUp::getConfig('session_name')) === $cash) {

            if (GateKeeper::getUserInfo('dir') != null) {
                $userdirs = json_decode(GateKeeper::getUserInfo('dir'), true);

                foreach ($userdirs as $value) {
                    $userpath = $startdir.$value;
                    $pos = strpos('./'.$path, $userpath);

                    if ($pos !== false) {
                        return true;
                    }
                }
                return false;
            }

            $pos = strpos('./'.$path, $startdir);

            $filepathinfo = Utils::mbPathinfo($path);
            $basepath = $filepathinfo['basename'];
            $evil = array('', '/', '\\', '.');
            $avoid = SetUp::getConfig('hidden_files');

            if ($pos === false
                || in_array($path, $evil)
                || in_array($basepath, $avoid)
                || realpath($path) === realpath($startdir)
                || realpath($path) === realpath(dirname(__FILE__))
            ) {
                return false;
            }
            return true;
        }
    
        return false;
    }

    /**
    * The safe way
    *
    * @param string $video file to check
    *
    * @return true/false
    */
    public function checkVideo($video)
    {
        $realsetup = realpath('../.'.SetUp::getConfig('starting_dir'));
        $realfile = realpath($video);
        if (strpos($realfile, $realsetup) !== false && file_exists($video)) {
            return true;
        }
        return false;
    }

    /**
    * Get user data by username
    *
    * @param int $search username to search
    *
    * @return user array requested
    */
    public static function getCurrentUser($search)
    {
        $currentuser = array();
        foreach (SetUp::getUsers() as $user) {
            if ($user['name'] == $search) {
                $currentuser = $user;
                return $currentuser;
            }
        }
        return false;
    }

    /**
    * Remove some chars from string
    *
    * @param string $str string to clean
    *
    * @return $str
    */
    public static function normalizeStr($str)
    {
        $str = strip_tags($str);
        $str = trim($str);
        $str = trim($str, '.');
        $str = stripslashes($str);
        // $str = htmlentities($str);
        $invalid = array(
            '&#34;' => '', '&#39;' => '' ,
            ' ' => '-',
            '{' => '-', '}' => '-',
            '<' => '', '>' => '',
            '`' => '', '´' => '',
            '„' => '', '”' => '', 
            '’' => '', '"' => '',
            '!' => '', '¡' => '',
            '?' => '', '¿' => '',
            '|' => '', '=' => '-', 
            '*' => 'x', ':' => '-',
            ',' => '.', ';' => '',
            'ª' => '', 'º' => '', 
            '~' => '', '&' => '-',  
            '\\' => '', '\'' => '-', '/' => '-',
            '§' => 's', '°' => '', '^' => '', '·' => '',
            '$' => 'usd', '¢' => 'cent', '£' => 'lb', '€' => 'eur',
            '®' => '', '™' => '', '@' => '-at-',
            // '(' => '-', ')' => '-', '.' => '_', ,
        );
        $cleanstring = strtr($str, $invalid);

       $cleanstring = Utils::normalizeName($cleanstring);

        // cut name if has more than 31 chars;
        // if (strlen($cleanstring) > 31) {
        //     $cleanstring = substr($cleanstring, 0, 31);
        // }
        return $cleanstring;
        return $str;
    }

    /**
    * Normalize NFD and NFC chars
    * requires (PHP 5 >= 5.3.0, PHP 7, PECL intl >= 1.0.0)
    *
    * @param string $str string to clean
    *
    * @return $cleanstring
    */
    public static function normalizeName($str)
    {
        $normalized = $str;
        if (function_exists('normalizer_is_normalized')) {
            if (!normalizer_is_normalized($normalized)) {
               $normalized = normalizer_normalize($normalized);
            }
        }
        return $normalized;
    }

    /**
    * Output errors
    *
    * @param string $message error message
    *
    * @return output error
    */
    public static function setError($message)
    {
        global $_ERROR;
        $_ERROR .= ' '.$message;
        $_SESSION['error'] = $_ERROR;
    }

    /**
    * Output success
    *
    * @param string $message success message
    *
    * @return output success
    */
    public static function setSuccess($message)
    {
        global $_SUCCESS;
        $_SUCCESS .= ' '.$message;
        $_SESSION['success'] = $_SUCCESS;
    }


    /**
    * Output warning
    *
    * @param string $message warning message
    *
    * @return output warning
    */
    public static function setWarning($message)
    {
        global $_WARNING;
        $_WARNING .= ' '.$message;
        $_SESSION['warning'] = $_WARNING;
    }

    /**
    * Check Magic quotes
    *
    * @param string $name string to check
    *
    * @return $name
    */
    public static function checkMagicQuotes($name)
    {
        if (get_magic_quotes_gpc()) {
            $name = stripslashes($name);
        } else {
            $name = $name;
        }
        return $name;
    }

    /**
    * Check file_info
    *
    * @return true/false
    */
    public static function checkFinfo()
    {
        if (function_exists('finfo_open')
            && function_exists('finfo_file')
        ) {
            return true;
        }
        return false;
    }

    /**
    * Check if item is in list
    *
    * @param string $item item to check
    * @param string $list list where to look
    *
    * @return true/false
    */
    public static function inList($item, $list)
    {
        if (is_array($list)
            && count($list) > 0
            && in_array($item, $list)
        ) {
            return true;
        }
        return false;
    }

    /**
    * Check if item is not in list
    *
    * @param string $item item to check
    * @param string $list list where to look
    *
    * @return true/false
    */
    public static function notList($item, $list)
    {
        if (is_array($list)
            && count($list) > 0
            && !in_array($item, $list)
        ) {
            return true;
        }
        return false;
    }

    /**
    * Check if target directory is writeable
    *
    * @param string $dir path to check
    *
    * @return array with files and folders count
    */
    public static function countContents($dir)
    {
        $aprila = glob($dir.'/*');
        $quanti = count($aprila);
        if ($aprila) {
            $quantifiles = count(array_filter($aprila, 'is_file'));
            $quantedir = count(array_filter($aprila, 'is_dir'));
        } else {
            $quantifiles = 0;
            $quantedir = 0;
        }
        $result = array(
            'files' => $quantifiles,
            'folders' => $quantedir
        );
        return $result;
    }
}


/**
 * SetUp main configuration
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class SetUp
{
    /**
    * Get the url of the application
    *
    * @return url of the app
    */
    public static function getAppUrl()
    {
        /**
        * Check if http or https
        */
        if (!empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off'
            || $_SERVER['SERVER_PORT'] == 443
        ) {
            $http = 'https://';
        } else {
            $http = 'http://';
        }
        /**
        * Setup the application url
        */
        $actual_link = $http.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']);
        $chunks = explode('vfm-admin', $actual_link);
        $cleanqs = $chunks[0];
        return $cleanqs;
    }

    /**
    * Return folders available inside given directory
    *
    * @param string $dir realtive path
    *
    * @return $folders array
    */
    public function getFolders($dir = '')
    {
        $directory = '.'.SetUp::getConfig('starting_dir');
        $files = array_diff(
            scandir($dir.$directory),
            array('.', '..')
        );
        $files = preg_grep('/^([^.])/', $files);

        $folders = array();

        foreach ($files as $item) {
            if (is_dir($directory . '/' . $item)) {
                array_push($folders, $item);
            }
        }
        return $folders;
    }

    /**
    * The function for getting a translated string.
    * Falls back to english if the correct language is missing something.
    *
    * @param string $stringName string to translate
    *
    * @return translation
    */
    public static function getLangString($stringName)
    {
        global $_TRANSLATIONS;
        if (isset($_TRANSLATIONS)
            && is_array($_TRANSLATIONS)
            && isset($_TRANSLATIONS[$stringName])
            && strlen($_TRANSLATIONS[$stringName]) > 0
        ) {
            return stripslashes($_TRANSLATIONS[$stringName]);
        } else {
            return '&gt;'.$stringName.'&lt;';
        }
    }

    /**
    * Show language menu
    *
    * @return true/false
    */
    public static function showLangMenu()
    {
        if (SetUp::getConfig('show_langmenu') == true) {
            return true;
        }
        return false;
    }

    /**
    * The function for getting configuration values
    *
    * @param string $name    config option name
    * @param string $default optional default value
    *
    * @return config value
    */
    public static function getConfig($name, $default = false)
    {
        global $_CONFIG;
        if (isset($_CONFIG) && isset($_CONFIG[$name])) {
            return $_CONFIG[$name];
        }
        if ($default !== false) {
            return $default;
        }
        return false;
    }

    /**
    * Print alert messages
    *
    * @return the alert
    */
    public static function printAlert()
    {
        global $_ERROR;
        global $_WARNING;
        global $_SUCCESS;

        $alert = false;
        $output = '';
        $sticky_class = '';

        if (SetUp::getConfig('sticky_alerts') === true) {
            $sticky_class = 'sticky-alert '.SetUp::getConfig('sticky_alerts_pos');
        }

        $closebutt = '<button type="button" class="close" aria-label="Close">'
            .'<span aria-hidden="true">&times;</span></button>';

        if (isset($_ERROR) && strlen($_ERROR) > 0) {
            $alert = true;
            $output .= '<div class="response nope alert" role="alert">'
            .$_ERROR.$closebutt.'</div>';
        }
        if (isset($_WARNING) && strlen($_WARNING) > 0) {
            $alert = true;
            $output .= '<div class="response boh alert" role="alert">'
            .$_WARNING.$closebutt.'</div>';
        }
        if (isset($_SUCCESS) && strlen($_SUCCESS) > 0) {
            $alert = true;
            $output .= '<div class="response yep alert" role="alert">'
            .$_SUCCESS.$closebutt.'</div>';
        }
        if ($alert === true) {
            $output = '<div class="alert-wrap '.$sticky_class.'">'.$output.'</div>';
            return $output;
        }
        return false;
    }


    /**
    * Get app description
    *
    * @return html decoded description or false
    */
    public function getDescription()
    {
        $fulldesc = html_entity_decode(Setup::getConfig('description'), ENT_QUOTES, 'UTF-8');
        $cleandesc = strip_tags($fulldesc, '<img>');

        if (strlen(trim($cleandesc)) > 0) {
            return $fulldesc;
        }
        return false;
    }

    /**
    * Switch language
    *
    * @param string $lang language to link
    *
    * @return language link
    */
    public function switchLang($lang)
    {
        $link = '?lang='.$lang;
        return $link;
    }

    /**
    * Format modification date time
    *
    * @param string $time new format
    *
    * @return formatted date
    */
    public static function formatModTime($time)
    {
        $timeformat = 'd.m.y H:i:s';
        if (SetUp::getConfig('time_format') != null
            && strlen(SetUp::getConfig('time_format')) > 0
        ) {
            $timeformat = SetUp::getConfig('time_format');
        }
        return date($timeformat, $time);
    }

    /**
    * Format file size
    *
    * @param string $size new format
    *
    * @return formatted size
    */
    public function formatSize($size)
    {
        $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
        $syz = $sizes[0];
        for ($i = 1; (($i < count($sizes)) && ($size >= 1024)); $i++) {
            $size = $size / 1024;
            $syz  = $sizes[$i];
        }
        return round($size, 2).' '.$syz;
    }

    /**
    * Fet file size in kb
    *
    * @param string $size new format
    *
    * @return formatted size
    */
    public function fullSize($size)
    {
        $size = $size / 1024;
        return round($size);
    }

    /**
    * Get all users from users.php
    *
    * @return users array
    */
    public static function getUsers()
    {
        global $_USERS;
        if (isset($_USERS)) {
            return $_USERS;
        }
        return null;
    }
}


/**
 * Manage download files
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class Downloader
{
    /**
    * Checks if file is under user folder
    *
    * @param string $checkPath path to check
    *
    * @return true/false
    */
    public function subDir($checkPath)
    {
        global $gateKeeper;

        if ($gateKeeper->getUserInfo('dir') == null) {
            return true;
        } else {
            $userdirs = json_decode($gateKeeper->getUserInfo('dir'), true);
            foreach ($userdirs as $value) {
                $pos = strpos($checkPath, $value);
                if ($pos !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
    * The safe way
    *
    * @param string $checkfile file to check
    *
    * @return true/false
    */
    public function checkFile($checkfile)
    {
        global $setUp;

        $fileclean = base64_decode($checkfile);
        $file = '../'.urldecode($fileclean);

        $filepathinfo = Utils::mbPathinfo($fileclean);

        $filename = $filepathinfo['basename'];
        $safedir = $filepathinfo['dirname'];

        $safedir = str_replace(array('/', '.'), '', $safedir);
        $realfile = realpath($file);
        $realsetup = realpath('.'.$setUp->getConfig('starting_dir'));

        $avoidDir = array('vfm-admin', 'etc');
        $avoidFile = array('index.php', 'vfm-thumb.php', '.htaccess', '.htpasswd');

        if (strpos($realfile, $realsetup) !== false
            && !in_array($safedir, $avoidDir) 
            && !in_array($filename, $avoidFile)
            && file_exists($file)
        ) {
            return true;
        }
        return false;
    }

    /**
    * Check download lifetime
    *
    * @param string $time time to check
    *
    * @return true/false
    */
    public function checkTime($time)
    {
        global $setUp;

        $lifedays = (int)$setUp->getConfig('lifetime');
        $lifetime = 86400 * $lifedays;
        if (time() <= $time + $lifetime) {
            return true;
        }
        return false;
    }

    /**
    * Get file info before processing download
    *
    * @param string $getfile file to download
    * @param string $playmp3 check audio
    *
    * @return $headers array
    */
    public function getHeaders($getfile, $playmp3 = false)
    {
        global $utils;

        $headers = array();

        $audiofiles = array('mp3','wav');
        $trackfile = './'.urldecode(base64_decode($getfile));
        $file = '.'.$trackfile;

        $filepathinfo = $utils->mbPathinfo($file);
        $filename = $filepathinfo['basename'];
        $dirname = $filepathinfo['dirname'].'/';
        $ext = $filepathinfo['extension'];
        $file_size = File::getFileSize($file);
        $disposition = 'inline';

        if ($ext == 'pdf' || $ext == 'PDF') {
            $content_type = 'application/pdf';
        } elseif ($ext == 'zip' || $ext == 'ZIP') {
            $content_type = 'application/zip';
            $disposition = 'attachment';
        } elseif (in_array(strtolower($ext), $audiofiles)
            && $playmp3 == 'play'
        ) {
            $content_type = 'audio/mp3';
        } else {
            $content_type = 'application/force-download';
        }
        $headers['file'] = $file;
        $headers['filename'] = $filename;
        $headers['file_size'] = $file_size;
        $headers['content_type'] = $content_type;
        $headers['disposition'] = $disposition;
        $headers['trackfile'] = $trackfile;
        $headers['dirname'] = $dirname;

        return $headers;
    }

    /**
    * Download files
    *
    * @param string $file         path to download
    * @param string $filename     file name
    * @param string $file_size    file size
    * @param string $content_type header content type
    * @param string $disposition  header disposition
    * @param bool   $android      android device
    *
    * @return file served
    */
    public function download(
        $file,
        $filename,
        $file_size,
        $content_type,
        $disposition = 'inline',
        $android = false
    ) {
        @set_time_limit(0);
        session_write_close();
        header("Content-Length: ".$file_size);

        if ($android) {
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"".$filename."\"");
        } else {
            header("Content-Type: $content_type");
            header("Content-Disposition: $disposition; filename=\"".$filename."\"");
            header("Content-Transfer-Encoding: binary");
            header("Expires: -1");
        }
        if (ob_get_level()) {
            ob_end_clean();
        }
        readfile($file);
        return true;
    }

    /**
    * Create ZIP archive
    *
    * @param array  $files  files array to download
    * @param string $folder folder path or false
    * @param bool   $ajax   if the function is called from /ajax/
    *
    * @return file served
    */
    public function createZip(
        $files = false,
        $folder = false,
        $ajax = false
    ) {
        $response = array('error' => false);
        $stepback = $ajax ? '../' : '';

        global $setUp;
        global $encodeExplorer;

        @set_time_limit(0);

        $script_url = $setUp->getConfig('script_url');
        $maxfiles = $setUp->getConfig('max_zip_files');
        $maxfilesize = $setUp->getConfig('max_zip_filesize');
        $maxbytes = $maxfilesize*1024*1024;

        if ($files && is_array($files)) {
            $totalsize = 0;
            $filesarray = array();
            foreach ($files as $pezzo) {
                $myfile = "../".urldecode(base64_decode($pezzo));
                $totalsize = $totalsize + File::getFileSize($myfile);
                array_push($filesarray, $myfile);
            }
            $howmany = count($filesarray);
        }
        if ($folder) {

            $folderpathinfo = Utils::mbPathinfo($folder);
            $folderbasename = Utils::normalizeStr(Utils::checkMagicQuotes($folderpathinfo['filename']));

            $folderpath = $stepback.$folder;
            if (!is_dir($folderpath)) {
                $response['error'] = '<strong>'.$folder.'</strong> does not exist';
                return $response;
            }

            // Create recursive directory iterator
            $filesarray = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($folderpath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            $foldersize = FileManager::getDirSize($folderpath);
            $totalsize = $foldersize['size'];
            $howmany = 0;
            foreach ($filesarray as $piece) {
                if (!is_dir($piece)) {
                    $howmany++;
                }
            }
        }

        $response['totalsize'] = $totalsize;
        $response['numfiles'] = $howmany;

        // skip if size or number exceedes
        if ($totalsize > $maxbytes) {
            $response['error'] = '<strong>'.$setUp->formatsize($totalsize).'</strong>: '
            .$encodeExplorer->getString('size_exceeded').'<br>(&lt; '.$setUp->formatsize($maxbytes).')';
            return $response;
        }
        if ($howmany > $maxfiles) {
            $response['error'] = '<strong>'.number_format($howmany).'</strong>: '
            .$encodeExplorer->getString('too_many_files').' '.number_format($maxfiles);
            return $response;
        }

        if ($howmany < 1) {
            $response['error'] = '<i class="fa fa-files-o"></i> - <strong>0</strong>';
            return $response;
        }
        // create /tmp/ folder if needed
        if (!is_dir($stepback.'tmp')) {
            if (!mkdir($stepback.'tmp', 0755)) {
                $response['error'] = 'Cannot create a tmp dir for .zip files';
                return $response;
            }
        }
        // delete tmp file if is older than 2 hours 
        $oldtmp = glob($stepback.'tmp/*');
        foreach ($oldtmp as $oldfile) {
            if (filemtime($oldfile) < time() - 60*60*2) {
                unlink($oldfile);
            }
        }

        // create temp zip
        $file = tempnam($stepback.'tmp', 'zip');

        if (!$file) {
            $response['error'] = 'Cannot create: tempnam("tmp","zip") from createZip()';
            return $response;       
        }

        $zip = new ZipArchive();

        if ($zip->open($file, ZipArchive::OVERWRITE) !== true) {
            $response['error'] = 'cannot open: '.$file;
            return $response;
        }

        session_write_close();

        $counter = 0;
        $logarray = array();

        foreach ($filesarray as $piece) {

            $filepathinfo = Utils::mbPathinfo($piece);
            $basename = Utils::normalizeStr(Utils::checkMagicQuotes($filepathinfo['basename']));

            // Skip directories (they would be added automatically)
            if (!is_dir($piece)) {
                $counter++;
                if ($counter > 100) {
                    $zip->close();
                    $zip->open($file, ZipArchive::CHECKCONS);
                    $counter = 0;
                }
                // Add current file to archive
                if ($folder) {
                    $folderpath = substr($stepback.$folder, 0, - strlen($folderbasename));
                    $relativePath = substr($piece, strlen($folderpath));
                    $zip->addFile($piece, $relativePath);
                } else {
                    $zip->addFile($piece, $basename);
                    array_push($logarray, "./".$basename);
                }
            }
        }
        $zip->close();

        $response['dir'] = $stepback.$folder;

        $response['file'] = $file;

        if ($folder) {
            array_push($logarray, $folder);
        }
        $response['logarray'] = $logarray;
        return $response;
    }
}

/**
 * Class to control password reset
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class Resetter
{
    /**
    * Call update user functions
    *
    * @return $message
    */
    public static function init()
    {
        global $updater;
        global $resetter;
        global $_USERS;
        global $users;
        $users = $_USERS;

        $resetpwd = filter_input(INPUT_POST, 'reset_pwd', FILTER_SANITIZE_STRING);
        $resetconf = filter_input(INPUT_POST, 'reset_conf', FILTER_SANITIZE_STRING);
        $userh = filter_input(INPUT_POST, 'userh', FILTER_SANITIZE_STRING);
        $getrp = filter_input(INPUT_POST, 'getrp', FILTER_SANITIZE_STRING);

        if ($resetpwd && $resetconf
            && ($resetpwd == $resetconf)
            && $userh
            && $resetter->checkTok($getrp, $userh) == true
        ) {
            $username = $resetter->getUserFromSha($userh);
            $updater->updateUserPwd($username, $resetpwd);
            $updater->updateUserFile('password');
            $resetter->resetToken($resetter->getMailFromSha($userh));
        }
    }

    /**
    * Get user name from encrypted email
    *
    * @param string $usermailsha user email in SHA1
    *
    * @return username
    */
    public function getUserFromSha($usermailsha)
    {
        global $_USERS;
        $utenti = $_USERS;

        foreach ($utenti as $value) {
            if (isset($value['email']) && sha1($value['email']) === $usermailsha) {
                return $value['name'];
            }
        }
    }

    /**
    * Get user mail from encrypted email
    *
    * @param string $usermailsha user email in SHA1
    *
    * @return username
    */
    public function getMailFromSha($usermailsha)
    {
        global $_USERS;
        $utenti = $_USERS;

        foreach ($utenti as $value) {
            if (isset($value['email']) && sha1($value['email']) === $usermailsha) {
                return $value['email'];
            }
        }
    }

    /**
    * Get user name from email
    *
    * @param string $usermail user email
    *
    * @return username
    */
    public function getUserFromMail($usermail)
    {
        global $_USERS;
        $utenti = $_USERS;

        foreach ($utenti as $value) {
            if (isset($value['email'])) {
                if ($value['email'] === $usermail) {
                    return $value['name'];
                }
            }
        }
    }

    /**
    * Reset token
    *
    * @param string $usermail user email
    *
    * @return mail to user
    */
    public function resetToken($usermail)
    {
        global $_TOKENS;
        global $tokens;
        $tokens = $_TOKENS;
        unset($tokens[$usermail]);

        $tkns = '$_TOKENS = ';

        if (false == (file_put_contents(
            'vfm-admin/users/token.php',
            "<?php\n\n $tkns".var_export($tokens, true).";\n"
        ))
        ) {
            Utils::setError('error, no token reset');
            return false;
        }
    }

    /**
    * Set token for password recovering
    *
    * @param string $usermail user email
    * @param string $path     path to token.php
    *
    * @return mail to user
    */
    public function setToken($usermail, $path = '')
    {
        global $resetter;
        global $_TOKENS;
        global $tokens;
        $tokens = $_TOKENS;

        $birth = time();
        $salt = SetUp::getConfig('salt');
        $token = sha1($salt.$usermail.$birth);

        $tokens[$usermail]['token'] = $token;
        $tokens[$usermail]['birth'] = $birth;
        $tkns = '$_TOKENS = ';

        if (false == (file_put_contents(
            $path.'token.php',
            "<?php\n\n $tkns".var_export($tokens, true).";\n"
        ))
        ) {
            return false;
        } else {
            $message = array();
            $message['name'] = $resetter->getUserFromMail($usermail);
            $message['tok'] = '?rp='.$token.'&usr='.sha1($usermail);
            return $message;
        }
        return false;
    }

    /**
    * Check token validity and lifetime
    *
    * @param string $getrp  time to check
    * @param string $getusr getusr to check
    *
    * @return true/false
    */
    public function checkTok($getrp, $getusr)
    {
        global $_TOKENS;
        global $tokens;
        $tokens = $_TOKENS;
        $now = time();

        foreach ($tokens as $key => $value) {
            if (sha1($key) === $getusr) {
                if ($value['token'] === $getrp) {
                    if ($now < $value['birth'] + 3600) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}

/**
 * The utilities for the chunk upload
 * managed by chunk.php
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class Chunk
{
    /**
    * Set response message
    *
    * @param string $message error message
    *
    * @return update session error
    */
    public function setError($message)
    {
        if (isset($_SESSION['error']) && $_SESSION['error'] !== $message) {
            $_SESSION['error'] .= $message;
        } else {
            $_SESSION['error'] = $message;
        }
    }

    /**
    * Set response message
    *
    * @param string $message warning message
    *
    * @return update session warning
    */
    public function setWarning($message)
    {
        if (isset($_SESSION['warning']) && $_SESSION['warning'] !== $message) {
            $_SESSION['warning'] .= $message;
        } else {
            $_SESSION['warning'] = $message;
        }
    }

    /**
    * Set response message
    *
    * @param string $message success message
    *
    * @return update session success
    */
    public function setSuccess($message)
    {
        if (isset($_SESSION['success']) && $_SESSION['success'] !== $message) {
            $_SESSION['success'] .= $message;
        } else {
            $_SESSION['success'] = $message;
        }
    }

    /**
    * Check if user has space to upload
    *
    * @param string $thissize size to check
    *
    * @return true/false
    */
    public function checkUserUp($thissize)
    {
        if (isset($_SESSION['vfm_user_used']) 
            && isset($_SESSION['vfm_user_space'])
        ) {
            $oldused = $_SESSION['vfm_user_used'];
            $newused = $oldused + $thissize;
            $freespace = $_SESSION['vfm_user_space'];
            
            if ($newused > $freespace) {
                return false;
            } else {
                return true;
            }
        }
        return true;
    }

    /**
    * Check if user has space to upload
    *
    * @param string $thissize size to check
    *
    * @return updated user space
    */
    public function setUserUp($thissize)
    {
        if (isset($_SESSION['vfm_user_used'])) {
            $oldused = $_SESSION['vfm_user_used'];
            $newused = $oldused + $thissize;
            $_SESSION['vfm_user_used'] = $newused;
        }
    }

    /**
    * Setup filename to upload
    *
    * @param string $resumableFilename filename to convert
    * @param string $rid               file ID
    *
    * @return resumableFilename updated
    */
    public function setupFilename($resumableFilename, $rid)
    {
        $extension = File::getFileExtension($resumableFilename);
        $filepathinfo = Utils::mbPathinfo($resumableFilename);
        $basename = Utils::normalizeStr(Utils::checkMagicQuotes($filepathinfo['filename']));

    //  change $resumableFilename to prepend date-time before file name
        $resumableFilename = $basename.'.'.$extension;
        // $resumableFilename = $basename.'_'.date('Y-m-d_G-i-s').'.'.$extension;

        array_push($_SESSION['upcoda'], $rid);
        array_push($_SESSION['uplist'], $resumableFilename);

        $upcoda = array_unique($_SESSION['upcoda']);
        $uplist = array_unique($_SESSION['uplist']);

        if (count($upcoda) > count($uplist)) {
            $count = count($upcoda);
            $basename = $basename.$count;
            $resumableFilename = $basename.'.'.$extension;
        }

        $_SESSION['upcoda'] = $upcoda;
        $_SESSION['uplist'] = $uplist;

        $resumabledata = array();

        $resumabledata['extension'] = $extension;
        $resumabledata['basename'] = $basename;
        $resumabledata['filename'] = $resumableFilename;

        return $resumabledata;
    }

    /**
     * Check if all the parts exist, and
     * gather all the parts of the file together
     *
     * @param string $location  - the final location
     * @param string $temp_dir  - the temporary directory holding all the parts of the file
     * @param string $fileName  - the original file name
     * @param string $chunkSize - each chunk size (in bytes)
     * @param string $totalSize - original file size (in bytes)
     * @param string $logloc    - relative location for log file
     *
     * @return uploaded file
     */
    public function createFileFromChunks($location, $temp_dir, $fileName, $chunkSize, $totalSize, $logloc)
    {
        global $chunk;
        $upload_dir = str_replace('\\', '', $location);
        $extension = File::getFileExtension($fileName);

        // count all the parts of this file
        $total_files = 0;
        $finalfile = FileManager::safeExtension($fileName, $extension);

        foreach (scandir($temp_dir) as $file) {
            if (stripos($file, $fileName) !== false) {
                $total_files++;
            }
        }

        // check that all the parts are present
        // the size of the last part is between chunkSize and 2*$chunkSize
        if ($total_files * $chunkSize >= ($totalSize - $chunkSize + 1)) {


            // create the final file
            if (is_dir($upload_dir)
                && ($openfile = fopen($upload_dir.$finalfile, 'w')) !== false
            ) {
                for ($i=1; $i<=$total_files; $i++) {
                    fwrite($openfile, file_get_contents($temp_dir.'/'.$fileName.'.part'.$i));
                }
                fclose($openfile);

                // rename the temporary directory (to avoid access from other
                // concurrent chunks uploads) and than delete it
                if (rename($temp_dir, $temp_dir.'_UNUSED')) {
                    Actions::deleteDir($temp_dir.'_UNUSED');
                } else {
                    Actions::deleteDir($temp_dir);
                }
                $chunk->setSuccess(' <span><i class="fa fa-check-circle"></i> '.$finalfile.' </span> ', 'yep');
                $chunk->setUserUp($totalSize);

                $message = array(
                    'user' => GateKeeper::getUserInfo('name'),
                    'action' => 'ADD',
                    'type' => 'file',
                    'item' => $logloc.$finalfile
                );
                Logger::log($message, '');
                if (SetUp::getConfig('notify_upload')) {
                    Logger::emailNotification($logloc.$finalfile, 'upload');
                }

            } else {
                $chunk->setError(' <span><i class="fa fa-exclamation-triangle"></i> cannot create the destination file', 'nope');
                return false;
            }
        }
    }
}

/**
 * The class controls the templating system
 *
 * @category PHP
 * @package  VenoFileManager
 * @author   Nicola Franchini <info@veno.it>
 * @license  Exclusively sold on CodeCanyon: http://bit.ly/veno-file-manager
 * @version  Release: 2.6.3
 * @link     http://filemanager.veno.it/
*/
class Template
{
    /**
    * Check if all the parts exist
    *
    * @param string $file     - the template part to search
    * @param string $relative - the relative path
    *
    * @return include file
    */
    public function getPart($file, $relative = 'vfm-admin/')
    {
        global
        $_CONFIG,
        $_DLIST,
        $_IMAGES,
        $_USERS,
        $_ERROR,
        $_SUCCESS,
        $_WARNING,
        $actual_link,
        $downloader,
        $encodeExplorer,
        $gateKeeper,
        $getcloud,
        $getrp,
        $getusr,
        $hash,
        $location,
        $logoclass,
        $newusers,
        $regactive,
        $resetter,
        $setUp,
        $time,
        $updater,
        $hasvideo,
        $hasimage,
        $hasaudio,
        $imageServer;
        
        if (file_exists($relative.'template/'.$file.'.php')) {
            $thefile = $relative.'template/'.$file.'.php';
        } else {
            $thefile =  $relative.'include/'.$file.'.php';
        }
        include $thefile;
    }
}
