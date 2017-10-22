<?php
/**
 * VFM - veno file manager: include/register.php
 * front-end registration panel
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
* Get additional custom fields
*/
$customfields = false;
if (file_exists('vfm-admin/users/customfields.php')) {
    include 'vfm-admin/users/customfields.php';
}
/**
* Registration Mask
*/
if ($setUp->getConfig("registration_enable") == true) { ?>
    
    <script type="text/javascript" src="vfm-admin/js/registration.js"></script>

    <section class="vfmblock">
        <div class="login">
            <div id="regresponse"></div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-user-plus"></i> <?php print $encodeExplorer->getString('registration'); ?>
                </div>
                <div class="panel-body">
                    <form id="regform" action="<?php print $encodeExplorer->makeLink(false, null, ""); ?>">
                        <input type="hidden" id="trans_pwd_match" value="<?php echo $encodeExplorer->getString("passwords_dont_match"); ?>">
                        <input type="hidden" id="trans_accept_terms" value="<?php echo $encodeExplorer->getString("accept_terms_and_conditions"); ?>">
                        <div id="login_bar" class="form-group">
                            <div class="form-group">
                                <div class="has-feedback">
                                    <label for="user_name">* 
                                        <?php echo $encodeExplorer->getString("username"); ?>
                                    </label>  
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                        <i class="fa fa-user fa-fw"></i>
                                        </span>
                                        <input type="text" name="user_name" value="" id="user_name" class="form-control" />
                                    </div>
                                    <span class="glyphicon glyphicon-minus form-control-feedback"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="user_pass">* 
                                    <?php echo $encodeExplorer->getString("password"); ?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock fa-fw"></i></span>
                                    <input type="password" name="user_pass" id="user_pass" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="user_pass">* 
                                    <?php echo $encodeExplorer->getString("password")." (".$encodeExplorer->getString("confirm").")"; ?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock fa-fw"></i></span>
                                    <input type="password" name="user_pass_confirm" id="user_pass_check" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="user_email">* 
                                    <?php echo $encodeExplorer->getString("email"); ?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-envelope fa-fw"></i></span>
                                    <input type="email" name="user_email" class="form-control" />
                                </div>
                            </div>
                            <?php
                            /**
                            * Additional user custom fields
                            */
                            if (is_array($customfields)) { ?>
                                <?php
                                foreach ($customfields as $customkey => $customfield) { ?>
                                    <div class="form-group">
                                        <label><?php echo $customfield['name']; ?></label>
                                    <?php
                                    if ($customfield['type'] === 'textarea') { ?>
                                        <textarea name="<?php echo $customkey; ?>" class="form-control" rows="2"></textarea>
                                    <?php
                                    }
                                    if ($customfield['type'] === 'select' && is_array($customfield['options'])) { ?>
                                        <select name="<?php echo $customkey; ?>" class="form-control coolselect">
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

                            <?php
                            $disclaimerfile = 'vfm-admin/registration-disclaimer.html';
                            if (file_exists($disclaimerfile)) {
                                $disclaimer = file_get_contents($disclaimerfile);
                                echo $disclaimer; ?>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="agree" name="agree"> Accept 
                                        <a href="#" data-toggle="modal" data-target="#disclaimer" required>
                                            <u>terms and conditions</u>
                                        </a>
                                    </label>
                                </div>
                            <?php
                            } ?>
                            <div class="form-group">
                            <?php 
                            /* ************************ CAPTCHA ************************* */
                            if ($setUp->getConfig("show_captcha_register") == true ) { 
                                $capath = "vfm-admin/";
                                include "vfm-admin/include/captcha.php"; 
                            }   ?>
                                <button type="submit" class="btn btn-primary btn-block" />
                                    <i class="fa fa-check"></i> 
                                    <?php print $encodeExplorer->getString("register"); ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="mailpreload">
                    <div class="cta">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </div>
            </div>
            <a href="?dir="><i class="fa fa-sign-in"></i>  <?php print $encodeExplorer->getString("log_in"); ?></a>
        </div>
    </section>
    <?php
}
