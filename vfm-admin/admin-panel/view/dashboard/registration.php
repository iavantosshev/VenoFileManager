<?php
/**
 * VFM - veno file manager: admin-panel/view/dashboard/registration.php
 * administration registration block
 *
 * @package VenoFileManager
 */

/**
* ROLE PERMISSIONS
**/
$regdirs = $setUp->getConfig('registration_user_folders');
if ($regdirs) {
    $regdirs = json_decode($regdirs, true);
    foreach ($regdirs as $dir) {
        echo " <input type=\"hidden\" value=\"".$dir."\" class=\"s-reguserfolders\">";
    }
} ?>
<div id="view-registration" class="anchor"></div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-default box-solid">
            <div class="box-header with-border">
                <i class="fa fa-user-plus"></i> <?php print $encodeExplorer->getString('registration'); ?>
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box bg-aqua">
                            <span class="info-box-icon"><i class="fa fa-user-plus"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-number"><?php print $encodeExplorer->getString("registration"); ?></span>
                                <div class="progress"></div>
                                <span class="progress-description">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="registration_enable" class="checkregs" 
                                            <?php
                                            if ($setUp->getConfig('registration_enable')) {
                                                echo "checked";
                                            } ?>> 
                                            <?php print $encodeExplorer->getString("enabled"); ?>
                                        </label>
                                    </div>
                                </span>
                            </div><!-- /.info-box-content -->
                        </div><!-- /.info-box -->   
                    </div>

                    <div class="col-md-4">
                        <label>
                            <?php print $encodeExplorer->getString("role"); ?>
                        </label>
                        <div class="form-group cooldropgroup">
                            <div class="input-group btn-group cooldrop">
                                <span class="input-group-addon">
                                    <i class="fa fa-check fa-fw"></i>
                                </span>
                                <select class="form-control coolselect" name="registration_role">
                                    <option value="user" 
                                    <?php
                                    if ($setUp->getConfig('registration_role') !== "admin") {
                                        echo "selected";
                                    } ?>>user</option>
                                    <option value="admin" 
                                    <?php
                                    if ($setUp->getConfig('registration_role') === "admin") {
                                        echo "selected";
                                    } ?>>admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="checkbox clear">
                            <label>
                                <input type="checkbox" name="show_captcha_register" 
                                <?php
                                if ($setUp->getConfig('show_captcha_register')) {
                                    echo "checked";
                                } ?>> <i class="fa fa-shield fa-fw"></i> 
                                <?php print $encodeExplorer->getString("show_captcha_register"); ?>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group cooldropgroup">
                                    <label>
                                        <?php print $encodeExplorer->getString("user_folder"); ?>
                                    </label>
                                    <div class="input-group btn-group cooldrop">
                                        <span class="input-group-addon">
                                            <i class="fa fa-sitemap fa-fw"></i>
                                        </span>
                                        <select name="reguserfolders[]" id="r-reguserfolders" class="form-control assignfolder" multiple="multiple">
                                        <option value="vfm_reg_new_folder"><?php echo $encodeExplorer->getString("new_username_folder"); ?></option>
                                        <?php
                                        foreach ($setUp->getFolders() as $folder) {
                                            echo '<option value="'.$folder.'">'.$folder.'</option>';
                                        } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group userquota cooldropgroup">
                                    <label><?php print $encodeExplorer->getString("available_space"); ?></label>
                                    <div class="input-group btn-group cooldrop">
                                        <span class="input-group-addon">
                                            <i class="fa fa-tachometer fa-fw"></i>
                                        </span>
                                        <select class="form-control coolselect" name="regquota">
                                            <option value=""><?php print $encodeExplorer->getString("unlimited"); ?></option>
                                            <?php
                                            foreach ($_QUOTA as $value) {
                                                echo '<option value="'.$value.'"';
                                                if ($setUp->getConfig('registration_user_quota') == $value) {
                                                    echo ' selected';
                                                }
                                                echo '>'.$value.'MB</option>';
                                            } ?>
                                        </select>
                                    </div> <!-- input-group -->
                                </div> <!-- userquota -->
                            </div> <!-- col-sm-12 -->                 
                        </div> <!-- row -->
                    </div> <!-- col-md-4 -->
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
    </div>
</div>