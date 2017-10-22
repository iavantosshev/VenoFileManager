<?php
/**
 * VFM - veno file manager: include/list-files.php
 * list files inside current directory
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
* List Files
*/
if ($gateKeeper->isAccessAllowed() && $location->editAllowed()) { 
    if ($encodeExplorer->files) { ?>
    <section class="vfmblock tableblock ghost ghost-hidden">
        <div class="action-group">
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle groupact" data-toggle="dropdown">
                    <i class="fa fa-cog"></i> 
                    <?php echo $encodeExplorer->getString("group_actions"); ?> 
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a class="multid" href="#">
                        <i class="fa fa-cloud-download"></i> 
                        <?php echo $encodeExplorer->getString("download"); ?></a>
                    </li>
                <?php
                if ($gateKeeper->isAllowed('move_enable')) { ?>
                    <li>
                        <a class="multimove" href="#">
                            <i class="fa fa-arrow-right"></i> 
                            <?php echo $encodeExplorer->getString("move"); ?>
                        </a>
                    </li>
                <?php
                }
                if ($gateKeeper->isAllowed('copy_enable')) { ?>
                   <li>
                        <a class="multicopy" href="#">
                            <i class="fa fa-files-o"></i> 
                            <?php echo $encodeExplorer->getString("copy"); ?>
                        </a>
                    </li>
                <?php
                }
                if ($gateKeeper->isAllowed('delete_enable')) { ?>
                    <li><a class="multic" href="#">
                            <i class="fa fa-trash-o"></i> 
                            <?php echo $encodeExplorer->getString("delete"); ?>
                        </a>
                    </li>
                <?php
                } ?>
                </ul>
            </div> <!-- .btn-group -->
            <?php
            if ($setUp->getConfig('sendfiles_enable')) { ?>
            <button class="btn btn-default manda">
                <i class="fa fa-paper-plane"></i> 
                <?php echo $encodeExplorer->getString("share"); ?>
            </button>
                <?php
            }
            $listdefault = $setUp->getConfig('list_view') ? $setUp->getConfig('list_view') : 'list';
            $listview = isset($_SESSION['listview']) ? $_SESSION['listview'] : $listdefault;

            if ($listview == 'grid') {
                $listclass = 'gridview';
                $switchclass = 'grid';
            } else {
                $listclass = '';
                $switchclass = '';
            } ?>
            <div class="switchview pull-right <?php echo $switchclass; ?>" title="<?php echo $encodeExplorer->getString("view"); ?>"></div>
        </div> <!-- .action-group -->

        <form id="tableform">
            <table class="table <?php echo $listclass; ?>" width="100%" id="sort">
                <thead>
                    <tr class="rowa one">
                        <td class="text-center">
                            <a href="#" title="<?php echo $encodeExplorer->getString("select_all"); ?>" id="selectall">
                                <i class="fa fa-check fa-lg"></i>
                            </a>
                        </td>
                        <td class="icon"></td>
                        <td class="mini h-filename">
                            <span class="visible-xs sorta nowrap">
                                <i class="fa fa-sort-alpha-asc"></i>
                            </span>
                            <span class="hidden-xs sorta nowrap">
                                <?php echo $encodeExplorer->getString("file_name"); ?>
                            </span>
                        </td>
                        <td class="taglia reduce mini h-filesize hidden-xs">
                            <span class="text-center sorta nowrap">
                                <?php echo $encodeExplorer->getString("size"); ?>
                            </span>
                        </td>
                        <td class="reduce mini h-filedate hidden-xs">
                            <span class="text-center sorta nowrap">
                                <?php echo $encodeExplorer->getString("last_changed"); ?>
                            </span>
                        </td>
                    <?php
                    if ($gateKeeper->isAllowed('rename_enable')) { ?>
                        <td class="mini text-center gridview-hidden hidden-xs">
                            <i class="fa fa-pencil"></i>
                        </td>
                    <?php
                    } ?>
                    <td class="mini text-center gridview-hidden">
                    <?php 
                    if ($gateKeeper->isAllowed('delete_enable')) {  ?>
                        <i class="fa fa-trash-o hidden-xs"></i>
                    <?php
                    } ?>
                        <i class="fa fa-cogs visible-xs"></i>
                     </td>
                    </tr>
                </thead>
                <tbody class="gridbody">
                <?php
                // Display the files
                $alt = $setUp->getConfig('salt');
                $altone = $setUp->getConfig('session_name');
                $hasvideo = false;
                $hasimage = false;
                $hasaudio = false;

                foreach ($encodeExplorer->files as $key => $file) {
                    $thisdir = urldecode($encodeExplorer->location->getDir(false, true, false, 0));
                    $thisfile = $file->getName();
                    $thisname = $file->getNameHtml();
                    $fullsize = $file->getSize();
                    $thislink = base64_encode($encodeExplorer->location->getDir(false, true, false, 0).$file->getNameEncoded());
                    $formatsize = $setUp->formatSize($fullsize);
                    $formattime = $setUp->formatModTime($file->getModTime());
                    $directlinks = $setUp->getConfig('direct_links');
                    $dash = md5($alt.$thislink.$altone.$alt);
                    $ext = pathinfo($thisfile, PATHINFO_EXTENSION);
                    $withoutExt = preg_replace("/\\.[^.\\s]{2,4}$/", "", $thisfile);
                    $del = $location->getDir(false, true, false, 0).$file->getName();
                    $delquery = base64_encode($del);
                    $cash = md5($delquery.$setUp->getConfig('salt').$setUp->getConfig('session_name'));
                    $thisdel = $encodeExplorer->makeLink(false, $del, $location->getDir(false, true, false, 0));
                    $imgdata = 'data-ext="'.$ext.'"';

                    if ($setUp->getConfig('enable_prettylinks') == true) {
                        $downlink = 'download/'.$thislink.'/h/'.$dash;
                        $imgdata .= ' data-name="'.$thisname.'" data-link="'.$thislink
                        .'" data-linkencoded="'.$thislink.'/h/'.$dash.'"';
                    } else {
                        $downlink = 'vfm-admin/vfm-downloader.php?q='.$thislink.'&h='.$dash;
                        $imgdata .= ' data-name="'.$thisname.'" data-link="'.$thislink
                        .'" data-linkencoded="'.$thislink.'&h='.$dash.'"';
                    }
                    $thisicon = "fa-file-o";
                    
                    $iconkey = strtolower($file->getType());
                    if (array_key_exists($iconkey, $_IMAGES)) {
                        $thisicon = $_IMAGES[$iconkey];
                    }
                    if ($file->isValidForVideo()) {
                        $hasvideo = true;
                        $thisicon = "fa-video-camera";
                    }
                    $gallclass = "";
                    $gallid = "";

                    if ($file->isValidForThumb()) {
                        $hasimage = true;
                        $gallclass = 'gallindex';
                        $gallid = ' id="gall-'.$key.'"';
                    } ?>
                    <tr class="rowa <?php echo $gallclass; ?>" <?php echo $gallid; ?>>
                        <td class="checkb text-center">
                            <div class="checkbox checkbox-primary checkbox-circle">
                                <label class="round-butt">
                                    <input type="checkbox" name="selecta" class="selecta" value="<?php echo $thislink; ?>">
                                </label>
                            </div>
                        </td>
                        <?php
                        // MP3 inline player link
                        if ($file->isValidForAudio() ) { 
                            $hasaudio = true;
                            ?>
                            <td class="icon text-center playme itemicon">
                            <?php
                            if ($setUp->getConfig('enable_prettylinks') == true) {
                                $linkaudio = "download/".$thislink."/h/".$dash;
                            } else {
                                $linkaudio = "vfm-admin/vfm-downloader.php?q=".$thislink."&h=".$dash;
                            } ?>
                        
                            <a type="audio/<?php echo $ext; ?>" class="sm2_button" href="<?php echo $linkaudio; ?>&audio=play">
                                <div class="icon-placeholder">
                                    <div class="cta">
                                        <i class="trackload fa fa-refresh fa-spin fa-lg"></i>
                                        <i class="trackpause fa fa-play-circle-o fa-lg"></i>
                                        <i class="trackplay fa fa-circle-o-notch fa-spin fa-lg"></i>
                                        <i class="trackstop fa fa-play-circle fa-lg"></i>
                                    </div>
                                </div>
                            <?php
                        } else { ?>
                        <td class="icon text-center itemicon">
                            <a href="<?php echo $downlink; ?>" 
                            <?php
                            if ($file->isValidForThumb() || $file->isValidForVideo()) {
                                echo $imgdata;
                            }
                            if (strtolower($ext) == 'pdf' || $directlinks) {
                                echo ' target="_blank"';
                            } ?> class="item file 
                            <?php
                            if ($file->isValidForVideo()) {
                                echo ' vid';
                            } ?>
                            <?php 
                            if ($file->isValidForThumb() && $setUp->getConfig('thumbnails')) {
                                echo ' thumb vfm-gall';
                            } ?>">
                            <?php
                            // INLINE THUMBNAILS
                            if ($setUp->getConfig('inline_thumbs') == true) {
                                if ($file->isValidForThumb()) { ?>
                                <div class="icon-placeholder">
                                    <img src="<?php echo $imageServer->showThumbnail(base64_decode($thislink), true);?>">
                                </div>
                                <?php
                                } else { ?>
                                <div class="icon-placeholder">
                                    <div class="cta">
                                        <i class="fa <?php echo $thisicon; ?> fa-lg"></i>
                                    </div>
                                </div>
                                <?php
                                }
                            } else { ?>
                                <div class="icon-placeholder">
                                    <div class="cta">
                                        <i class="fa <?php echo $thisicon; ?> fa-lg"></i>
                                    </div>
                                </div>
                            <?php
                            } 
                        } ?>
                                <div class="hover">
                                    <div>
                                        <div class="round-butt">
                                        <?php
                                        if ($file->isValidForThumb()) { ?>
                                            <i class="fa fa-search-plus fa-fw"></i>
                                        <?php
                                        } elseif ($file->isValidForVideo()) { ?>
                                           <i class="fa fa-film fa-fw"></i>
                                        <?php
                                        } elseif (strtolower($ext) == 'pdf') { ?>
                                           <i class="fa fa-angle-right fa-fw"></i>
                                        <?php
                                        } elseif ($file->isValidForAudio()) { ?>
                                           <i class="fa fa-play-circle fa-fw"></i>
                                        <?php
                                        } else { ?>
                                            <i class="fa fa-download fa-fw"></i>
                                        <?php
                                        } ?>
                                        </div><br>
                                        <span class="badge">
                                            <strong>
                                                <?php echo $formatsize; ?>
                                            </strong>
                                        </span>
                                    </div>
                                </div>
                            </a>
                            <div class="infopanel">
                                <?php
                                if ($file->isValidForAudio() 
                                    || $file->isValidForThumb()
                                    || $file->isValidForVideo()
                                ) { ?>
                                <div class="minibutt">
                                    <a class="round-butt" href="<?php echo $downlink; ?>" 
                                        <?php if ($directlinks) echo ' target="_blank"'; ?>>
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                                <?php
                                }
                                if ($gateKeeper->isAllowed('rename_enable') 
                                    && $location->editAllowed()
                                ) { ?>
                                    <div class="icon rename text-center minibutt">
                                        <a class="round-butt" href="javascript:void(0)" data-thisdir="<?php echo $thisdir; ?>" 
                                            data-thisext="<?php echo $ext; ?>" data-thisname="<?php echo $withoutExt; ?>">
                                            <i class="fa fa-pencil-square-o"></i>
                                        </a>
                                    </div>
                                <?php
                                }
                                if ($gateKeeper->isAllowed('delete_enable') 
                                    && $location->editAllowed()
                                ) {
                                    $delquery = base64_encode($del);
                                    $cash = md5($delquery.$setUp->getConfig('salt').$setUp->getConfig('session_name')); ?>
                                    <div class="del text-center minibutt">
                                        <a class="round-butt" data-name="<?php echo $thisfile; ?>" href="<?php echo $thisdel; ?>&h=<?php echo $cash; ?>">
                                            <i class="fa fa-trash-o"></i>
                                        </a>
                                    </div>
                                <?php
                                } ?>
                            </div>
                        </td>

                        <td class="name" data-order="<?php echo $thisname; ?>" data-filter="<?php echo $thisname; ?>">
                            <div class="relative">
                                <a href="<?php echo $downlink; ?>" 
                                    <?php
                                    if ($file->isValidForThumb() || $file->isValidForVideo()) {
                                        echo $imgdata;
                                    }
                                    if (strtolower($ext) == 'pdf' || $directlinks) {
                                        echo ' target="_blank"';
                                    } ?> class="full-lenght item file 
                                    <?php
                                    if ($file->isValidForThumb() && $setUp->getConfig('thumbnails')) {
                                        echo ' thumb vfm-gall';
                                    } ?>
                                    <?php
                                    if ($file->isValidForVideo()) {
                                        echo ' vid';
                                    } ?>
                                    "><?php echo $thisname; ?>
                                </a>
                                <div class="grid-item-title"><?php echo $thisname; ?></div>

                                <?php
                                if ($file->isValidForThumb()) { ?>
                                    <span class="hover"><i class="fa fa-search-plus fa-fw"></i></span>
                                <?php
                                } elseif (strtolower($ext) == 'pdf') { ?>
                                    <span class="hover"><i class="fa fa-angle-right fa-fw"></i></span>
                                <?php
                                } elseif ($file->isValidForVideo()) { ?>
                                    <span class="hover"><i class="fa fa-film fa-fw"></i></span>
                                <?php
                                } else { ?>
                                    <span class="hover"><i class="fa fa-download fa-fw"></i></span>
                                <?php
                                } ?>
                            </div>
                        </td>

                        <td class="mini reduce nowrap hidden-xs" data-order="<?php echo $fullsize; ?>">
                            <span class="text-center">
                                <?php echo $formatsize; ?>
                            </span>
                        </td>

                        <td class="mini reduce hidden-xs nowrap" data-order="<?php echo $file->getModTime(); ?>">
                            <span class="text-center">
                                <?php echo $formattime; ?>
                            </span>
                        </td>
                        
                        <?php
                        if ($location->editAllowed()) {
                            if ($gateKeeper->isAllowed('rename_enable')) { ?>
                            <td class="icon rename text-center hidden-xs">
                                <a class="round-butt butt-mini" href="javascript:void(0)" data-thisdir="<?php echo $thisdir; ?>" 
                                    data-thisext="<?php echo $ext; ?>" data-thisname="<?php echo $withoutExt; ?>">
                                    <i class="fa fa-pencil-square-o"></i>
                                </a>
                            </td>
                            <?php
                            } ?>
                            <td class="text-center">
                            <?php
                            if ($gateKeeper->isAllowed('delete_enable')) { ?>
                                <div class="del hidden-xs">
                                    <a class="round-butt butt-mini" data-name="<?php echo $thisfile; ?>" href="<?php echo $thisdel; ?>&h=<?php echo $cash; ?>">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            <?php
                            } ?>
                                <div class="dropdown visible-xs">
                                    <a class="round-butt butt-mini dropdown-toggle" data-toggle="dropdown" href="#">
                                        <i class="fa fa-cog"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a href="<?php echo $downlink; ?>" 
                                                <?php
                                                if (strtolower($ext) == 'pdf' || $directlinks) {
                                                    echo ' target="_blank"';
                                                } ?>>
                                                <i class="fa fa-cloud-download"></i> 
                                                <?php echo $encodeExplorer->getString("download"); ?>
                                            </a>
                                        </li>
                                    <?php
                                    if ($gateKeeper->isAllowed('rename_enable')) { ?>
                                        <li class="rename">
                                            <a href="javascript:void(0)" 
                                                data-thisdir="<?php echo $thisdir; ?>" 
                                                data-thisname="<?php echo $withoutExt; ?>">
                                                <i class="fa fa-edit"></i> 
                                                <?php echo $encodeExplorer->getString("rename"); ?>
                                            </a>
                                        </li>
                                    <?php
                                    }
                                    if ($gateKeeper->isAllowed('delete_enable')) { ?>
                                        <li class="del">
                                            <a href="<?php echo $thisdel; ?>&h=<?php echo $cash; ?>" data-name="<?php echo $thisfile; ?>">
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
                        } ?>
                        </tr>
                    <?php
                } ?>
                    </tbody>
                </table>
            </form>
        </section>
    <?php
    } else { 
        // end if files, show big icon for empty folders
        ?>
        <section class="vfmblock tableblock text-center lead hidetable">
            <span class="fa-stack fa-4x alpha-light">
                <i class="fa fa-circle-thin fa-stack-2x"></i>
                <?php
                // show upload icon
                if ($gateKeeper->isAllowed('upload_enable')) { 
                    echo '<i class="fa fa-cloud-upload fa-stack-1x"></i>';
                } else {
                    echo '<i class="fa fa-folder-open fa-stack-1x"></i>';
                } ?>
            </span>
        </section>
        <?php
    }
} // end access allowed
