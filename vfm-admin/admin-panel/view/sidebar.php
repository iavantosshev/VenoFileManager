<?php
/**
 * VFM - veno file manager administration sidebar
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
$adminurl = $setUp->getConfig('script_url')."vfm-admin";
if ($activesec == 'home') {
    $open = " active";
} else {
    $open = "";
} ?>
<aside class="main-sidebar">
    <section class="sidebar" id="scrollspy">
        <ul class="sidebar-menu nav">
            <?php
            if (isset($_SESSION['vfm_admin_name'])) { ?>

            <?php
            } ?>
                <li class="header text-uppercase"><?php print $encodeExplorer->getString("administration"); ?></li>
            </li>
        <?php 
        if ($activesec == 'home') { ?>
            <li class="treeview active">
                <a href="#view-preferences">
                    <i class="fa fa-dashboard fa-fw"></i> 
                    <span><?php print $encodeExplorer->getString("preferences"); ?></span> 
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li class="<?php echo $open; ?>"><a href="#view-general">
                        <i class="fa fa-cogs fa-fw"></i> <span> 
                        <?php echo $encodeExplorer->getString("general_settings"); ?>
                        </span></a>
                    </li>
                    <li><a href="#view-lists"><i class="fa fa-list-alt fa-fw"></i> 
                        <span><?php print $encodeExplorer->getString('lists'); ?></span></a>
                    </li>
                    <li><a href="#view-share"><i class="fa fa-paper-plane fa-fw"></i> 
                        <span><?php print $encodeExplorer->getString('share_files'); ?></span></a>
                    </li>
                    <li><a href="#view-permissions"><i class="fa fa-graduation-cap fa-fw"></i> 
                        <span><?php print $encodeExplorer->getString('permissions'); ?></span></a>
                    </li>
                    <li><a href="#view-registration"><i class="fa fa-user-plus fa-fw"></i> 
                        <span><?php print $encodeExplorer->getString('registration'); ?></span></a>
                    </li>
                    <li><a href="#view-email"><i class="fa fa-envelope-o fa-fw"></i> 
                        <span><?php print $encodeExplorer->getString('email'); ?></span></a>
                    </li>
                    <li><a href="#view-activities"><i class="fa fa-bar-chart fa-fw"></i> 
                        <span><?php print $encodeExplorer->getString("activity_register"); ?></span></a>
                    </li>
                    <li><a href="#view-appearance"><i class="fa fa-paint-brush fa-fw"></i> 
                        <span><?php print $encodeExplorer->getString("appearance"); ?></span></a>
                    </li>
                </ul>
            </li>
        <?php 
        } else { ?>
            <li>
                <a href="index.php">
                    <i class="fa fa-dashboard fa-fw"></i> 
                    <span><?php print $encodeExplorer->getString("preferences"); ?></span> 
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
            </li>
        <?php
        }

        print "<li";
        if ($activesec == 'users') {
            print " class=\"active\"";
        }
        print "><a href=\"?users=go\">
        <i class=\"fa fa-users fa-fw\"></i> <span>" 
        .$encodeExplorer->getString("users").
        "</span></a></li>";
        print "<li";
        if ($activesec == 'lang') {
            print " class=\"active\"";
        }
        print "><a href=\"?languagemanager=go\">
        <i class=\"fa fa-language fa-fw\"></i> <span>" 
        .$encodeExplorer->getString("translations").
        "</span></a></li>";
        
        if ($setUp->getConfig('log_file') == true) { 
            print "<li";
            if ($activesec == 'log') {
                print " class=\"active\"";
            }
            print "><a href=\"?log=go\">
                <i class=\"fa fa-bar-chart-o fa-pie-chart\"></i><span> " 
             .$encodeExplorer->getString("statistics").
            "</span></a></li>";
        } ?>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>