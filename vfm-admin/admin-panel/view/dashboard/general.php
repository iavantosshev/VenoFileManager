<?php
/**
* GENERAL SETTINGS
**/

/**
* Timezones list with GMT offset
*
* @return array
*/
function tzList() 
{
    $zones_array = array();
    $timestamp = time();
    foreach (timezone_identifiers_list() as $key => $zone) {
        date_default_timezone_set($zone);
        $zones_array[$key]['zone'] = $zone;
        $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
    }
    return $zones_array;
} ?>
<div class="anchor" id="view-general"></div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-default box-solid">

            <div class="box-header with-border">
                <i class="fa fa-cog"></i> <?php print $encodeExplorer->getString("general_settings"); ?>
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label><?php print $encodeExplorer->getString("app_name"); ?></label>
                            <input type="text" class="form-control" 
                            value="<?php print $setUp->getConfig('appname'); ?>" name="appname">
                        </div>

                        <div class="form-group">
                            <label><?php print $encodeExplorer->getString("description"); ?></label>
                            <textarea class="form-control summernote" name="description"><?php print $setUp->getConfig('description'); ?></textarea>
                        </div>
 
                        <div class="checkbox checkbox-bigger clear">
                            <label>
                                <input type="checkbox" name="require_login" 
                                <?php
                                if ($setUp->getConfig('require_login')) {
                                    echo "checked";
                                } ?>><i class="fa fa-lock fa-fw"></i> 
                                <?php print $encodeExplorer->getString("require_login"); ?>
                            </label>
                        </div>

                        <div class="checkbox clear">
                            <label>
                                <input type="checkbox" name="show_captcha" 
                                <?php
                                if ($setUp->getConfig('show_captcha')) {
                                    echo "checked";
                                } ?>> <i class="fa fa-shield fa-fw"></i> <i class="fa fa-sign-in fa-fw"></i>  

                                <?php print $encodeExplorer->getString("show_captcha"); ?>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="show_captcha_reset" 
                                <?php
                                if ($setUp->getConfig('show_captcha_reset')) {
                                    echo "checked";
                                } ?>> <i class="fa fa-shield fa-fw"></i> <i class="fa fa-key fa-flip-horizontal fa-fw"></i> 
                                <?php print $encodeExplorer->getString("show_captcha_reset"); ?>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="show_usermenu" 
                                <?php
                                if ($setUp->getConfig('show_usermenu')) {
                                    echo "checked";
                                } ?>><i class="fa fa-user fa-fw"></i> 
                                <?php print $encodeExplorer->getString("show_usermenu"); ?>
                            </label>
                        </div>
                        <div class="checkbox toggle">
                            <label>
                                <input type="checkbox" name="show_langmenu" 
                                <?php
                                if ($setUp->getConfig('show_langmenu')) {
                                    echo "checked";
                                } ?>><i class="fa fa-flag fa-fw"></i> 
                                <?php print $encodeExplorer->getString("show_langmenu"); ?>
                            </label>
                        </div>

                        <div class="checkbox toggled">
                            <label>
                                <input type="checkbox" name="show_langname" 
                                <?php
                                if ($setUp->getConfig('show_langname')) {
                                    echo "checked";
                                } ?>><i class="fa fa-commenting-o fa-fw"></i> 
                                <?php print $encodeExplorer->getString("show_current_language"); ?>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="show_path" 
                                <?php
                                if ($setUp->getConfig('show_path')) {
                                    echo "checked";
                                } ?>><i class="fa fa-ellipsis-h fa-fw"></i> 
                                <?php print $encodeExplorer->getString("display_breadcrumbs"); ?>
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="upload_notification_enable" 
                                <?php
                                if ($setUp->getConfig('upload_notification_enable')) {
                                    echo "checked";
                                } ?>><i class="fa fa-envelope-o fa-fw"></i> 
                                <?php print $encodeExplorer->getString("can_notify_uploads"); ?>
                            </label>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>
                                    <i class="fa fa-file-archive-o fa-fw" aria-hidden="true"></i> <?php print $encodeExplorer->getString("zip_multiple_files"); ?>
                                </label>
                            </div>
                            <div class="col-xs-6 col-sm-12 col-lg-6">
                                <label>
                                    <?php print $encodeExplorer->getString("max_files"); ?>
                                </label>
                                <input type="number" class="form-control" name="max_zip_files" value="<?php echo $setUp->getConfig('max_zip_files'); ?>">
                            </div>

                            <div class="col-xs-6 col-sm-12 col-lg-6 form-group">
                                <label><?php print $encodeExplorer->getString("max_filesize"); ?></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="max_zip_filesize" value="<?php echo $setUp->getConfig('max_zip_filesize'); ?>">
                                    <span class="input-group-addon">MB</span>
                                </div> 
                            </div>
                        </div>
                    </div> <!-- col 6 -->

                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><?php print $encodeExplorer->getString("application_url"); ?></label>
                                    <input type="text" class="form-control" placeholder="http://.../" 
                                    value="<?php print $setUp->getConfig('script_url'); ?>" name="script_url">
                                </div>
                            </div>

                            <div class="col-xs-6 col-sm-12 col-lg-4">
                                <label>
                                    <?php print $encodeExplorer->getString("default_lang"); ?>
                                </label>
                                <select class="form-control" name="lang">
                                    <?php
                                    foreach ($translations as $key => $lingua) { ?>
                                        <option value="<?php echo $key; ?>" 
                                        <?php
                                        if ($key == $setUp->getConfig('lang')) {
                                            echo "selected";
                                        } 
                                        ?>>
                                    <?php echo $lingua; ?></option>
                                <?php
                                    } ?>
                                </select>
                            </div>

                            <div class="col-xs-6 col-sm-12 col-lg-4">
                                <label><?php print $encodeExplorer->getString("direction"); ?></label>
                                <select class="form-control" name="txt_direction">
                                    <option value="LTR" 
                                    <?php
                                    if ($setUp->getConfig('txt_direction') == "LTR") {
                                                echo "selected";
                                    } ?> >Left to Right</option>
                                    <option value="RTL" 
                                    <?php
                                    if ($setUp->getConfig('txt_direction') == "RTL") {
                                                echo "selected";
                                    } ?> >Right to Left</option>
                                </select>
                            </div>

                            <div class="col-sm-12 col-lg-4">
                                <label><?php print $encodeExplorer->getString("time_format"); ?></label>
                                <select class="form-control" name="time_format">
                                    <option 
                                    <?php
                                    if ($setUp->getConfig('time_format') == "d/m/Y - H:i") {
                                                echo "selected";
                                    } ?> >d/m/Y</option>
                                    <option 
                                    <?php
                                    if ($setUp->getConfig('time_format') == "m/d/Y - H:i") {
                                                echo "selected";
                                    } ?> >m/d/Y</option>
                                    <option 
                                    <?php
                                    if ($setUp->getConfig('time_format') == "Y/m/d - H:i") {
                                                echo "selected";
                                    } ?> >Y/m/d</option>
                                </select>
                            </div>
                        </div><!-- row -->
                    </div> <!-- col-sm-6 -->

                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                            if (strlen($setUp->getConfig('default_timezone')) < 3 ) {
                                    $thistime = "UTC";
                            } else {
                                    $thistime = $setUp->getConfig('default_timezone');
                            } ?>
                            <label><?php print $encodeExplorer->getString("default_timezone"); ?></label>
                            <select class="form-control" name="default_timezone">
                            <?php 
                            foreach (tzList() as $tim) { 
                                print "<option value=\"".$tim['zone']."\" ";
                                if ($tim['zone'] == $thistime) {
                                    print "selected";
                                }
                                print ">".$tim['diff_from_GMT'] . ' - ' . $tim['zone']."</option>";
                            } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-folder-open"></i> 
                                <?php print $encodeExplorer->getString("uploads_dir"); ?>
                            </label>
                            <?php
                            $cleandir = substr($setUp->getConfig('starting_dir'), 2);
                            $cleandir = substr_replace($cleandir, "", -1); ?>
                            <input type="text" class="form-control blockme" 
                            name="starting_dir" value="<?php echo $cleandir; ?>">
                        </div>

                        <div class="radio toggle-extensions clear">
                            <label>
                                <input type="radio" name="selectivext" class="togglext" value="reject"
                                <?php if ($setUp->getConfig('selectivext') == "reject") echo " checked"; ?>>
                                <span class="togglabel"><?php print $encodeExplorer->getString("rejected_ext"); ?></span>
                            </label>
                        </div>

                        <div class="form-group">
                            <?php 
                            $upload_reject_extension = $setUp->getConfig('upload_reject_extension');
                            $rejectlist = $upload_reject_extension ? implode(",", $upload_reject_extension) : false; ?>
                            <input type="text" class="form-control" name="upload_reject_extension" 
                            value="<?php echo $rejectlist; ?>" placeholder="php,html,htm">
                        </div>

                        <div class="radio toggle-extensions clear">
                            <label>
                                <input type="radio" name="selectivext" class="togglext" value="allow" 
                                <?php if ($setUp->getConfig('selectivext') == "allow") echo " checked"; ?>>
                                <span class="togglabel"><?php print $encodeExplorer->getString("allowed_ext"); ?></span>
                            </label>
                        </div>
                        <div class="form-group">
                            <?php 
                            $upload_allow_type = $setUp->getConfig('upload_allow_type');
                            $allowlist = $upload_allow_type ? implode(",", $upload_allow_type) : false; ?>
                            <input type="text" class="form-control" name="upload_allow_type" 
                            value="<?php echo $allowlist; ?>" placeholder="jpg,jpeg,gif,png">
                        </div>

                        <div class="toggled">
                            <div class="checkbox checkbox-big clear">
                                <label>
                                    <input type="checkbox" name="enable_prettylinks" id="disable-prettylinks" 
                                    <?php
                                    if ($setUp->getConfig('enable_prettylinks')) {
                                            echo "checked";
                                    } ?>>/ 
                                    <?php print $encodeExplorer->getString("prettylinks"); ?> 
                                </label>
                            </div>
                            <div class="row">
                                <div class="form-group col-xs-12">
                                    <?php print $encodeExplorer->getString("prettylink_old"); ?>:<br>
                                    <code>/vfm-admin/vfm-downloader.php?q=xxx</code><br>
                                    <?php print $encodeExplorer->getString("prettylink"); ?>:<br>
                                    <code>/download/xxx</code>
                                </div>
                            </div>
                        </div>
                        <div class="checkbox clear">
                            <label>
                                <input type="checkbox" name="direct_links" 
                                <?php
                                if ($setUp->getConfig('direct_links')) {
                                    echo "checked";
                                } ?>><i class="fa fa-eye fa-fw"></i><i class="fa fa-long-arrow-right fa-fw"></i><i class="fa fa-files-o fa-fw"></i> 
                                <?php print $encodeExplorer->getString("direct_links"); ?>
                            </label>
                        </div>
                    </div> <!-- col-sm-6 -->
                </div> <!-- row -->

                <div class="box-footer">
                    <button type="submit" class="btn btn-default pull-right" 
                    data-toggle="tooltip" data-placement="left"
                    title="<?php print $encodeExplorer->getString("save_settings"); ?>">
                        <i class="fa fa-save"></i>
                    </button>
                </div>
            </div> <!-- box-body -->
        </div> <!-- box -->
    </div> <!-- col-12 -->
</div> <!-- row -->
