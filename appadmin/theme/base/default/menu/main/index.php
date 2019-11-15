<?php

use yii\helpers\Json;
use \backend\models\Auth;
use core\widgets\JsBlock;
use yii\helpers\Url;
// 获取权限
//$auth = Auth::getDataTableAuth('auth-rule');

// 定义标题和面包屑信息
$this->title = '菜单管理';
?>
<?= Yii::$service->page->widget->render('datatables'); ?>
<?php JsBlock::begin() ?>
<script type="text/javascript">
    var aAdmins = <?= Json::encode($users) ?>,
        aParents = <?= Json::encode($parents) ?>,
        arrStatus = <?= Json::encode($status) ?>;

    var m = mt({
        title: "菜单管理",
        buttons: <?= Json::encode($buttons['buttons']) ?>,
        operations: {
            buttons: <?= Json::encode($buttons['operations']) ?>
        },
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
        number: false,
        table: {
            "columns": [{
                    data: "id",
                    title: "Id",
                    defaultOrder: "desc",
                    edit: {
                        type: "hidden"
                    },
                    search: {
                        type: "text"
                    }
                },
                {
                    data: "pid",
                    title: "上级分类",
                    edit: {
                        type: "selectOptions",
                        number: 1,
                        id: "select-options"
                    },
                    search: {
                        type: "selectOptions"
                    },
                    createdCell: parentStatus
                },
                {
                    "data": "menu_name",
                    "title": "栏目名称",
                    "edit": {
                        "required": 1,
                        "rangelength": "[2, 50]"
                    },
                    "search": {
                        "type": "text"
                    },
                    "bSortable": false
                },
                {
                    "data": "icons",
                    "title": "图标",
                    "edit": {
                        "rangelength": "[2, 50]",
                        "value": "menu-icon fa fa-caret-right"
                    },
                    "bSortable": false
                },
                {
                    "data": "url",
                    "title": "访问地址",
                    "edit": {
                        "rangelength": "[2, 50]"
                    },
                    "search": {
                        "type": "text"
                    },
                    "bSortable": false
                },
                {
                    "data": "status",
                    "title": "状态",
                    "value": arrStatus,
                    "edit": {
                        "type": "radio",
                        "default": 1,
                        "required": 1,
                        "number": 1
                    },
                    "search": {
                        "type": "select"
                    },
                    "createdCell": MeTables.statusString,
                    "bSortable": false
                },
                {
                    "data": "sort",
                    "title": "排序",
                    "edit": {
                        "type": "text",
                        "required": 1,
                        "number": 1,
                        "value": 100
                    }
                },
                // 公共属性字段信息
                {
                    "data": "created_at",
                    "title": "创建时间",
                    "createdCell": MeTables.dateTimeString
                },
                {
                    "data": "created_id",
                    "title": "创建用户",
                    render: function (data) { return $.getValue(aAdmins, data, data); },
                    "bSortable": false
                },
                {
                    "data": "updated_at",
                    "title": "修改时间",
                    "createdCell": MeTables.dateTimeString
                },
                {
                    "data": "updated_id",
                    "title": "修改用户",
                    render: function (data) { return $.getValue(aAdmins, data, data); },
                    "bSortable": false
                }
            ]
        }
    });

    function parentStatus(td, data) {
        $(td).html($.getValue(aParents, data, '顶级分类'));
    }

    $.extend(MeTables, {
        selectOptionsCreate: function(params) {
            return '<select ' + this.handleParams(params) + '><option value="0">顶级分类</option><?= $options ?></select>';
        },
        selectOptionsSearchMiddleCreate: function(params) {
            console.log(params);
            delete params.type;
            params.id = "search-" + params.name;
            return '<label for="' + params.id + '"> ' + params.title + ': <select ' + this.handleParams(params) + '>' +
                '<option value="All">请选择</option>' +
                '<option value="0">顶级分类</option>' +
                '<?= $options ?>' +
                '</select></label>';
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