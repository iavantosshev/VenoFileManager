<?php
/**
 * VFM - veno file manager: include/navbar.php
 * user menu, user panel and language selector
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
?>

<div class="topbanner">
    <div class="container">
<?php
/**
* User menu
*/
if ($gateKeeper->isUserLoggedIn()) {
    
    if ($setUp->getConfig("show_usermenu") == true ) { ?>

        <div class="btn-group pull-right">
            <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
            <?php echo $gateKeeper->getUserInfo('name'); ?> <i class="fa fa-user"></i> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
        <?php // administration link
        if ($gateKeeper->isSuperAdmin()) { ?>
                <li>
                    <a href="vfm-admin/"><i class="fa fa-cogs"></i> 
                        <?php echo $encodeExplorer->getString("administration"); ?>
                    </a>
                </li>
        <?php
        } ?>
                <li>
                    <a class="edituser" href="#" data-toggle="modal" data-target="#userpanel">
                        <i class="fa fa-cog"></i> <?php echo $encodeExplorer->getString("update_profile"); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $encodeExplorer->makeLink(true, null, ""); ?>">
                        <i class="fa fa-sign-out"></i> <?php echo $encodeExplorer->getString("log_out"); ?>
                    </a>
                </li>
            </ul>
        </div> 
    <?php
    } else { ?>
        <a class="pull-right btn btn-warning btn-sm" href="<?php echo $encodeExplorer->makeLink(true, null, ""); ?>">
            <i class="fa fa-sign-out"></i> <?php echo $encodeExplorer->getString("log_out"); ?>
        </a> 
        <?php 
        if ($gateKeeper->isSuperAdmin()) { ?>
        <a class="pull-right btn btn-primary btn-sm" href="vfm-admin/">
            <i class="fa fa-cogs"></i> <?php echo $encodeExplorer->getString("administration"); ?>
        </a>
        <?php
        }
    }
} ?>

<?php
/**
* Language selector
*/ 
if ($setUp->showLangMenu()) { ?>

    <div class="btn-group pull-right">
        <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-flag"></i>
        </button>
    <?php

    if ($islogin == true) { 
        print ($encodeExplorer->printLangMenu(''));
    } else {
        print ($encodeExplorer->printLangMenu('vfm-admin/'));
    }
} ?>
    </div> <!-- .vfm-wrapper -->
</div> <!-- .topbanner -->

<?php
/**
* User Panel
*/
if ($gateKeeper->isUserLoggedIn() && $setUp->getConfig("show_usermenu") == true ) { ?>
    <div class="modal userpanel fade" id="userpanel" tabindex="-1">

        <div class="modal-dialog">
            <div class="modal-content">
           
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title"><i class="fa fa-cog"></i> <?php echo $encodeExplorer->getString("update_profile"); ?></h4>
                </div>
                <form role="form" method="post" id="usrForm" autocomplete="off" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);?>">
                    <div class="modal-body">
                       
                        <label for="user_new_name">
                            <?php print $encodeExplorer->getString("username"); ?>
                        </label>
                        <input name="user_old_name" type="hidden" readonly 
                        class="form-control" value="<?php print $gateKeeper->getUserInfo('name'); ?>">

                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                            <input name="user_new_name" type="text" 
                            class="form-control" value="<?php print $gateKeeper->getUserInfo('name'); ?>">
                        </div>
                    
                            <label for="user_new_email">
                                <?php print $encodeExplorer->getString("email"); ?>
                            </label>
                            <input name="user_old_email" type="hidden" readonly 
                            class="form-control" value="<?php print $gateKeeper->getUserInfo('email'); ?>">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope fa-fw"></i></span>
                            <input name="user_new_email" type="text" 
                            class="form-control" value="<?php print $gateKeeper->getUserInfo('email'); ?>">
                        </div>
              

                        <label for="user_new_pass">
                            <?php print $encodeExplorer->getString("new_password"); ?>
                        </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock fa-fw"></i></span>
                            <input name="user_new_pass" id="newp" type="password" class="form-control">
                        </div>
                 
                        <label for="user_new_pass_confirm">
                            <?php print $encodeExplorer->getString("new_password")
                            ." (".$encodeExplorer->getString("confirm").")"; ?>
                        </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock fa-fw"></i></span>
                            <input name="user_new_pass_confirm" id="checknewp" type="password" class="form-control">
                        </div>

                        <label for="user_old_pass">
                            <?php print $encodeExplorer->getString("current_pass"); ?>
                        </label> 
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-unlock fa-fw"></i></span>
                            <input name="user_old_pass" type="password" id="oldp" required class="form-control">
                        </div>

                    </div> <!-- modal-body -->
                    <div class="modal-footer">

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-refresh"></i>
                                <?php print $encodeExplorer->getString("update"); ?>
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <?php
} ?>