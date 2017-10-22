<?php
/**
 * VFM - veno file manager: include/activate.php
 * Activate new pending user
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
if ($regactive && $setUp->getConfig("registration_enable") == true) :

    if (file_exists('vfm-admin/users/users-new.php')) {
        include 'vfm-admin/users/users-new.php';

        if (!empty($newusers)) {
        
            global $users;
            global $newusers;

            $newuser = $updater->findUserKey($regactive);
            
            if ($newuser !== false) {
                $username = $newuser['name'];
                $usermail = $newuser['email'];

                if ($updater->findUser($username) === false && $updater->findEmail($usermail) === false) {
                    array_push($users, $newuser);
                    $updater->updateUserFile('new');
                } else {
                    $_ERROR = "<strong>".$username."</strong> ".$encodeExplorer->getString("file_exists");
                }

                // clean old non confirmed users 
                // clean current confirmed user
                $newusers = $updater->removeUserFromValue($newusers, 'name', $username);
                $newusers = $updater->removeUserFromValue($newusers, 'email', $usermail);

                $lifetime = strtotime("-1 day");
                $newusers = $updater->removeOldReg($newusers, 'date', $lifetime);

                if ($updater->updateRegistrationFile($newusers, 'vfm-admin/users/')) {
                    $_SUCCESS = $encodeExplorer->getString("registration_completed");
                } else {
                    $_WARNING = "failed updating registration file";
                }
            } else {
                $_ERROR = $encodeExplorer->getString("invalid_link");
            } 
        } else {
            $_ERROR = $encodeExplorer->getString("link_expired");
        } 
    }
endif;
