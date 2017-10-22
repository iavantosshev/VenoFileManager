<?php 
/**
 * Get available foders for users
 */
$availableFolders = array_filter($setUp->getFolders());

$utenti = $_USERS;
// get MasterAdmin ($king) 
// and remove it from list ($utenti)
$king = array_shift($utenti);
$kingmail = isset($king['email']) ? $king['email'] : "";
/**
* ADD NEW USER
*/
?>
<div class="col-sm-6">
    <button class="btn btn-info btn-lg btn-block" data-toggle="modal" data-target="#newuserpanel">
        <i class="fa fa-user-plus"></i> <?php print $encodeExplorer->getString("add_user"); ?>
    </button>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="newuserpanel">
  <div class="modal-dialog">
    <div class="modal-content">
        <form role="form" method="post" autocomplete="off" 
        action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>?users=new" 
        class="clear intero" enctype="multipart/form-data" id="newUsrForm">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-user-plus"></i> <?php print $encodeExplorer->getString("new_user"); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user fa-fw"></i>
                                </span>
                                <input type="text" class="form-control addme" name="newusername" id="newusername" 
                                placeholder="*<?php print $encodeExplorer->getString("username"); ?>">
                            </div>
                        </div>
                        <div class="col-md-6 form-group cooldropgroup">
                            <label class="sr-only">
                                <?php print $encodeExplorer->getString("role"); ?>
                            </label>
                            <div class="input-group btn-group cooldrop">
                            <span class="input-group-addon">
                                <i class="fa fa-check fa-fw"></i>
                            </span>
                            <select name="newrole" class="form-control coolselect">
                                <option value="user">user</option>
                                <option value="admin">admin</option>
                                <option value="superadmin">superadmin</option>
                            </select>
                            </div>
                        </div>
                    </div> <!-- row -->
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-lock fa-fw"></i>
                                </span>
                                <input type="password" name="newuserpass" class="form-control addme" id="newuserpass" 
                                placeholder="*<?php print $encodeExplorer->getString("password"); ?>">
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-envelope fa-fw"></i>
                                </span>
                                <input type="email" name="newusermail" 
                                class="form-control newusermail addme" 
                                placeholder="<?php print $encodeExplorer->getString("email"); ?>">
                            </div>
                        </div>
                    </div> <!-- row -->
                    <div class="row">
                        <div class="col-md-6 form-group cooldropgroup">
                            <label>
                                <?php print $encodeExplorer->getString("user_folder"); ?>
                            </label>
                            <?php
                            if (empty($availableFolders)) {
                                print "<fieldset disabled>";
                            } ?>
                            <div class="input-group btn-group cooldrop">
                                <span class="input-group-addon">
                                    <i class="fa fa-sitemap fa-fw"></i>
                                </span>
                                <select name="newuserfolders[]" class="form-control assignfolder" multiple="multiple">
                                <?php
                                foreach ($setUp->getFolders() as $folder) {
                                    print "<option value=\"".$folder."\">".$folder."</option>";
                                } ?>
                                </select>
                            </div>
                            <?php
                            if (empty($availableFolders)) {
                                print "</fieldset>";
                            } ?>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>
                                <?php print $encodeExplorer->getString("make_directory"); ?>
                            </label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-folder fa-fw"></i>
                                </span>
                                <input type="text" name="newuserfolder" 
                                class="form-control addme usrfolder getfolder assignnew" 
                                placeholder="<?php print $encodeExplorer->getString("add_new"); ?>">
                            </div>
                        </div>
                    </div> <!-- row -->
                    <div class="row">
                        <div class="col-md-6 form-group userquota cooldropgroup">
                            <label><?php print $encodeExplorer->getString("available_space"); ?></label>
                            <div class="input-group btn-group cooldrop">
                                <span class="input-group-addon">
                                    <i class="fa fa-tachometer fa-fw"></i>
                                </span>
                                <select class="form-control coolselect" name="quota">
                                    <option value=""><?php print $encodeExplorer->getString("unlimited"); ?></option>
                                    <?php
                                    foreach ($_QUOTA as $value) {
                                        print "<option value=\"".$value."\">".$value."MB</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div> <!-- row -->

                    <div class="row">
                        <div class="col-xs-6 form-group">
                        <?php
                        if (strlen($setUp->getConfig('email_from')) > 4) { ?>
                            <div class="checkbox usernotif">
                                <label>
                                <input type="checkbox" name="usernotif"> <i class="fa fa-envelope"></i> 
                                <?php print $encodeExplorer->getString("notify_user"); ?>
                                </label>
                            </div>
                        <?php 
                        } ?>
                        </div>
                    </div> <!-- row -->
                    <?php
                    /**
                    * Set additional custom fields
                    */
                    if (is_array($customfields)) { ?>
                        <div class="row">
                        <?php
                        foreach ($customfields as $customkey => $customfield) { 
                            if (isset($customfield['type'])) { ?>
                            <div class="col-md-6 form-group">
                                <label><?php echo $customfield['name']; ?></label>
                                <?php
                                if ($customfield['type'] === 'textarea') { ?>
                                <textarea name="<?php echo $customkey; ?>" class="form-control" rows="2"></textarea>
                                <?php
                                }
                                if ($customfield['type'] === 'select' && is_array($customfield['options'])) { 
                                    $multiselect = '';
                                    if (isset($customfield['multiple']) && $customfield['multiple'] == true) {
                                         $multiselect = ($customfield['multiple'] == true ? 'multiple="multiple"' : '');
                                    } ?>
                                    <select name="<?php echo $customkey; ?>" class="form-control coolselect" <?php echo $multiselect; ?>>
                                    <?php
                                    foreach ($customfield['options'] as $optionval => $optiontitle) { ?>
                                    <option value="<?php echo $optionval; ?>"><?php echo $optiontitle; ?></option>
                                    <?php
                                    } ?>
                                </select>
                                <?php
                                }
                                if ($customfield['type'] === 'text' || $customfield['type'] === 'email') { ?>
                                <input type="<?php echo $customfield['type']; ?>" name="<?php echo $customkey; ?>" class="form-control">
                                <?php
                                } ?>
                            </div>
                            <?php
                            }
                        } ?>
                        </div> <!-- row -->
                    <?php
                    } ?>
                </div>
            </div><!-- /.modal-body -->
            <div class="modal-footer">
                <button class="btn btn-success btn-lg">
                    <i class="fa fa-plus"></i> 
                        <?php print $encodeExplorer->getString("new_user"); ?>
                </button>
            </div>
        </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
$('#newUsrForm').submit(function(e){
    if ($('#newusername').val().length < 1) {
        $('#newusername').focus();
        e.preventDefault();
        return false;
    }
    if ($('#newuserpass').val().length < 1) {
        $('#newuserpass').focus();
        e.preventDefault();
        return false;
    }
});
</script>
