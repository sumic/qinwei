<?php

use yii\helpers\Json;
use core\widgets\JsBlock;

// 定义标题和面包屑信息
$this->title = '管理员管理';
?>
<?= Yii::$service->page->widget->render('metable');?>
<?php JsBlock::begin() ?>
<script type="text/javascript">
    var aStatus = <?=Json::encode($status)?>,
        aStatusColor = <?=Json::encode($statusColor)?>,
        aAdmins = <?=Json::encode($adminUsers)?>,
        aRoles = <?=Json::encode($roles)?>,
        bHide = <?=$isSuper ? 'false' : 'true'?>;
    m = meTables({
        title: "管理员信息",
        fileSelector: ["#file"],
        buttons: <?=Json::encode($buttons['buttons'])?>,
        operations: {
            buttons: <?=Json::encode($buttons['operations'])?>
        },
        number: false,
        table: {
            columns: [
                {
                    title: "管理员ID",
                    data: "id",
                    edit: {type: "hidden"},
                    search: {type: "text"},
                    defaultOrder: "desc"
                },
                {
                    title: "管理员账号",
                    data: "username",
                    edit: {required: true, rangeLength: "[2, 255]", autocomplete: "off"},
                    search: {type: "text", name: "username"},
                    sortable: false
                },
                {
                    title: "密码",
                    data: "password",
                    hide: true,
                    edit: {type: "password", rangeLength: "[2, 20]", autocomplete: "new-password"},
                    sortable: false,
                    defaultContent: "",
                    view: false
                },
                {
                    title: "确认密码",
                    data: null,
                    hide: true,
                    edit: {
                        type: "password",
                        name: "repassword",
                        rangeLength: "[2, 20]",
                        equalTo: "input[name=password]:first"
                    },
                    sortable: false,
                    defaultContent: "",
                    view: false
                },
                {
                    title: "头像",
                    data: "face",
                    hide: true,
                    edit: {
                        type: "file",
                        options: {
                            "id": "file",
                            "name": "UploadForm[face]",
                            "input-name": "face",
                            "input-type": "ace_file",
                            "file-name": "face"
                        }
                    }
                },
                {
                    title: "邮箱",
                    data: "email",
                    edit: {required: true, rangeLength: "[2, 255]", email: true},
                    search: {name: "email"},
                    sortable: false
                },
                {
                    title: "角色",
                    data: "role",
                    value: aRoles,
                    edit: {type: "select", required: true},
                    sortable: false,
                    createdCell: function (td, data) {
                        $(td).html($.getValue(aRoles, data, data));
                    }
                },
                {
                    title: "状态",
                    data: "status",
                    value: aStatus,
                    edit: {type: "radio", default: 10, required: true, number: true},
                    sortable: false,
                    search: {type: "select"},
                    createdCell: function (td, data) {
                        $(td).html(MeTables.valuesString(aStatus, aStatusColor, data));
                    }
                },
                {
                    title: "创建时间",
                    data: "created_at",
                    createdCell: MeTables.dateTimeString
                },
                {
                    title: "创建用户",
                    data: "created_id",
                    sortable: false,
                    render: function (data) {
                        return $.getValue(aAdmins, data, data);
                    }
                },
                {
                    title: "修改时间",
                    data: "updated_at",
                    createdCell: MeTables.dateTimeString
                },
                {
                    title: "修改用户",
                    data: "updated_id",
                    sortable: false,
                    export: false,
                    render: function (data) { return $.getValue(aAdmins, data, data); }
                },
                {
                    title: "切换登录",
                    data: null,
                    sortable: false,
                    hide: bHide,
                    render: function (data, is_display, row) {
                        if (row.id != "<?=$admin->id?>" && row["switch_user_login"]) {
                            return '<a target="_parent" href="' + row["switch_user_login"] + '"><?=Yii::t('admin', '切换登录')?></a>'
                        }

                        return '--';
                    }
                }
            ]
        }
    });

    var $file = null;
    $.extend(m, {
        beforeShow: function (data) {
            $file.ace_file_input("reset_input");
            // 修改复值
            if (this.action == "update" && !empty(data.face)) {
                $file.ace_file_input("show_file_list", [data.face]);
            }

            return true;
        }
    });

    $(function () {
        m.init();
        $file = $("#file");
    });
    </script>
<?php JsBlock::end() ?>
