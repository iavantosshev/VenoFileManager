<?php
/**
 * VFM - veno file manager: include/header.php
 * site header: top banner, description
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
$parent = basename($_SERVER["SCRIPT_FILENAME"]);
$islogin = ($parent === "login.php" ? true : false);
$logoAlignment = $setUp->getConfig("align_logo");
$fulldesc = $setUp->getDescription();

if ($setUp->getConfig("show_head") !== true && $fulldesc == false) {
    return;
} ?>

            <header class="vfm-header">
                <div class="container">
            <?php
            /**
            * ************************************************
            * ******************* Top Banner *****************
            * ************************************************
            */
            if ($setUp->getConfig("show_head") == true ) { 

                if ($islogin == true) { 
                    $logopath = "images/";
                } else {
                    $logopath = "vfm-admin/images/";
                }
                ?>
                <div class="head-banner text-<?php echo $logoAlignment; ?>">
                    <a href="<?php echo $setUp->getConfig("script_url"); ?>">
                        <img alt="<?php print $setUp->getConfig('appname'); ?>" src="<?php print $logopath.$setUp->getConfig('logo'); ?>">
                    </a>
                </div>
            <?php
            } 
            /**
            * ************************************************
            * ****************** Description *****************
            * ************************************************
            */            

            if ($gateKeeper->isAccessAllowed() 
                && !$getcloud 
                && $islogin == false
                && $fulldesc
            ) { ?>
                <div class="description lead"><?php echo $fulldesc; ?></div> 
            <?php
            } ?>
                </div> <!-- .container -->
            </header>