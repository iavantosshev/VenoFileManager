<?php
/**
 * VFM - veno file manager: include/login.php
 * front-end login panel
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
* Login Area
*/
if (!$gateKeeper->isAccessAllowed()) { ?>
    <section class="vfmblock">
        <div class="login">

            <div class="panel panel-default">
                <div class="panel-body">
                    <form enctype="multipart/form-data" method="post" role="form" 
                    action="<?php echo $encodeExplorer->makeLink(false, null, ""); ?>">
                        <div id="login_bar" class="form-group">
                            <div class="form-group">
                                <label class="sr-only" for="user_name">
                                    <?php echo $encodeExplorer->getString("username"); ?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                                    <input type="text" name="user_name" value="" id="user_name" class="form-control" 
                                    placeholder="<?php echo $encodeExplorer->getString("username"); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="user_pass">
                                    <?php echo $encodeExplorer->getString("password"); ?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock fa-fw"></i></span>
                                    <input type="password" name="user_pass" id="user_pass" class="form-control" 
                                    placeholder="<?php echo $encodeExplorer->getString("password"); ?>" />
                                </div>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="vfm_remember" value="yes"> 
                                    <?php echo $encodeExplorer->getString("remember_me"); ?>
                                </label>
                            </div>
                            <?php 
                            /* ************************ CAPTCHA ************************* */
                            if ($setUp->getConfig("show_captcha") == true ) { 
                                $capath = "vfm-admin/";
                                include "vfm-admin/include/captcha.php"; 
                            }   ?>
                            <button type="submit" class="btn btn-primary btn-block" />
                                <i class="fa fa-sign-in"></i> 
                                <?php echo $encodeExplorer->getString("log_in"); ?>
                            </button>
                        </div>
                    </form>
                    <p><a href="?rp=req"><?php echo $encodeExplorer->getString("lost_password"); ?></a></p>
                </div>
            </div>
    <?php
    if ($setUp->getConfig("registration_enable") == true ) { ?>
            <p>
                <a class="btn btn-default btn-block" href="?reg=1">
                    <i class="fa fa-user-plus"></i> <?php echo $encodeExplorer->getString("registration"); ?>
                </a>
            </p>
            <?php
    }   ?>
        </div>
    </section>
    <?php
}

if ($gateKeeper->isAccessAllowed() 
    && $gateKeeper->showLoginBox()
) { ?>

        <section class="vfmblock">
            <form enctype="multipart/form-data" method="post" 
            action="<?php echo $encodeExplorer->makeLink(false, null, ""); ?>" class="form-inline" role="form">
                <div id="login_bar">
                    <div class="form-group">
                        <label class="sr-only" for="user_name">
                            <?php echo $encodeExplorer->getString("username"); ?>:
                        </label>
                        <input type="text" name="user_name" value="" id="user_name" class="form-control" 
                        placeholder="<?php echo $encodeExplorer->getString("username"); ?>" />
                    </div>
                    <div class="form-group">
                        <label class="sr-only" for="user_pass">
                            <?php echo $encodeExplorer->getString("password"); ?>: 
                        </label>
                        <input type="password" name="user_pass" id="user_pass" class="form-control" 
                        placeholder="<?php echo $encodeExplorer->getString("password"); ?>" />
                    </div>
    <?php 
    /* ************************ CAPTCHA ************************* */
    if ($setUp->getConfig("show_captcha") == true ) { 
        $capath = "vfm-admin/";
        include "vfm-admin/include/captcha.php"; 
    }   ?>
                    <button type="submit" class="btn btn-primary" />
                        <i class="fa fa-sign-in"></i> 
                        <?php echo $encodeExplorer->getString("log_in"); ?>
                    </button>
    <?php
    if ($setUp->getConfig("registration_enable") == true ) { ?>
                <a class="btn btn-default" href="?reg=1">
                    <i class="fa fa-user-plus"></i> <?php echo $encodeExplorer->getString("registration"); ?>
                </a>
            <?php
    }   ?>
                </div>
            </form>
            <a class="small" href="?rp=req"><?php echo $encodeExplorer->getString("lost_password"); ?></a>
        </section>
        <?php
}