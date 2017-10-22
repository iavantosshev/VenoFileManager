<?php
/**
 * VFM - veno file manager: include/list-folders.php
 * list folders inside curret directory
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
* List Folders
*/
if ($gateKeeper->isAccessAllowed()) { 

    $cleandir = "?dir=".substr($setUp->getConfig('starting_dir').$gateKeeper->getUserInfo('dir'), 2);
    $stolink = $encodeExplorer->makeLink(false, null, $location->getDir(false, true, false, 1));
    $stodeeplink = $encodeExplorer->makeLink(false, null, $location->getDir(false, true, false, 0));

    if (strlen($stolink) > strlen($cleandir)) {
            $parentlink = $encodeExplorer->makeLink(false, null, $location->getDir(false, true, false, 1));
    } else {
            $parentlink = "?dir=";
    }
    if (strlen($stodeeplink) > strlen($cleandir)
        && $setUp->getConfig("show_path") !== true
    ) { ?>
        <ol class="breadcrumb">
            <li>
                <a href="<?php echo $parentlink; ?>">
                    <i class="fa fa-angle-left"></i> <i class="fa fa-folder-open"></i>
                </a>
            </li>
        </ol>
    <?php
    }
       
    // Ready to display folders.
    if ($encodeExplorer->dirs) { ?>
        <section class="vfmblock tableblock ghost ghost-hidden">
            <table class="table" width="100%" id="sortable">
                <thead>
                    <tr class="rowa two">
                        <td></td>
                        <td class="mini"><span class="sorta nowrap"><i class="fa fa-sort-alpha-asc"></i></span></td>
                        <td class="mini hidden-xs"><span class="sorta nowrap"><i class="fa fa-calendar"></i></span></td>
                        <?php
                        if ($location->editAllowed()) { 
                            // mobile menu
                            if ($setUp->getConfig("download_dir_enable") === true
                                || $gateKeeper->isAllowed('rename_dir_enable')
                                || $gateKeeper->isAllowed('delete_dir_enable')
                            ) { ?>
                            <td class="mini text-center visible-xs">
                                <i class="fa fa-cogs"></i>
                            </td>
                            <?php 
                            } ?>
                            <?php
                            // download column
                            if ($setUp->getConfig("download_dir_enable") === true) { ?>
                            <td class="mini text-center">
                                <i class="fa fa-download hidden-xs"></i>
                            </td>
                            <?php 
                            } ?>
                            <?php
                            // edit column
                            if ($gateKeeper->isAllowed('rename_dir_enable')) { ?>
                            <td class="mini text-center hidden-xs">
                                <i class="fa fa-pencil"></i>
                            </td>
                            <?php 
                            } ?>
                            <?php
                            // delete column
                            if ($gateKeeper->isAllowed('delete_dir_enable')) { ?>
                                <td class="mini text-center hidden-xs">
                                    <i class="fa fa-trash-o"></i>
                                </td>
                            <?php 
                            } 
                        } ?>
                    </tr>
                </thead>
                <tbody>
        <?php
        foreach ($encodeExplorer->dirs as $dir) {
            $dirname = $dir->getName();
            $dirpath = $dir->getLocation().$dirname;
            $dirdatatime = filemtime($dirpath);
            $dirtime = $setUp->formatModTime($dirdatatime);
            $nameencoded = $dir->getNameEncoded();
            $locationDir = $location->getDir(false, true, false, 0);
            $del = $locationDir.$nameencoded;
            $delquery = base64_encode($del);
            $alt = $setUp->getConfig('salt');
            $altone = $setUp->getConfig('session_name');
            $cash = md5($delquery.$alt.$altone);
            $thislink = $encodeExplorer->makeLink(false, null, $del);
            $thisdel = $encodeExplorer->makeLink(false, $del, $locationDir);
            $thisdir = urldecode($locationDir);
            $dash = md5($alt.base64_encode($dirpath).$altone);
            
            if ($setUp->getConfig('enable_prettylinks') == true) {
                $downlink = 'download/f/'.base64_encode($dirpath).'/h/'.$dash;
            } else {
                $downlink = 'vfm-admin/vfm-downloader.php?f='.base64_encode($dirpath).'&h='.$dash;
            } ?>
                    <tr class="rowa">
            <?php 
            if ($setUp->getConfig("show_folder_counter") === true) {
                $quanti = Utils::countContents($location->getDir(false, false, false, 0).$dirname);
                $quantifiles = $quanti['files'];
                $quantedir = $quanti['folders']; ?>
                        <td class="icon nowrap folder-badges">
                            <a href="<?php echo $thislink; ?>">
                                <span class="badge">
                                    <i class="fa fa-folder-o"></i> 
                                    <?php echo $quantedir; ?>
                                </span>
                                <span class="badge">
                                    <i class="fa fa-files-o"></i> 
                                    <?php echo $quantifiles; ?>
                                </span> 
                            </a>
                        </td>
            <?php   
            } else { ?>
                        <td></td>
            <?php
            } ?>
                        <td class="name" data-order="<?php echo $dirname; ?>">
                            <div class="relative">
                                <a class="full-lenght" href="<?php echo $thislink; ?>">
                                    <span class="icon text-center">
                                        <i class="fa fa-folder fa-lg fa-fw"></i> <?php echo $dirname; ?>
                                    </span>
                                </a>
                                <span class="hover">
                                    <i class="fa fa-folder-open-o fa-fw"></i>
                                </span>
                            </div>
                        </td>
                        <td class="hidden-xs mini reduce nowrap" data-order="<?php echo $dirdatatime; ?>">
                            <?php echo $dirtime; ?>
                        </td>
            <?php
            if ($location->editAllowed()) {
                if ($setUp->getConfig("download_dir_enable") === true
                    || $gateKeeper->isAllowed('rename_dir_enable')
                    || $gateKeeper->isAllowed('delete_dir_enable')
                ) { ?>
                        <td class="text-center visible-xs">
                            <div class="dropdown">
                                <a class="round-butt butt-mini dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-cog"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <?php
                                    if ($setUp->getConfig("download_dir_enable") === true) { ?>
                                    <li>
                                        <a class="zipdir" 
                                            data-zip="<?php echo base64_encode($dirpath); ?>" 
                                            data-dash="<?php echo $dash; ?>" 
                                            data-thisdir="<?php echo $thisdir; ?>" 
                                            data-thisname="<?php echo $dirname; ?>">
                                            <i class="fa fa-cloud-download"></i> 
                                            <?php echo $encodeExplorer->getString("download"); ?>
                                        </a>
                                    </li>
                                    <?php
                                    } ?>
                                    <?php
                                    if ($gateKeeper->isAllowed('rename_dir_enable')) { ?>
                                    <li class="rename">
                                        <a href="javascript:void(0)" 
                                            data-thisdir="<?php echo $thisdir; ?>" 
                                            data-thisname="<?php echo $dirname; ?>">
                                            <i class="fa fa-edit"></i> 
                                            <?php echo $encodeExplorer->getString("rename"); ?>
                                        </a>
                                    </li>
                                    <?php
                                    } ?>
                                    <?php
                                    if ($gateKeeper->isAllowed('delete_dir_enable')) { ?>
                                    <li class="del">
                                        <a href="<?php echo $thisdel; ?>&h=<?php echo $cash; ?>&fa=<?php echo $delquery; ?>" data-name="<?php echo $dirname; ?>">
                                            <i class="fa fa-trash-o"></i> 
                                            <?php echo $encodeExplorer->getString("delete"); ?>
                                        </a>
                                    </li>
                                    <?php
                                    } ?>
                                  </ul>
                            </div>
                        </td>
                <?php
                } // END mobile dropdown ?>
                <?php
                if ($setUp->getConfig("download_dir_enable") === true) { ?>
                        <td class="text-center">
                            <a class="round-butt butt-mini zipdir hidden-xs" 
                                data-zip="<?php echo base64_encode($dirpath); ?>" 
                                data-dash="<?php echo $dash; ?>" href="javascript:void(0)"
                                data-thisname="<?php echo $dirname; ?>">
                                <i class="fa fa-cloud-download"></i>
                            </a>
                        </td>
                <?php
                } ?>    
                <?php
                if ($gateKeeper->isAllowed('rename_dir_enable')) { ?>
                        <td class="text-center hidden-xs">
                            <div class="rename">
                                <a class="round-butt butt-mini" href="javascript:void(0)" 
                                    data-thisdir="<?php echo $thisdir; ?>" 
                                    data-thisname="<?php echo $dirname; ?>">
                                    <i class="fa fa-pencil-square-o"></i>
                                </a>
                            </div>
                        </td>
                <?php
                } ?>
                <?php
                if ($gateKeeper->isAllowed('delete_dir_enable')) { ?>
                        <td class="del text-center hidden-xs">
                            <a class="round-butt butt-mini" data-name="<?php echo $dirname; ?>" 
                                href="<?php echo $thisdel; ?>&h=<?php echo $cash; ?>&fa=<?php echo $delquery; ?>">
                                <i class="fa fa-times"></i>
                            </a>
                        </td>
                <?php
                } ?>

            <?php 
            } // END location allowed ?>
                    </tr>
        <?php
        } // END foreach dir ?>
                </tbody>
            </table>
        </section>
        <?php
        if ($setUp->getConfig("download_dir_enable") === true) { ?>
            <div id="zipmodal" class="modal fade" tabindex="-1" role="dialog" 
            data-backdrop="static" data-keyboard="false">
              <div class="modal-dialog modal-sm">
                <div class="modal-content text-center">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body zipicon">
                        <i class="fa fa-folder-open-o fa-5x"></i>
                        <span class="ziparrow"></span>
                        <i class="fa fa-file-archive-o fa-5x"></i>
                    </div>
                    <div class="modal-footer">
                        <div class="text-center zipresp">
                            <i class="fa fa-refresh fa-spin fa-2x fa-fw"></i>
                        </div>
                    </div>
                </div>
              </div>
            </div>
        <?php
        } // END if download folders
    } // END if dirs
} // END isAccessAllowed();
