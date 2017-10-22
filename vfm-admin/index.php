<?php
/**
 * VFM - veno file manager administration
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
if ($_CONFIG['debug_mode'] === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL ^ E_NOTICE);
}
if (strlen($_CONFIG['session_name']) < 5) {
    header('Location:login.php');
    exit;
}
session_name($_CONFIG["session_name"]);
session_start();

require_once 'users/users.php';
require_once 'translations/en.php';
require_once 'class.php';

if (!GateKeeper::isSuperAdmin()) {
    header('Location:login.php');
    exit;
}
require_once 'admin-panel/view/admin-head.php';
// user available quota
$_QUOTA = array(
    "10",
    "20",
    "50",
    "100",
    "200",
    "500",
    ); 
// exipration for downloadable links
$share_lifetime = array(
    // "days" => "menu value"
    "1" => "24 h",
    "2" => "48 h",
    "3" => "72 h",
    "5" => "5 days",
    "7" => "7 days",
    "10" => "10 days",
    "30" => "30 days",
    "365" => "1 year",
    "36500" => "Unlimited (100 years)",
    ); ?>
<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php print $encodeExplorer->getString('administration')." | ".$setUp->getConfig('appname'); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Language" content="<?php echo $lang; ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="images/favicon.ico">
    <meta name="viewport" 
    content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="admin-panel/css/admin.min.css">
    <?php 
    $rtlclass = "";
    if ($setUp->getConfig("txt_direction") == "RTL") {
        $rtlclass = "rtl"; ?>
        <link rel="stylesheet" href="css/bootstrap-rtl.min.css">
    <?php 
    } ?>
    <link rel="stylesheet" href="admin-panel/css/admin-skins.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <script src="js/jquery-1.12.4.min.js"></script>
    <!--[if lt IE 9]>
    <script src="js/html5.js" type="text/javascript"></script>
    <script src="js/respond.min.js" type="text/javascript"></script>
    <![endif]-->
</head>
<?php $skin = $setUp->getConfig('admin_color_scheme') ? $setUp->getConfig('admin_color_scheme') : 'blue'; ?>
<body class="skin-<?php print $skin; ?> fixed sidebar-mini admin-body <?php echo $rtlclass; ?>" data-target="#scrollspy" data-spy="scroll">
    <div class="anchor" id="view-preferences"></div>
    <div class="wrapper">
        <header class="main-header">
            <a href="./" class="logo">
                <?php print $setUp->getConfig('appname'); ?>
            </a>
            <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="<?php echo $setUp->getConfig('script_url'); ?>">
                                <i class="fa fa-home fa-fw"></i> 
                            </a>
                        </li>
                        <li>
                            <a href="login.php?logout" title="<?php echo $encodeExplorer->getString("log_out"); ?>">
                                <i class="fa fa-sign-out fa-fw"></i> 
                            </a>
                        </li>
                        <li class="dropdow">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-flag fa-fw"></i>
                                <?php // echo $encodeExplorer->getString("LANGUAGE_NAME"); ?>
                            </a>
                            <?php print ($encodeExplorer->printLangMenu()); ?>
                        </li>

                    </ul>
                </div>
            </nav>
        </header>
        <?php require "admin-panel/view/sidebar.php"; ?>
        <div class="content-wrapper">
            <?php
            $callout_icon = array(
                'yep' => 'fa-check',
                'nope' => 'fa-times',
                'boh' => 'fa-exclamation',
            );
            $callout_status = array(
                'yep' => 'success',
                'nope' => 'danger',
                'boh' => 'warning',
            );
            if (is_array($response)) {
                foreach ($response as $alert) { 
                    $icona = isset($callout_icon[$alert['status']]) ? $callout_icon[$alert['status']] : 'danger';
                    $bgcolor = isset($callout_status[$alert['status']]) ? $callout_status[$alert['status']] : 'fa-exclamation';
                    ?>
                    <div class="alert bs-callout bs-callout-<?php echo $bgcolor; ?> fade" role="alert">
                        <i class="fa <?php echo $icona; ?>"></i> <?php echo $alert['message']; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php
                } ?>
                <script type="text/javascript">
                $(document).ready(function(){
                    $('.bs-callout').addClass('in');
                });
                </script>
                <?php
            } ?>
            <?php 
                /**
                * LANGUAGE MANAGER
                */ 
            if (isset($_GET['languagemanager'])) { ?>

                <div class="content-header">
                    <h3>
                        <i class="fa fa-language"></i> 
                        <?php print $encodeExplorer->getString("language_manager"); ?>
                    </h3>
                </div>
                <div class="content">
                <?php
                if (($_GET['languagemanager'] == 'editlang' && $editlang)
                    || ($_GET['languagemanager'] == 'newlang' 
                    && $postnewlang && strlen($postnewlang) == 2
                    && !array_key_exists($postnewlang, $translations))
                ) {         
                    include "admin-panel/view/language/edit.php";        
                } else {
                    include "admin-panel/view/language/panel.php";
                } 
                ?>
                </div>
            <?php
            } elseif (isset($_GET['users'])) { 
                /**
                * USERS
                */ 
                ?>
                <div class="content-header">
                    <h3><i class="fa fa-users"></i> 
                        <?php print $encodeExplorer->getString("users"); ?>
                    </h3>
                </div>
                <div class="content body">
                    <div class="row">
                        <?php
                        include "admin-panel/view/users/new-user.php";
                        include "admin-panel/view/users/master-admin.php";
                        ?>
                    </div>
                    <?php
                    include "admin-panel/view/users/list-users.php";
                    include "admin-panel/view/users/modal-user.php";
                    ?>
                </div>
            <?php
            } elseif (isset($_GET['log'])) { 
                /**
                * ANALYTICS
                */
            ?>
                <div class="content-header">
                    <h3><i class="fa fa-pie-chart"></i>
                    <?php print $encodeExplorer->getString("statistics"); ?>
                    </h3>
                    <?php
                    include "admin-panel/view/analytics/selector.php";
                    ?>
                </div>
                <div class="content">
                    <?php
                    include "admin-panel/view/analytics/charts.php";
                    include "admin-panel/view/analytics/table.php";
                    include "admin-panel/view/analytics/loader.php";
                    ?>
                </div>

                <?php
            } else { 
                /**
                * CONFIG SETTINGS
                */
                ?>
                <div class="content-header">
                    <h3><i class="fa fa-tachometer"></i> 
                        <?php print $encodeExplorer->getString("preferences"); ?>
                    </h3>
                </div>
                <div class="content">
                    <form role="form" method="post" id="settings-form"
                    action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" 
                    enctype="multipart/form-data">
                        <?php
                        include "admin-panel/view/dashboard/general.php"; 
                        include "admin-panel/view/dashboard/lists.php"; 
                        include "admin-panel/view/dashboard/share.php"; 
                        include "admin-panel/view/dashboard/permissions.php";
                        include "admin-panel/view/dashboard/registration.php"; 
                        include "admin-panel/view/dashboard/email.php";
                        include "admin-panel/view/dashboard/activities.php";
                        include "admin-panel/view/dashboard/appearance.php"; 
                        ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                <button type="submit" class="btn btn-info btn-lg btn-block clear">
                                    <i class="fa fa-refresh"></i> 
                                    <?php print $encodeExplorer->getString("save_settings"); ?>
                                </button>
                            </div></div>

                        </div>
                            <div class="form-group">
                               
                            <div class="checkbox checkbox-big">
                                <label>
                                    <input type="checkbox" name="debug_mode" 
                                    <?php
                                    if ($setUp->getConfig('debug_mode') === true) {
                                        echo "checked";
                                    } ?>>
                                    <i class="fa fa-wrench"></i> DEBUG MODE 
                                        <a title="display general PHP notices and SMTP connection responses for e-mail forms" class="tooltipper" data-placement="right" href="javascript:void(0)">
                                            <i class="fa fa-question-circle"></i>
                                        </a>
                                </label>
                            </div>
                             </div>
                    </form>
                </div> <!-- contant -->
                <?php 
            } // END SWITCH PANELS ?>
        <br>
        <br>
        <br>
    </div> <!-- content-wrapper -->

    <footer class="main-footer">
        <div class="pull-right">
            <a href="http://filemanager.veno.it/" target="_blank" title="Current version">
                <i class="vfmi vfmi-mark"></i> 
                <small> <?php echo $vfm_version; ?></small>
            </a>
        </div>
        <a href="../"><strong><?php print $setUp->getConfig('appname'); ?></strong></a> &copy; <?php echo date('Y'); ?>
    </footer>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script src="admin-panel/js/app.min.js"></script>
    <script src="admin-panel/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <link rel="stylesheet" href="admin-panel/plugins/summernote/summernote.css">
    <script src="admin-panel/plugins/summernote/summernote.min.js"></script>
    <script type="text/javascript" src="admin-panel/js/admin.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            /**
            * start pretty multiselect for user folders
            */  
            multiselectWithOptions(' <?php print $encodeExplorer->getString("select_all"); ?>','<?php print $encodeExplorer->getString("selected_files"); ?>', '<?php print $encodeExplorer->getString("available_folders"); ?>');
            /**
            * update default folders for self registered users
            */ 
            var regdata = [];
            var regfolders = $(".s-reguserfolders");
            regfolders.each(function(){
                regdata.push($(this).val());
            });
            $("#r-reguserfolders").val(regdata);
            $(".assignfolder").multiselect('refresh');
            /**
            * toggle registration user quota panel
            */ 
            showHideQuota($('.assignfolder'));
        });
    </script>
</body>
</html>