<?php

use yii\helpers\Json;
use \backend\models\Auth;
use core\widgets\JsBlock;
use yii\helpers\Url;
// 获取权限
//$auth = Auth::getDataTableAuth('auth-rule');

// 定义标题和面包屑信息
$this->title = '公众号管理';
?>
<?= Yii::$service->page->widget->render('datatables'); ?>
<?php JsBlock::begin() ?>
<script type="text/javascript">
    var aMptype = <?= Json::encode($mptype) ?>,
        arrStatus = <?= Json::encode($status) ?>;

    function amptype(td, data) {
        $(td).html(aMptype[data] ? aMptype[data] : '未选择');
    }

    $.extend(MeTables, {
        selectOptionsCreate: function(params) {
            return '<select ' + this.handleParams(params) + '><option value="">请选择</option><?= $options ?></select>';
        },
        selectOptionsSearchMiddleCreate: function(params) {
            delete params.type;
            params.id = "search-" + params.name;
            return '<label for="' + params.id + '"> ' + params.title + ': <select ' + this.handleParams(params) + '>' +
                '<option value="">请选择</option>' +
                '<?= $options ?>' +
                '</select></label>';
        }
    });
    var m = mt({
        title: "公众号管理",
        buttons: <?= Json::encode($buttons['buttons']) ?>,
        operations: {
            buttons: <?= Json::encode($buttons['operations']) ?>
        },
        number:false,
        url: {
            search: '<?= Url::toRoute('search'); ?>',
            create: '<?= Url::toRoute('create'); ?>',
            update: '<?= Url::toRoute('update'); ?>',
            delete: '<?= Url::toRoute('delete'); ?>',
            export: "export",
            upload: "upload",
            editable: "editable",
            deleteAll: '<?= Url::toRoute('delete'); ?>',
        },
        table: {
            "aoColumns": [{
                    "data": "id",
                    "title": "Id",
                    "defaultOrder": "desc",
                    "edit": {
                        "type": "hidden"
                    },
                },
                {
                    "data": "mpname",
                    "title": "公众号名称",
                    "edit": {
                        "required": 1,
                        "rangelength": "[2, 50]"
                    },
                    "search": {
                        type: "text"
                    },
                },
                {
                    "data": "mptype",
                    "title": "公众号类型",
                    "edit": {
                        "type": "selectOptions",
                        "number": 1,
                        id: "select-options"
                    },
                    "search": {
                        type: "selectOptions"
                    },
                    "createdCell": amptype
                },
                {
                    "data": "appid",
                    "title": "应用ID (Appid)",
                    "edit": {
                        "required": 1,
                        "rangelength": "[2, 100]"
                    },
                    "search": {
                        "type": "text"
                    },
                    "bSortable": false
                },
                {
                    "data": "appsecret",
                    "title": "应用密匙 (AppSecret)",
                    "edit": {
                        "rangelength": "[2, 50]"
                    },
                    "bSortable": false,
                },
                {
                    "data": "token",
                    "title": "令牌 (Token)",
                    "edit": {
                        "rangelength": "[3, 32]"
                    },
                    "bSortable": false
                },
                {
                    "data": "aeskey",
                    "title": "消息加密密匙 (AesKey)",
                    "edit": {
                        "rangelength": "[43, 43]"
                    },
                    "bSortable": false,
                    "bViews": true,
                    "isHide": true
                },
                {
                    "data": "status",
                    "title": "状态",
                    "value": arrStatus,
                    "createdCell": MeTables.statusString,
                    "edit": {
                        "type": "radio",
                        "default": 1,
                        "required": 1,
                        "number": 1
                    },
                    "bSortable": false
                },
                // 公共属性字段信息
                {
                    "data": "created_at",
                    "title": "创建时间",
                    "createdCell": MeTables.dateTimeString
                },
                {
                    "data": "updated_at",
                    "title": "修改时间",
                    "createdCell": MeTables.dateTimeString
                },
            ]
        }
    });

    // 添加之前之后处理
    $.extend(m, {
        beforeShow: function(data) {
            $("#select-options option").prop("disabled", false);
            return true;
        },

        afterShow: function(data) {
            if (this.action === "update") {
                // 自己不能选
                $("#select-options option[value='" + data.id + "']").prop("disabled", true);
                // 子类不能选
                $("#select-options option[data-pid='" + data.id + "']").prop("disabled", true).each(function() {
                    $("#select-options option[data-pid='" + $(this).val() + "']").prop("disabled", true)
                });
            }
            return true;
        },

        afterSave: function() {
            return true;
        }
    });

    // 表单初始化
    $(function() {
        m.init();
    });
</script>
<?php JsBlock::end() ?>