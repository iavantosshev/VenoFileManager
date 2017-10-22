<?php
/**
 * VFM - veno file manager: include/load-js.php
 * Load javascript files
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
?> 
<script type="text/javascript" src="vfm-admin/js/bootstrap.min.js"></script>
<script type="text/javascript" src="vfm-admin/js/app.min.js"></script>
<script type="text/javascript">
    // confirm
    bootbox.addLocale('vfm', 
    {
        OK : '<?php echo $encodeExplorer->getString("OK"); ?>',
        CANCEL : '<?php echo $encodeExplorer->getString("CANCEL"); ?>',
        CONFIRM : '<?php echo $encodeExplorer->getString("CONFIRM"); ?>'
    });
    bootbox.setLocale('vfm');
</script>

<?php
/**
* Call JS
*/ 
if ($gateKeeper->isAccessAllowed()) : ?>
    <script src="vfm-admin/js/datatables.min.js"></script>
    <?php
    /**
    * ********************
    *  Init Folder datatables
    * ********************
    */ 
    $dirpaginate = 'off';
    if ($setUp->getConfig("show_pagination_folders") == true) { 
        $dirpaginate = 'on';
    }
    if ($setUp->getConfig('folderdeforder') == "date") { 
        $tbSortcol = 2;
        $tbSortdir = 'desc';
    } else { 
        $tbSortcol = 1;
        $tbSortdir = 'asc';
    } 
    if ($setUp->getConfig("show_pagination_num_folder") == true) { 
        $sPaginationTypeF = 'full_numbers';
    } else {
        $sPaginationTypeF = 'simple';
    }
    $iDisplayLengthF = $setUp->getConfig('folderdefnum'); ?>

    <script type="text/javascript">
    $(document).ready(function() {
        callFoldersTable(
            <?php echo json_encode($sPaginationTypeF); ?>, 
            <?php echo json_encode($iDisplayLengthF); ?>, 
            <?php echo json_encode($tbSortcol); ?>, 
            <?php echo json_encode($tbSortdir); ?>,
            <?php echo json_encode($dirpaginate); ?>
        );
        // zip folders
        callBindZip('<?php echo $encodeExplorer->getString("confirm_folder_download"); ?>');
    });
    </script>
    <?php
    /**
    * ********************
    * Init File datatables
    * ********************
    */
    if ($setUp->getConfig("show_pagination_num") == true 
        || $setUp->getConfig("show_pagination") == true 
        || $setUp->getConfig("show_search") == true
    ) { 

        if ($setUp->getConfig("show_pagination_num") == true) { 
            $sPaginationType = 'full_numbers';
        } else {
            $sPaginationType = 'simple';
        }
        
        $bPaginate = ($setUp->getConfig("show_pagination") ? true : 0);
        $bFilter = ($setUp->getConfig("show_search") ? true : 0);
        $iDisplayLength = $setUp->getConfig('filedefnum');
        $iDisplayLength = isset($_SESSION['ilenght']) ? $_SESSION['ilenght'] : $setUp->getConfig('filedefnum');

        // list by name
        if ($setUp->getConfig('filedeforder') == "alpha") { 
            $fnSortcol = 2;
            $fnSortdir = 'asc';
            // list by size
        } elseif ($setUp->getConfig('filedeforder') == "size") { 
            $fnSortcol = 3;
            $fnSortdir = 'asc';
            // list by creation date
        } else { 
            $fnSortcol = 4;
            $fnSortdir = 'desc';
        } ?>

    <script type="text/javascript">
    $(document).ready(function() {
        callFilesTable(
            <?php echo json_encode($sPaginationType); ?>,
            <?php echo json_encode($bPaginate); ?>,
            <?php echo json_encode($bFilter); ?>,
            <?php echo json_encode($iDisplayLength); ?>,
            <?php echo json_encode($fnSortcol); ?>,
            <?php echo json_encode($fnSortdir); ?>
        );
    });
    </script>
    <?php
    } // end init file datatable

    /**
    * ********************
    * Init soundmanager
    * ********************
    */
    if ($setUp->getConfig("playmusic") == true && $hasaudio == true) { ?>
        <a type="audio/mp3" class="sm2_button hidden" href="#"></a>
        <script src="vfm-admin/js/soundmanager2.min.js"></script>
    <?php 
    }
endif; ?>