   <?php 
    /**
    * CUSTOM HEADER 
    */ 
    ?>
    <h3>
        <i class="fa fa-certificate"></i> 
        <?php print $encodeExplorer->getString("custom_header"); ?>
    </h3>
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
    <div class="row">
        <div class="col-sm-6">
            <form role="form" method="post" 
            action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" 
            enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <span class="btn btn-default btn-file">
                                        <?php print $encodeExplorer->getString("browse"); ?>
                                        <input type="file" name="file" value="select">
                                    </span>
                                </span>
                                <input class="form-control" type="text" readonly 
                                name="fileToUpload" id="fileToUpload" onchange="fileSelected();">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <button class="upload_sumbit btn btn-primary btn-block pull-left" type="submit">
                                <?php print $encodeExplorer->getString("upload"); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

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
        </div>

        <div class="col-sm-6 placeheader <?php echo $placealign; ?>">
            <img src="images/<?php print $setUp->getConfig('logo'); ?>">
        </div>
    </div>