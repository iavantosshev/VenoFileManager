<?php
/**
* EMAIL SETTINGS
**/
?>
<div class="anchor" id="view-email"></div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-default box-solid">
            <div class="box-header with-border">
                <i class="fa fa-envelope"></i> 
                <?php print $encodeExplorer->getString("email"); ?>
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
     
            <div class="box-body">
                <div class="form-group">
                    <label>
                        <i class="fa fa-envelope-o"></i> 
                        <?php print $encodeExplorer->getString("email_from"); ?>
                    </label>
                    <input type="email" class="form-control input-lg" 
                    name="email_from" value="<?php print $setUp->getConfig('email_from'); ?>"
                    placeholder="noreply@example.com">
                </div>

                <div class="checkbox checkbox-big clear toggle">
                    <label>
                        <input type="checkbox" name="smtp_enable" id="smtp_enable" 
                        <?php
                        if ($setUp->getConfig('smtp_enable') == true) {
                            echo "checked";
                        } ?>>SMTP mail
                    </label>
                </div>
            
                <div class="toggled">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>
                                    <?php print $encodeExplorer->getString("smtp_server"); ?>
                                </label>
                                <input type="text" class="form-control" 
                                name="smtp_server" value="<?php print $setUp->getConfig('smtp_server'); ?>"
                                placeholder="mail.example.com">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>
                                        <?php print $encodeExplorer->getString("port"); ?>
                                    </label>
                                    <input type="text" class="form-control" 
                                    name="port" value="<?php print $setUp->getConfig('port'); ?>"
                                    placeholder="25">
                                </div>

                                <div class="col-md-6">
                                    <label><?php print $encodeExplorer->getString("secure_connection"); ?></label>

                                    <select class="form-control" name="secure_conn">
                                        <option 
                                    <?php
                                    if ($setUp->getConfig('secure_conn') == "") {
                                        echo "selected";
                                    } ?> value="none">none</option>
                                        <option 
                                    <?php
                                    if ($setUp->getConfig('secure_conn') == "ssl") {
                                        echo "selected";
                                    } ?> value="ssl">SSL</option>
                                        <option 
                                    <?php
                                    if ($setUp->getConfig('secure_conn') == "tls") {
                                        echo "selected";
                                    } ?> value="tls">TLS</option>
                                    </select>
                                </div> <!-- col 6 -->
                            </div> <!-- row -->
                        </div> <!-- col 6 -->
                    </div> <!-- row -->
        
                    <div class="row">
                        <div class="col-sm-12">

                            <div class="checkbox clear toggle">
                                <label>
                                    <input type="checkbox" name="smtp_auth" 
                                    <?php
                                    if ($setUp->getConfig('smtp_auth') == true) {
                                        echo "checked";
                                    } ?>>
                                    <?php print $encodeExplorer->getString("smtp_auth"); ?>
                                </label>
                            </div>
                    
                            <div class="row toggled">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>
                                            <?php print $encodeExplorer->getString("username"); ?>
                                        </label>
                                        <input type="text" class="form-control" 
                                        name="email_login" value="<?php print $setUp->getConfig('email_login'); ?>"
                                        placeholder="login@example.com">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>
                                            <?php print $encodeExplorer->getString("password"); ?>
                                        </label>
                                        <input type="password" class="form-control" 
                                        name="email_pass" value=""
                                        placeholder="<?php print $encodeExplorer->getString("password"); ?>">
                                    </div>
                                </div> <!-- col 6 -->
                            </div> <!-- row toggled -->
                        </div> <!-- col 12 -->
                    </div> <!-- row -->

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
    </div>  <!-- col 12 -->
</div> <!-- row -->