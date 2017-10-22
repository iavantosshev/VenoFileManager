<hr>
<div class="row">
    <div class="col-md-12">
        <div class="box box-default box-solid">
            <div class="box-header with-border">
                <strong><?php echo $encodeExplorer->getString("users"); ?></strong>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="data-users" class="table table-hover table-condense">
                        <thead>
                            <tr>
                                <th><span class="sorta nowrap">ID</span></th>
                                <th></th>
                                <th><span class="sorta nowrap"><?php echo $encodeExplorer->getString("username"); ?></span></th>
                                <th><span class="sorta nowrap"><?php echo $encodeExplorer->getString("role"); ?></span></th>
                                <th><span class="sorta nowrap"><?php echo $encodeExplorer->getString("email"); ?></span></th>
                                <th><span class="nowrap"><?php echo $encodeExplorer->getString("user_folder"); ?></span></th>
                                <th><span class="nowrap"><?php echo $encodeExplorer->getString("available_space"); ?></span></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            /**
                            * LIST USERS
                            */
                            foreach ($utenti as $key => $user) {
                                $usermail = isset($user['email']) ? $user['email'] : '';
                                $userdirs = isset($user['dir']) ? json_decode($user['dir'], true) : false;
                                $userquota = ($userdirs && isset($user['quota'])) ? $user['quota'] : '';
                                $printquota = strlen($userquota) > 0 ? $userquota.' MB' : '';
                                $listuserdirs = false;
                                $gooduserdirs = false;
                                if ($userdirs) {
                                    $gooduserdirs = array();
                                    foreach ($userdirs as $dir) { 
                                        if (in_array($dir, $availableFolders)) {
                                            array_push($gooduserdirs, $dir);
                                        }
                                    }
                                    $countuserdirs = count($gooduserdirs);
                                    $listuserdirs = $countuserdirs > 1 ? '('.$countuserdirs.')' : $userdirs[0];
                                } ?>
                            <tr>
                                <td><?php echo $key; ?></td>
                                <td><img class="img-circle" width="25" src="<?php echo GateKeeper::getAvatar($user['name'], ''); ?>"></td>
                                <td><?php echo $user['name']; ?></td>
                                <td><em><?php echo $user['role'];?></em></td>
                                <td><?php echo $usermail; ?></td>
                                <td><?php echo $listuserdirs; ?></td>
                                <td><?php echo $printquota; ?></td>

                                <td class="usrblock">
                                    <button class="round-butt butt-mini btn-default" data-toggle="modal" data-target="#modaluser">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                <?php 
                                foreach ($user as $attr => $value) {
                                    if ($attr !== 'dir' && $attr !== 'pass') { ?>
                                    <input type="hidden" data-key="<?php echo $attr; ?>" value="<?php echo $value; ?>" class="send-userdata">
                                <?php
                                    }
                                }
                                if ($gooduserdirs) {
                                    foreach ($gooduserdirs as $dir) { ?>
                                        <input type="hidden" value="<?php echo $dir; ?>" class="s-userfolders">
                                <?php
                                    }
                                } ?>
                                </td>
                            </tr>
                            <?php 
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/datatables.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#data-users').DataTable({
        dom        : 'flprtip',
        lengthMenu : [[25, 50, 100], [25, 50, 100]],
        order      : [[ 0, 'desc' ]],
        language : {
            emptyTable     : '--',
            info           : '_START_-_END_ / _TOTAL_ ',
            infoEmpty      : '',
            infoFiltered   : '',
            infoPostFix    : '',
            lengthMenu     : ' _MENU_',
            loadingRecords : '<i class="fa fa-refresh fa-spin"></i>',
            processing     : '<i class="fa fa-refresh fa-spin"></i>',
            search         : '<span class="input-group-addon"><i class="fa fa-search"></i></span> ',
            zeroRecords    : '--',
            paginate : {
                first    : '<i class="fa fa-angle-double-left"></i>',
                last     : '<i class="fa fa-angle-double-right"></i>',
                previous : '<i class="fa fa-angle-left"></i>',
                next     : '<i class="fa fa-angle-right"></i>'
            }
        },
        columnDefs : [ 
            { 
                targets : [ 0, 1, 3 ], 
                searchable : false
            },
            { 
                targets : [ 5, 6, 7 ], 
                orderable  : false,
                searchable : false
            }
        ]
    });
});
</script>
