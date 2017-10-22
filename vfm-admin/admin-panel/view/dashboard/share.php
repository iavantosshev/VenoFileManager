<?php
/**
* FILE SHARING
**/
?>
<div id="view-share" class="anchor"></div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-default box-solid">
            <div class="box-header with-border">
                <i class="fa fa-share"></i>  <?php print $encodeExplorer->getString("share_files"); ?>
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>

            <div class="box-body">
                <div class="checkbox toggle checkbox-bigger">
                    <label>
                        <input type="checkbox" name="sendfiles_enable" 
                        <?php
                        if ($setUp->getConfig('sendfiles_enable') == true) {
                            echo "checked";
                        } ?>><i class="fa fa-paper-plane-o"></i> 
                        <?php print $encodeExplorer->getString("share"); ?>
                    </label>
                </div>
                <div class="form-group toggled">
                    <div class="row">
                        <div class="col-sm-6">
                            <label><?php print $encodeExplorer->getString("keep_links"); ?></label>
                            <select class="form-control" name="lifetime">
                            <?php 
                            foreach ($share_lifetime as $key => $value) {
                                echo "<option ";
                                if ($setUp->getConfig('lifetime') == $key) {
                                    echo "selected ";
                                }
                                echo "value=\"".$key."\">".$value."</option>";
                            } ?>
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <div class="checkbox checkbox-big">
                                <label>
                                    <input type="checkbox" name="secure_sharing" 
                                    <?php
                                    if ($setUp->getConfig('secure_sharing') == true) {
                                        echo "checked";
                                    } ?>><i class="fa fa-key"></i> 
                                    <?php print $encodeExplorer->getString("password_protection"); ?>
                                </label>
                            </div>
                            <div class="checkbox checkbox-big">
                                <label>
                                    <input type="checkbox" name="show_captcha_download" 
                                    <?php
                                    if ($setUp->getConfig('show_captcha_download') == true) {
                                        echo "checked";
                                    } ?>><i class="fa fa-shield"></i> 
                                    <?php print $encodeExplorer->getString("enter_captcha"); ?>
                                </label>
                            </div>
                            <div class="checkbox checkbox-big">
                                <label>
                                    <input type="checkbox" name="clipboard" 
                                    <?php
                                    if ($setUp->getConfig('clipboard') == true) {
                                        echo "checked";
                                    } ?>><i class="fa fa-clipboard"></i> 
                                    <?php print $encodeExplorer->getString("copy_to_clipboard"); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div> <!-- toggled -->

                <div class="box-footer">
                    <button type="submit" class="btn btn-default pull-right" 
                    data-toggle="tooltip" data-placement="left"
                    title="<?php print $encodeExplorer->getString("save_settings"); ?>">
                        <i class="fa fa-save"></i>
                    </button>
                </div>
            </div> <!-- box-body -->
        </div> <!-- box -->
    </div> <!-- col -->
</div> <!-- row -->