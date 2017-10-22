<?php
/**
 * VFM - veno file manager: include/modals.php
 * popup windows
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
* Group Actions
*/
if ($gateKeeper->isAccessAllowed()) {

    $time = time();
    $hash = md5($_CONFIG['salt'].$time);
    $doit = ($time * 12);
    $pulito = rtrim($setUp->getConfig("script_url"), "/");

    $insert4 = $encodeExplorer->getString('insert_4_chars');

    if ($setUp->getConfig("show_pagination_num") == true 
        || $setUp->getConfig("show_pagination") == true 
        || $setUp->getConfig("show_search") == true
    ) {
        $activepagination = true;
    } else {
        $activepagination = 0;
    }
    $selectfiles = $encodeExplorer->getString("select_files");
    $toomanyfiles = $encodeExplorer->getString('too_many_files');

    $maxzipfiles = $setUp->getConfig('max_zip_files');
    $prettylinks = ($setUp->getConfig('enable_prettylinks') ? true : 0);
    ?>
    <script type="text/javascript">
        createShareLink(
            <?php echo json_encode($insert4); ?>, 
            <?php echo json_encode($time); ?>, 
            <?php echo json_encode($hash); ?>, 
            <?php echo json_encode($pulito); ?>, 
            <?php echo json_encode($activepagination); ?>,
            <?php echo json_encode($maxzipfiles); ?>,
            <?php echo json_encode($selectfiles); ?>, 
            <?php echo json_encode($toomanyfiles); ?>,
            <?php echo json_encode($prettylinks); ?>
        );
    </script>
    <div class="modal fade downloadmulti" id="downloadmulti" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                    </button>
                    <p class="modal-title">
                        <?php echo " " .$encodeExplorer->getString('selected_files'); ?>: 
                        <span class="numfiles badge badge-danger"></span>
                    </p>
                </div>
                <div class="text-center modal-body">
                    <a class="btn btn-primary btn-lg centertext bigd sendlink" href="#">
                        <i class="fa fa-cloud-download fa-5x"></i>
                    </a>
                </div>
            </div>
         </div>
    </div>
    <?php
    /**
    * Send files window
    */
    if ($setUp->getConfig('sendfiles_enable')) { ?>
            <div class="modal fade sendfiles" id="sendfilesmodal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                            </button>
                            <h5 class="modal-title">
                                <?php echo " " .$encodeExplorer->getString("selected_files"); ?>: 
                                <span class="numfiles badge badge-danger"></span>
                            </h5>
                        </div>

                        <div class="modal-body">
                            <div class="form-group createlink-wrap">
                                <button id="createlink" class="btn btn-primary btn-block"><i class="fa fa-check"></i> 
                                    <?php echo $encodeExplorer->getString("generate_link"); ?></button>
                            </div>
        <?php
        if ($setUp->getConfig('secure_sharing')) { ?>
                            <div class="checkbox">
                                <label>
                                    <input id="use_pass" name="use_pass" type="checkbox"><i class="fa fa-key"></i> 
                                    <?php echo $encodeExplorer->getString("password_protection"); ?>
                                </label>
                            </div>
        <?php
        } ?>
                        <div class="form-group shalink">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <a class="btn btn-primary sharebutt" href="#" target="_blank">
                                        <i class="fa fa-link fa-fw"></i>
                                    </a>
                                </span>
                                <input id="copylink" class="sharelink form-control" type="text" onclick="this.select()" readonly>
        <?php
        if ($setUp->getConfig('clipboard')) { ?>
                                <script src="vfm-admin/js/clipboard.min.js"></script>
                                <span class="input-group-btn">
                                    <button id="clipme" class="clipme btn btn-primary" 
                                    data-toggle="popover" data-placement="bottom" 
                                    data-content="<?php echo $encodeExplorer->getString("copied"); ?>" 
                                    data-clipboard-target="#copylink">
                                        <i class="fa fa-clipboard fa-fw"></i>
                                    </button>
                                </span>
        <?php
        } ?>
                            </div>
                        </div>
        <?php
        if ($setUp->getConfig('secure_sharing')) { ?>
                            <div class="form-group seclink">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock fa-fw"></i></span>
                                    <input class="form-control passlink setpass" type="text" onclick="this.select()" 
                                    placeholder="<?php echo $encodeExplorer->getString("random_password"); ?>">
                                </div>
                            </div>
        <?php
        } 
        $mailsystem = $setUp->getConfig('email_from');
        if (strlen($mailsystem) > 0) { ?>
                            <div class="openmail">
                                <span class="fa-stack fa-lg">
                                  <i class="fa fa-circle-thin fa-stack-2x"></i>
                                  <i class="fa fa-envelope fa-stack-1x"></i>
                                </span>
                            </div>
                            <form role="form" id="sendfiles">
                                <div class="mailresponse"></div>
                                
                                <input name="thislang" type="hidden" 
                                value="<?php echo $encodeExplorer->lang; ?>">

                                <label for="mitt">
                                    <?php echo $encodeExplorer->getString("from"); ?>:
                                </label>

                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                                    <input name="mitt" type="email" class="form-control" id="mitt" 
                                    value="<?php echo $gateKeeper->getUserInfo('email'); ?>" 
                                     placeholder="<?php echo $encodeExplorer->getString("your_email"); ?>" required >
                                </div>
                            
                                <div class="wrap-dest">
                                    <div class="form-group">
                                        <label for="dest">
                                            <?php echo $encodeExplorer->getString("send_to"); ?>:
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-envelope fa-fw"></i></span>
                                            <input name="dest" type="email" data-role="multiemail" class="form-control addest" id="dest" 
                                            placeholder="" required >
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group clear">
                                    <div class="btn btn-primary btn-xs shownext">
                                        <i class="fa fa-plus"></i> <i class="fa fa-user"></i>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <textarea class="form-control" name="message" id="mess" rows="3" 
                                    placeholder="<?php echo $encodeExplorer->getString("message"); ?>"></textarea>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fa fa-envelope"></i>
                                    </button>
                                </div>

                                <input name="passlink" class="form-control passlink" type="hidden">
                                <input name="attach" class="attach" type="hidden">
                                <input name="sharelink" class="sharelink" type="hidden">
                            </form>
                            
                            <div class="mailpreload">
                                <div class="cta">
                                    <i class="fa fa-refresh fa-spin"></i>
                                </div>
                            </div>
        <?php
        } ?>
                        </div> <!-- modal-body -->
                    </div>
                </div>
            </div>
        <?php
    } // end sendfiles enabled

    /**
    * Rename files and folders
    */
    if ($gateKeeper->isAllowed('rename_enable')) { ?>

        <div class="modal fade changename" id="modalchangename" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title"><i class="fa fa-edit"></i> <?php echo $encodeExplorer->getString("rename"); ?></h4>
                    </div>

                    <div class="modal-body">
                        <form role="form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);?>">
                            <input readonly name="thisdir" type="hidden" 
                            class="form-control" id="dir">
                            <input readonly name="thisext" type="hidden"
                            class="form-control" id="ext">
                            <input readonly name="oldname" type="hidden" 
                            class="form-control" id="oldname">

                            <div class="input-group">
                                <label for="newname" class="sr-only">
                                    <?php echo $encodeExplorer->getString("rename"); ?>
                                </label>
                                <input name="newname" type="text" 
                                class="form-control" id="newname">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-primary">
                                        <?php echo $encodeExplorer->getString("rename"); ?>
                                    </button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } // end rename_enable

    /**
    * Move files
    */
    if ($gateKeeper->isAllowed('move_enable') || $gateKeeper->isAllowed('copy_enable')) { 
        ?>
        <script type="text/javascript">
        setupMove(
            <?php echo json_encode($activepagination); ?>,
            <?php echo json_encode($selectfiles); ?>,
            <?php echo json_encode($time); ?>, 
            <?php echo json_encode($hash); ?>, 
            <?php echo json_encode($doit); ?>
        );
        </script>

        <div class="modal fade movefiles" id="movemulti" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title">
                            <i class="fa fa-list"></i> 
                            <?php echo $encodeExplorer->getString("select_destination_folder"); ?>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="hiddenalert"></div>
                        <?php
                        if (isset($_GET['dir']) && strlen($_GET['dir']) > 0) {
                            $currentdir = "./".trim($_GET['dir'], "/")."/";
                        } else {
                            $currentdir = $setUp->getConfig('starting_dir');
                        }
                        // check if any folder is assigned to the current user
                        if ($gateKeeper->getUserInfo('dir') !== null) {
                            $userpatharray = array();
                            $userpatharray = json_decode(GateKeeper::getUserInfo('dir'), true);

                            // show all available directories trees
                            foreach ($userpatharray as $userdir) {
                                $path = $setUp->getConfig('starting_dir').$userdir.'/'; ?>
                            <ul class="foldertree">
                                <li class="folderoot">
                                <?php
                                if ($path === $currentdir) { ?>
                                    <i class="fa fa-folder-open"></i> <?php echo $userdir ?>
                                <?php
                                } else { ?>
                                    <a href="#" data-dest="<?php echo urlencode($path); ?>" class="movelink">
                                        <i class="fa fa-folder"></i> <?php echo $userdir; ?>
                                    </a>
                                <?php
                                }
                                Actions::walkDir($path, $currentdir);
                                ?>
                                </li>
                            </ul>
                            <?php
                            }
                        } else {
                            // no directory assigned, access to all folders
                            $movedir = $setUp->getConfig('starting_dir');
                            $cleandir = substr(
                                $setUp->getConfig('starting_dir'), 2
                            );
                            $cleandir = substr_replace($cleandir, '', -1); ?>
            
                            <ul class="foldertree">
                                <li class="folderoot">
                            <?php
                            if ($movedir === $currentdir) { ?>
                                    <i class="fa fa-folder-open"></i> <?php echo $cleandir; ?>
                            <?php
                            } else { ?>
                                    <a href="#" data-dest="<?php echo urlencode($movedir); ?>" class="movelink">
                                        <i class="fa fa-folder"></i> <?php echo $cleandir; ?>
                                    </a>
                            <?php
                            }
                            Actions::walkDir($movedir, $currentdir); ?>
                                </li>
                            </ul>
                        <?php
                        } ?>
                        <form id="moveform">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } // end move_enable

    /**
    * Delete multiple files
    */
    if ($gateKeeper->isAllowed('delete_enable')) { 
        $confirmthisdel = $encodeExplorer->getString('delete_this_confirm');
        $confirmdel = $encodeExplorer->getString('delete_confirm'); ?>
        <script type="text/javascript">
            setupDelete(
                <?php echo json_encode($confirmthisdel); ?>, 
                <?php echo json_encode($confirmdel); ?>, 
                <?php echo json_encode($activepagination); ?>, 
                <?php echo json_encode($time); ?>, 
                <?php echo json_encode($hash); ?>, 
                <?php echo json_encode($doit); ?>, 
                <?php echo json_encode($selectfiles); ?>
            );
        </script>
        <div class="modal fade deletemulti" id="deletemulti" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                        </button>
                        <p class="modal-title"> 
                            <?php echo $encodeExplorer->getString("selected_files"); ?>: 
                            <span class="numfiles badge badge-danger"></span>
                        </p>
                    </div>
                    <div class="text-center modal-body">
                        <form id="delform">
                            <a class="btn btn-primary btn-lg centertext bigd removelink" href="#">
                            <i class="fa fa-trash-o fa-5x"></i></a>
                            <p class="delresp"></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php  
    } // end delete enabled
} // end isAccessAllowed

/**
* Show Thumbnails
*/
if (($setUp->getConfig("thumbnails") == true && $hasimage == true) 
    || ($setUp->getConfig("playvideo") == true && $hasvideo == true)
) { ?>
<script type="text/javascript">
    var script_url = <?php echo json_encode($setUp->getConfig('script_url')); ?>;
    <?php 
    if ($setUp->getConfig('enable_prettylinks') == true) { ?>
    var baselink = "download/";
    <?php 
    } else { ?>
    var baselink = "vfm-admin/vfm-downloader.php?q=";
    <?php 
    } ?>
</script>
    <div class="modal fade zoomview" id="zoomview" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                    </button>
                    <div class="modal-title">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <a class="vfmlink btn btn-primary" href="#">
                                    <i class="fa fa-download fa-lg"></i> 
                                </a> 
                            </span>
                            <input type="text" class="thumbtitle form-control" value="" onclick="this.select()" readonly >
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="vfm-zoom"></div>
                    <!--            
                     <div style="position:absolute; right:10px; bottom:10px;">Custom Watermark</div>
                    -->                
                </div>
            </div>
        </div>
    </div>
    <?php    
    /**
    * Load video preview 
    */ 
    if ($setUp->getConfig('playvideo') == true && $hasvideo == true) : ?>

    <link href="vfm-admin/js/videojs/video-js.min.css" rel="stylesheet">
    <script src="vfm-admin/js/videojs/video.min.js"></script>
    <script type="text/javascript">
    function loadVid(thislink, thislinkencoded, thisname, thisID, ext){
        if (ext == 'ogv') {
            ext = 'ogg';
        }
        var vidlink = 'vfm-admin/ajax/streamvid.php?vid=' + thislink;
        var playerhtml = '<video id="my-video" class="video-js vjs-16-9" >' + '<source src="'+ vidlink +'" type="video/'+ ext +'">';
        $(".vfm-zoom").html(playerhtml);
        videojs('#my-video', { 
            "controls": true, 
            "autoplay": true, 
            "preload": "auto"
        }, function(){
            $('#zoomview').on('hidden.bs.modal', function (e) {
                if ( $( "#my-video" ).length ) {
                    videojs('#my-video').dispose();
                }
            });
        });
        $("#zoomview .thumbtitle").val(thisname);
        $("#zoomview").data('id', thisID);
        $("#zoomview").modal();
        $(".vfmlink").attr("href", baselink + thislinkencoded);
        <?php 
        if ($setUp->getConfig('direct_links') == true) { ?>
            $("#zoomview .thumbtitle").val(script_url + thislink);
            $(".vfmlink").attr('target','_blank');
        <?php 
        } ?>
    }
    </script>
    <?php 
    endif;

    /**
    * Load image preview 
    */ 
    if ($setUp->getConfig('thumbnails') == true && $hasimage == true) : ?>

    <script type="text/javascript">
    function loadImg(thislink, thislinkencoded, thisname, thisID){
        $(".vfm-zoom").html("<i class=\"fa fa-refresh fa-spin\"></i><img class=\"preimg\" src=\"vfm-thumb.php?thumb="+ thislink +"&y=1\" \/>");
        $("#zoomview").data('id', thisID);
        $("#zoomview .thumbtitle").val(thisname);
        var firstImg = $('.preimg');
        firstImg.css('display','none');
        $("#zoomview").modal();

        firstImg.one('load', function() {
            $(".vfm-zoom .fa-refresh").fadeOut();
            $(this).fadeIn();
            checkNextPrev(thisID);
            $(".vfmlink").attr("href", baselink + thislinkencoded);
            <?php 
            if ($setUp->getConfig('direct_links') == true) { ?>
                $(".vfmlink").attr('target','_blank');
                $("#zoomview .thumbtitle").val(script_url + thislink);
            <?php 
            } ?>
        }).each(function() {
            if(this.complete) {
                $(this).load();
            }
        });   
    }
    </script>
    <?php
    endif;
} // end thumbnails || video
