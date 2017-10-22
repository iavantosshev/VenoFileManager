<?php
/**
 * VFM - veno file manager: include/downloader.php
 * Show download buttons for shared links
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
* Downloader
*/
$expired = false;
global $getfilelist;
if ($getfilelist && file_exists('vfm-admin/shorten/'.$getfilelist.'.json')) {

    $datarray = json_decode(file_get_contents('vfm-admin/shorten/'.$getfilelist.'.json'), true);
    $passa = true;
    $passcap = true;
    $passpass = true;
    
    if ($setUp->getConfig('enable_prettylinks') == true) {
        $zip_url = "download/dl/".$getfilelist;
    } else {
        $zip_url = "vfm-admin/vfm-downloader.php?dl=".$getfilelist;
    }
    
    $pass = (isset($datarray['pass']) ? $datarray['pass'] : false);

    if ($pass) { 
        $passa = false;
        $passpass = false;

        $postpass = filter_input(INPUT_POST, "dwnldpwd", FILTER_SANITIZE_STRING);
        if (md5($postpass) === $pass) {
            $passa = true;
            $passpass = true;
            $zip_url .= "&pw=".urlencode($postpass);
        }
    }

    $postcaptcha = filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_STRING);
    if (Utils::checkCaptcha($postcaptcha, 'show_captcha_download') !== true) {
        $passa = false;
        $passcap = false;
    }


    $hash = $datarray['hash'];
    $time = $datarray['time'];
        
    if ($passa === true) {

        $direct_links = $setUp->getConfig('direct_links');
        $target = '';

        $countfiles = 0;
        
        if ($downloader->checkTime($time) == true) { ?>
            <div class="intero centertext bigzip">
                <a class="btn btn-primary btn-lg centertext" href="<?php echo $zip_url; ?>">
                    <i class="fa fa-cloud-download fa-5x"></i><br>
                    .zip
                </a>
            </div>
            
            <div class="intero">
                <ul class="multilink">
            <?php

            $pieces = explode(",", $datarray['attachments']);

            $totalsize = 0;
            $salt = $setUp->getConfig('salt');

            foreach ($pieces as $pezzo) {

                $myfile = urldecode(base64_decode($pezzo));

                if (file_exists($myfile)) {

                    $countfiles++;

                    $filepathinfo = Utils::mbPathinfo($myfile);
                    $filename = $filepathinfo['basename'];
                    $extension = strtolower($filepathinfo['extension']);
                    $supah = md5($hash.$salt.$pezzo);
                    $filesize = File::getFileSize($myfile);
                    $totalsize += $filesize;
                    if (array_key_exists($extension, $_IMAGES)) {
                        $thisicon = $_IMAGES[$extension];
                    } else {
                        $thisicon = "fa-file-o";
                    }
                    /**
                    * Set pretty links
                    */                    
                    if ($setUp->getConfig('enable_prettylinks') == true) {
                        $downlink = "download/".$pezzo."/h/".$hash."/sh/".$supah;
                    } else {
                        $downlink = "vfm-admin/vfm-downloader.php?q=".$pezzo."&h=".$hash."&sh=".$supah;
                    } 
                    /**
                    * Set link to direct file
                    */
                    if ($direct_links === true) {
                        // $downlink = $myfile;
                        $target = 'target="_blank"';
                    }

                    ?>
                    <li>
                        <a class="btn btn-primary" href="<?php echo $downlink; ?>" <?php echo $target; ?>>
                            <span class="pull-left small">
                                <i class="fa <?php echo $thisicon; ?> fa-2x"></i> <?php echo $filename; ?>
                            </span>
                            <span class="pull-right small">
                                <?php echo $setUp->formatsize($filesize); ?> <i class="fa fa-download fa-2x"></i>
                            </span>
                        </a>
                    </li>
                    <?php
                }
            }
            print "</ul></div>";

            // if more than 1 file file exists create a zip
            if (strlen($countfiles > 1)) {
                // check number of files and total size 
                // if under the limits, show the zip button
                $max_zip_filesize = $setUp->getConfig('max_zip_filesize');
                $max_zip_files = $setUp->getConfig('max_zip_files');
                $totalsize = $totalsize/1024/1024;
                $totalfiles = $countfiles;

                if ($totalsize <= $max_zip_filesize && $totalfiles <= $max_zip_files) { ?>
                    <script type="text/javascript">
                    $(document).ready(function(){
                        $('.bigzip').fadeIn();
                    });
                    </script>
                    <?php
                }
            }
        } 
        // download link time expired
        // or no more file available
        if (strlen($countfiles < 1) || $downloader->checkTime($time) == false) {
            unlink('vfm-admin/shorten/'.$getfilelist.'.json');
            $expired = true;
        }
        
    } // END if $passa == true

    if ($passa !== true) { ?>

        <div class="row" id="dwnldpwd">
            <div class="col-sm-4">
            </div>
            <div class="col-sm-4">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);?>">
                <?php
                if (strlen($pass) > 0) {
        
                    if ($postpass && $passpass !== true) { ?>
                        <script type="text/javascript">
                            var $error2 = $('<div class="alert-wrap"><div class="response nope alert-dismissible alert" role="alert">'
                            + ' <?php echo $encodeExplorer->getString("wrong_captcha"); ?>'
                            + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
                            + '<span aria-hidden="true">&times;</span></button></div></div>');
                            $('#error').append($error2);
                        </script>
                    <?php
                    } ?>
                    <div class="form-group">
                        <label for="dwnldpwd"><?php echo $encodeExplorer->getString("password"); ?></label>
                        <input type="password" name="dwnldpwd" class="form-control" placeholder="<?php echo $encodeExplorer->getString("password"); ?>">
                    </div>
                <?php
                }
                /* ************************ CAPTCHA ************************* */
                if ($setUp->getConfig("show_captcha_download") == true ) { 
                    $capath = "vfm-admin/";
                    include "vfm-admin/include/captcha.php"; 
                    
                    if ($postcaptcha && $passcap !== true) { ?>
                        <script type="text/javascript">
                            var $error = $('<div class="alert-wrap"><div class="response nope alert-dismissible alert" role="alert">'
                            + ' <?php echo $encodeExplorer->getString("wrong_captcha"); ?>'
                            + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
                            + '<span aria-hidden="true">&times;</span></button></div></div>');
                            $('#error').append($error);
                        </script>  
                    <?php
                    }
                } ?>
                    <div class="form-group">
                      <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-check"></i>
                      </button>
                    </div>
                </form>
            </div>
            <div class="col-sm-4">
            </div>
        </div>
    <?php
    } ?>
<?php

} else {
    $expired = true;
}

if ($expired === true) { ?>
    <div class="intero centertext">
        <a class="btn btn-default btn-lg centertext whitewrap" href="./">
            <?php echo $encodeExplorer->getString("link_expired"); ?>
        </a>
    </div>
<?php
}
