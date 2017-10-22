    <div class="row">
        <div class="col-md-7 col-sm-6">
            <form role="form" method="post" 
            action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>?languagemanager=editlang">

                <div class="form-group">
                    <label><?php print $encodeExplorer->getString("edit_language"); ?> </label>

                    <div class="input-group">
                        <select class="form-control input-lg" name="editlang">
                        <?php
                        $translations = $encodeExplorer->getLanguages();

                        foreach ($translations as $key => $lingua) { ?>
                            <option value="<?php echo $key; ?>"
                            <?php
                            if ($key == $thelang) {
                                echo "selected";
                            } ?> >
                            <?php echo $lingua; ?>
                            </option>
                        <?php
                        } ?>
                        </select>
                        <span class="input-group-btn btn-group-lg">
                            <button class="btn btn-default" type="submit"><i class="fa fa-pencil-square-o"></i> 
                                <?php print $encodeExplorer->getString("edit"); ?>
                            </button>
                        </span>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-5  col-sm-6">
            <form role="form" method="post" 
            action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>?languagemanager=newlang">
                <div class="form-group">
                    <label>
                        2-letters ISO 639-1 code 
                        <a title="view full list" class="tooltipper" data-placement="left" target="_blank" href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control input-lg" name="newlang" placeholder="en">
                        <span class="input-group-btn btn-group-lg">
                            <button class="btn btn-info" type="submit">
                                <i class="fa fa-plus"></i> 
                                <?php print $encodeExplorer->getString("new_language"); ?>
                            </button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>