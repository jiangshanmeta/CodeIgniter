<?php
$config = [
    'create_link'=>'/common/create/user',
    'create_privilege'=>[1],
    'docreate_link'=>'/common/doCreate/user',
    'detail_link'=>'/common/info/user',
    'detail_privilege'=>[50],
    'edit_link'=>'/common/update/user/',
    'edit_privilege'=>[2],
    'doedit_link'=>'/common/doUpdate/user/',
    'operators'=>[
        [
            'name'=>'delete',
            'label'=>'删除',
            'icon'=>'el-icon-delete',
            'link'=>'/common/doDelete/user/',
            'privilege'=>[52],
        ],

    ],

];
?>