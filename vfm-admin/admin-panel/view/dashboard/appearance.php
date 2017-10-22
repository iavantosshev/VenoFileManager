<?php
/**
 * ASPECT
 *
 * @package    VenoFileManager
 * @subpackage Administration
 */
?>
<div id="view-appearance" class="anchor"></div>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-default box-solid">
            <div class="box-header with-border">
                <i class="fa fa-paint-brush"></i> <?php print $encodeExplorer->getString("appearance"); ?>
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label><?php print $encodeExplorer->getString("skin"); ?></label>
                            <select class="form-control skinswitch" name="skin">
                            <?php
                            $skinselected = basename($setUp->getConfig('skin'), '.css');
                            $colorbarselected = $setUp->getConfig('progress_color') ? $setUp->getConfig('progress_color') : $skinselected;

                            $skins = glob('skins/*.css');
                            foreach ($skins as $skin) { 
                                $fileskin = basename($skin);
                                $skinname = basename($skin, '.css');
                                ?>
                                <option 
                                <?php
                                if ($setUp->getConfig('skin') == $fileskin) {
                                    echo 'selected ';
                                } 
                                ?> 
                                value="<?php echo $fileskin; ?>" data-color="<?php echo $skinname; ?>">
                                    <?php echo $skinname; ?>
                                </option>
                            <?php
                            } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?php print $encodeExplorer->getString("upload_progress"); ?></label>
                            <div class="radio pro">
                                <label>
                                    <input type="radio" name="progressColor" value="" data-color="<?php echo $skinselected; ?>" class="first-progress" 
                                    <?php
                                    if ($colorbarselected == $skinselected) {
                                        echo "checked";
                                    } ?>>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar <?php echo $skinselected; ?>" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
                                            <p class="pull-left propercent">45%</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="radio pro">
                                <label>
                                    <input type="radio" name="progressColor" value="progress-bar-info" data-color="progress-bar-info" 
                                    <?php
                                    if ($colorbarselected == "progress-bar-info") {
                                        echo "checked";
                                    } ?>>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100" style="width: 65%">
                                            <p class="pull-left propercent">65%</p>
                                        </div>
                                    </div>
                              </label>
                            </div>
                            <div class="radio pro">
                                <label>
                                    <input type="radio" name="progressColor" value="progress-bar-success" data-color="progress-bar-success" 
                                    <?php
                                    if ($colorbarselected == "progress-bar-success") {
                                        echo "checked";
                                    } ?>>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100" style="width: 35%">
                                            <p class="pull-left propercent">35%</p>
                                        </div>
                                    </div>
                              </label>
                            </div>
                            <div class="radio pro">
                                <label>
                                    <input type="radio" name="progressColor" value="progress-bar-warning" data-color="progress-bar-warning" 
                                    <?php
                                    if ($colorbarselected == "progress-bar-warning") {
                                        echo "checked";
                                    } ?>>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 85%">
                                            <p class="pull-left propercent">85%</p>
                                        </div>
                                    </div>
                              </label>
                            </div>
                            <div class="radio pro">
                                <label>
                                    <input type="radio" name="progressColor" value="progress-bar-danger" data-color="progress-bar-danger" 
                                    <?php
                                    if ($colorbarselected == "progress-bar-danger") {
                                        echo "checked";
                                    } ?>>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%">
                                            <p class="pull-left propercent">75%</p>
                                        </div>
                                    </div>
                              </label>
                            </div>

                            <div class="checkbox clear intero">
                                <label>
                                    <input type="checkbox" name="show_percentage" id="percent" 
                                    <?php
                                    if ($setUp->getConfig('show_percentage')) {
                                            echo "checked";
                                    } ?>>
                                    <?php print $encodeExplorer->getString("show_percentage"); ?> %
                                </label>
                            </div>

                            <div class="checkbox clear intero">
                                <label>
                                    <input type="checkbox" name="single_progress" id="single-progress" 
                                    <?php
                                    if ($setUp->getConfig('single_progress')) {
                                            echo "checked";
                                    } ?>>
                                    <div class="progress progress-single">
                                        <div class="progress-bar <?php echo $colorbarselected; ?>" role="progressbar" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100" style="width: 65%">
                                            <p class="pull-left propercent"><?php print $encodeExplorer->getString("single_progress"); ?></p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div><!-- col6 -->
                    <?php
                    $logoAlignment = $setUp->getConfig("align_logo");
                    switch ($logoAlignment) {
                    case "left":
                        $placealign = "text-left";
                        break;
                    case "center":
                        $placealign = "text-center";
                        break;
                    case "right":
                        $placealign = "text-right";
                        break;
                    default:
                        $placealign = "text-left";
                    } ?>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="checkbox clear toggle">
                                        <label>
                                            <input type="checkbox" name="sticky_alerts" 
                                            <?php
                                            if ($setUp->getConfig('sticky_alerts')) {
                                                echo "checked";
                                            } ?>><i class="fa fa-sticky-note fa-fw"></i> 
                                            <?php print $encodeExplorer->getString("sticky_alerts"); ?>
                                        </label>
                                    </div>

                                    <div class="row toggled">
                                        <div class="col-sm-6">
                                            <?php
                                            $stickypos = $setUp->getConfig('sticky_alerts_pos') ? $setUp->getConfig('sticky_alerts_pos') : 'top-left';
                                            ?>
                                            <select class="form-control" name="sticky_alerts_pos_v">
                                                <option 
                                            <?php
                                            if ($stickypos == "top-left" || $stickypos == "top-right") {
                                                echo "selected";
                                            } ?> value="top">top</option>
                                                <option 
                                            <?php
                                            if ($stickypos == "bottom-left" || $stickypos == "bottom-right") {
                                                echo "selected";
                                            } ?> value="bottom">bottom</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <select class="form-control" name="sticky_alerts_pos_h">
                                                <option 
                                            <?php
                                            if ($stickypos == "top-left" || $stickypos == "bottom-left") {
                                                echo "selected";
                                            } ?> value="left">left</option>
                                                <option 
                                            <?php
                                            if ($stickypos == "top-right" || $stickypos == "bottom-right") {
                                                echo "selected";
                                            } ?> value="right">right</option>
                                            </select>
                                        </div> <!-- col 6 -->
                                    </div> <!-- row toggled -->
                                </div>

                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="show_head" 
                                        <?php
                                        if ($setUp->getConfig('show_head')) {
                                            echo "checked";
                                        } ?>><i class="fa fa-certificate fa-fw"></i> 
                                        <?php print $encodeExplorer->getString("custom_header"); ?>
                                    </label>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-default btn-file">
                                                <?php print $encodeExplorer->getString("upload"); ?> 
                                                <i class="fa fa-upload"></i>
                                                <input type="file" name="file" value="select">
                                            </span>
                                        </span>
                                        <input class="form-control" type="text" readonly 
                                        name="fileToUpload" id="fileToUpload" onchange="fileSelected();">
                                    </div>
                                </div>

                                <div class="placeheader form-group <?php echo $placealign; ?>">
                                    <img src="images/<?php print $setUp->getConfig('logo'); ?>">
                                </div>

                                <div class="form-group">
                                    <label><?php print $encodeExplorer->getString("alignment"); ?></label>

                                    <div class="form-group select-logo-alignment">
                                        <label class="radio-inline">
                                            <input form="settings-form" type="radio" name="align_logo" 
                                            <?php
                                            if ($setUp->getConfig('align_logo') == "left") {
                                                echo "checked";
                                            } ?> value="left"> <i class="fa fa-align-left"></i>
                                        </label>
                                        <label class="radio-inline">
                                            <input form="settings-form" type="radio" name="align_logo" 
                                            <?php
                                            if ($setUp->getConfig('align_logo') == "center") {
                                                echo "checked";
                                            } ?> value="center"> <i class="fa fa-align-center"></i>
                                        </label>
                                        <label class="radio-inline">
                                            <input form="settings-form" type="radio" name="align_logo" 
                                            <?php
                                            if ($setUp->getConfig('align_logo') == "right") {
                                                echo "checked";
                                            } ?> value="right"> <i class="fa fa-align-right"></i>
                                        </label>
                                    </div>
                                </div> <!-- .form-group-->
                            </div>
                        </div>
                    </div>
                </div> <!-- row -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-default pull-right" 
                    data-toggle="tooltip" data-placement="left"
                    title="<?php print $encodeExplorer->getString("save_settings"); ?>">
                        <i class="fa fa-save"></i>
                    </button>
                </div>
            </div><!-- box body -->
        </div><!-- box -->
    </div><!-- col -->
</div> <!-- row -->

<div class="row">
    <div class="col-sm-12">
        <div class="box box-default box-solid">
            <div class="box-header with-border">
                <i class="fa fa-eyedropper"></i> <?php print $encodeExplorer->getString("administration_color_scheme"); ?>
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
            <div class="box-body">
                <div class="row adminscheme">
                <?php
                $colorlist = array('blue', 'purple', 'red', 'yellow', 'green', 'white');
                foreach ($colorlist as $color) { 
                    if ($setUp->getConfig('admin_color_scheme') == $color) { 
                        $layoutclass = "minilayout active";
                        $state = "checked";
                    } else {
                        $layoutclass = "minilayout";
                        $state = "";
                    } ?>
                    <div class="col-md-2 col-sm-4 col-xs-6">
                        <div class="<?php echo $layoutclass; ?>">
                            <label>
                                <input type="radio" name="admin_color_scheme" value="<?php echo $color; ?>" <?php echo $state; ?> >
                                <?php echo $color; ?>
                                <div class="colorbar-scheme">
                                    <div class="colorbar primary-<?php echo $color; ?>"></div>
                                    <div class="colorbar primary-side-<?php echo $color; ?>"></div>
                                    <div class="colorbar secondary-side-<?php echo $color; ?>"></div>
                                </div>
                            </label>
                        </div>
                    </div>
                <?php
                } // end foreach
                ?>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-default pull-right" 
                    data-toggle="tooltip" data-placement="left"
                    title="<?php print $encodeExplorer->getString("save_settings"); ?>">
                        <i class="fa fa-save"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>