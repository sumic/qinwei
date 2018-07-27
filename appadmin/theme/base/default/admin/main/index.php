<?php

use yii\helpers\Json;
use \backend\models\Auth;
use core\widgets\JsBlock;
use yii\helpers\Url;
// 获取权限
//$auth = Auth::getDataTableAuth('auth-rule');

// 定义标题和面包屑信息
$this->title = '管理员管理';
?>
<?= Yii::$service->page->widget->render('metable');?>
<?php JsBlock::begin() ?>
<script type="text/javascript">
    var aStatus = <?=Json::encode($status)?>,
        aStatusColor = <?=Json::encode($statusColor)?>,
        aAdmins = <?=Json::encode($adminUsers)?>,
        aRoles  = <?=Json::encode($roles)?>,
        m = meTables({
            title: "管理员信息",
            fileSelector: ["#file"],
            buttons: <?=Json::encode($buttons['buttons'])?>,
            operations: {
                buttons: <?=Json::encode($buttons['operations'])?>
            },
            url: {
                search: '<?=Url::toRoute('search');?>',
                create: '<?=Url::toRoute('create');?>',
                update: '<?=Url::toRoute('update');?>',
                delete: '<?=Url::toRoute('delete');?>',
                export: "export",
                upload: '<?=Url::to(['upload', 'sField' => 'face','sType'=>'image']);?>',
                editable: "editable",
                deleteAll: '<?=Url::toRoute('delete');?>',
            },
            table: {
                "aoColumns":[
                    {
                        "title": "管理员ID",
                        "data": "id",
                        "sName": "id",
                        "edit": {"type": "hidden"},
                        "search": {"type": "text"},
                        "defaultOrder": "desc"
                    },
                    {
                        "title": "管理员账号",
                        "data": "username",
                        "sName": "username",
                        "edit": {"type": "text", "required": true, "rangelength": "[2, 255]"},
                        "search": {"type": "text"},
                        "bSortable": false
                    },
                    {
                        "title": "密码",
                        "data": "password",
                        "sName": "password",
                        "isHide": true,
                        "edit": {"type": "password", "rangelength": "[2, 20]"},
                        "bSortable": false,
                        "defaultContent": "",
                        "bViews": false
                    },
                    {
                        "title": "确认密码",
                        "data": "repassword",
                        "sName": "repassword",
                        "isHide": true,
                        "edit": {"type": "password", "rangelength": "[2, 20]", "equalTo": "input[name=password]:first"},
                        "bSortable": false,
                        "defaultContent": "",
                        "bViews": false
                    },
                    {
                        "title": "头像", "data": "face", "sName": "face", "isHide": true,
                        "edit": {
                            "type": "file",
                            options: {
                                "id": "file",
                                "name": "face",
                                "input-name": "face",
                                "input-type": "ace_file",
                                "file-name": "face"
                            }
                        }
                    },
                    {
                        "title": "邮箱",
                        "data": "email",
                        "sName": "email",
                        "edit": {"type": "text", "required": true, "rangelength": "[2, 255]", "email": true},
                        "search": {"type": "text"},
                        "bSortable": false
                    },
                    {
                        "title": "角色",
                        "data": "role",
                        "sName": "role",
                        "value": aRoles,
                        "edit": {"type": "select", "required": true},
                        "bSortable": false,
                        "createdCell": function(td, data) {
                            $(td).html(aRoles[data] ? aRoles[data] : data);
                        }
                    },
                    {
                        "title": "状态", "data": "status", "sName": "status", "value": aStatus,
                        "edit": {"type": "radio", "default": 10, "required": true, "number": true},
                        "bSortable": false,
                        "search": {"type": "select"},
                        "createdCell": function (td, data) {
                            $(td).html(mt.valuesString(aStatus, aStatusColor, data));
                        }
                    },
                    {
                        "title": "创建时间",
                        "data": "created_at",
                        "sName": "created_at",
                        "createdCell": meTables.dateTimeString
                    },
                    {
                        "title": "创建用户",
                        "data": "created_id",
                        "sName": "created_id",
                        "bSortable": false,
                        "createdCell": mt.adminString
                    },
                    {"title": "修改时间", "data": "updated_at", "sName": "updated_at", "createdCell": mt.dateTimeString},
                    {
                        "title": "修改用户",
                        "data": "updated_id",
                        "sName": "updated_id",
                        "bSortable": false,
                        "createdCell": mt.adminString
                    }
                ]
            }
        });

        var $file = null;
        mt.fn.extend({
            beforeShow: function(data) {
                $file.ace_file_input("reset_input");
                // 修改复值
                if (this.action == "update" && ! empty(data.face)) {
                    console.log(data.face);
                    $file.ace_file_input("show_file_list", [data.face]);
                }

                return true;
            }
        });

        $(function(){
            m.init();
            $file = $("#file");
        });
    </script>
<?php JsBlock::end() ?>
