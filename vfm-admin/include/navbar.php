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
$stepback = $islogin ? '' : 'vfm-admin/';
?>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#collapse-vfm-menu">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <?php
            /**
            * Brand button
            */
            ?>
            <a class="navbar-brand" href="<?php echo $setUp->getConfig("script_url"); ?>">
                <?php echo $setUp->getConfig("appname"); ?>
            </a>
        </div>
        <div class="collapse navbar-collapse" id="collapse-vfm-menu">
            <ul class="nav navbar-nav navbar-right">
<?php
/**
* User menu
*/
if ($gateKeeper->isUserLoggedIn()) {

    $username = $gateKeeper->getUserInfo('name');
    $avaimg = $gateKeeper->getAvatar($username, $stepback);
    $avadefault = $gateKeeper->getAvatar('', $stepback);

    if ($setUp->getConfig("show_usermenu") == true ) { ?>
                <li>
                    <a class="edituser" href="#" data-toggle="modal" data-target="#userpanel">
                        <img class="img-circle avatar" width="28px" height="28px" src="<?php echo $avaimg."?t=".rand(1, 100); ?>" />
                        <span class="hidden-sm">
                            <strong><?php echo $username; ?></strong>
                        </span>
                    </a>
                </li>
        <?php
        if ($gateKeeper->isSuperAdmin()) { ?>
                <li>
                    <a href="<?php echo $setUp->getConfig("script_url"); ?>vfm-admin/">
                        <i class="fa fa-cogs fa-fw"></i> 
                        <span class="hidden-sm">
                            <?php echo $encodeExplorer->getString("administration"); ?>
                        </span>
                    </a>
                </li>
        <?php
        } ?>
                <li>
                    <a href="<?php echo $setUp->getConfig("script_url").$encodeExplorer->makeLink(true, null, ""); ?>">
                        <i class="fa fa-sign-out fa-fw"></i> 
                        <span class="hidden-sm">
                            <?php echo $encodeExplorer->getString("log_out"); ?>
                        </span>
                    </a>
                </li>
    <?php
    } else { ?>
                <li>
                    <a href="<?php echo $encodeExplorer->makeLink(true, null, ""); ?>">
                        <i class="fa fa-sign-out fa-fw"></i> 
                        <span class="hidden-sm">
                            <?php echo $encodeExplorer->getString("log_out"); ?>
                        </span>
                    </a>
                </li>
        <?php 
        if ($gateKeeper->isSuperAdmin()) { ?>
                <li>
                    <a href="<?php echo $setUp->getConfig("script_url"); ?>vfm-admin/">
                        <i class="fa fa-cogs fa-fw"></i> 
                        <span class="hidden-sm">
                            <?php echo $encodeExplorer->getString("administration"); ?>
                        </span>
                    </a>
                </li>
        <?php
        }
    }
} // end logged user
/**
* Language selector
*/ 
if ($setUp->showLangMenu()) { ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <i class="fa fa-flag fa-fw"></i> 
    <?php
    if ($setUp->getConfig('show_langname')) { ?>
                        <span class="hidden-sm">
                            <?php echo $encodeExplorer->getString("LANGUAGE_NAME"); ?>
                        </span>
    <?php
    } ?>
                        <span class="caret"></span>
                    </a>
    <?php
    echo $encodeExplorer->printLangMenu($stepback);
    ?>
                </li>
    <?php
} ?>
            </ul>
        </div>
    </div>
</nav>

<?php
/**
* User Panel
*/
if ($gateKeeper->isUserLoggedIn() && $setUp->getConfig("show_usermenu") == true ) { ?>

    <script src="<?php echo $stepback; ?>js/avatars.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
      Avatars('<?php echo $avaimg; ?>', '<?php echo $avadefault; ?>');
    });
    </script>
    
    <div class="modal userpanel fade" id="userpanel" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
            </button>
            <ul class="nav nav-pills" role="tablist">
              <li role="presentation" class="active">
                    <a href="#upprof" aria-controls="home" role="tab" data-toggle="pill">
                        <i class="fa fa-edit"></i> 
                        <?php echo $encodeExplorer->getString("update_profile"); ?>
                    </a>
              </li>
              <li role="presentation">
                    <a href="#upava" aria-controls="home" role="tab" data-toggle="pill">
                        <i class="fa fa-user"></i> 
                        <?php echo $encodeExplorer->getString("avatar"); ?>
                    </a>
              </li>
            </ul>
          </div>

          <div class="modal-body">
            <div class="tab-content">
              <div role="tabpanel" class="tab-pane fade text-center" id="upava">
                <div class="avatar-panel">
                  <div class="image-editor">
                    <label for="uppavatar" class="upload-wrapper">
                        <input type="file" id="uppavatar" class="cropit-image-input">
                    </label>

                    <i class="fa fa-times fa-lg pull-right text-muted remove-avatar"></i>
                    
                    <div class="updated"></div>

                    <input type="hidden" class="image-name" value="<?php print md5($gateKeeper->getUserInfo('name')); ?>">
                    <div class="cropit-image-preview"></div>
                    <div class="image-size-wrapper">         
                        <input type="range" class="cropit-image-zoom-input slider">
                    </div>
                  </div>
                </div>

                <div class="uppa btn btn-default">
                    <?php print $encodeExplorer->getString("upload"); ?> <i class="fa fa-upload fa-fw"></i>
                </div>
                <div class="export btn btn-primary hidden">
                    <?php print $encodeExplorer->getString("update"); ?> <i class="fa fa-check-circle fa-fw"></i>
                </div>

              </div> <!-- tabpanel -->

              <div role="tabpanel" class="tab-pane fade in active" id="upprof">
                <form role="form" method="post" id="usrForm" autocomplete="off" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);?>">
                  <div class="form-group">
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
                        * <?php print $encodeExplorer->getString("current_pass"); ?>
                    </label> 
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-unlock fa-fw"></i></span>
                        <input name="user_old_pass" type="password" id="oldp" required class="form-control">
                    </div>
                  </div>

                  <div class="form-group">
                      <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-refresh"></i>
                        <?php print $encodeExplorer->getString("update"); ?>
                      </button>
                  </div>

                </form>
              </div> <!-- tabpanel -->
            </div><!-- tab-content -->
          </div> <!-- modal-body -->
        </div> <!-- modal-content -->
      </div> <!-- modal-dialog -->
    </div> <!-- modal -->
    <?php
} ?>