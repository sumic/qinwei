<?php

use yii\helpers\Json;
use \backend\models\Auth;
use core\widgets\JsBlock;
use yii\helpers\Url;
// 获取权限
//$auth = Auth::getDataTableAuth('auth-rule');

// 定义标题和面包屑信息
$this->title = '角色管理';
?>
<!-- flashmessage -->
<?= Yii::$service->page->widget->render('flashmessage');?>
<?= Yii::$service->page->widget->render('datatables');?>
<?php JsBlock::begin() ?>
<script type="text/javascript">
    var iType = <?=$type?>,
        oButtons = <?=Json::encode($buttons['buttons'])?>,
        oOperationsButtons = <?=Json::encode($buttons['operations'])?>;

        oButtons.updateAll = {bShow: false};
        oButtons.deleteAll = {bShow: false};
        oOperationsButtons.see = {"cClass": "role-see"};
        oOperationsButtons.other = {
        bShow: <?=Yii::$app->user->can('role/main/update') ? 'true' : 'false' ?>,
        "title": "编辑权限",
        "button-title": "编辑权限",
        "className": "btn-warning",
        "cClass": "role-edit",
        "icon": "fa-pencil-square-o",
        "sClass": "yellow"
    };

    var m = mt({
        title: "角色信息",
        checkbox: false,
        buttons: oButtons,
        operations: {
            width: "200px",
            buttons: oOperationsButtons
        },
        number: false,
        table: {
            columns: [
                {
                    title: "类型",
                    data: "type",
                    hide: true,
                    export: false,
                    edit: {type: "hidden", value: iType}
                },
                {
                    title: "名称",
                    data: "name",
                    hide: true,
                    edit: {type: "hidden"},
                    search: {name: "name"}
                },
                {
                    title: "角色名称",
                    data: "name",
                    edit: {
                        name: "newName",
                        required: true,
                        rangeLength: "[2, 64]",
                        placeholder: "请输入英文字母、数字、_、/等字符串"
                    },
                    sortable: false
                },
                {
                    title: "说明描述",
                    data: "description",
                    edit: {
                        type: "text",
                        required: true,
                        rangeLength: "[2, 255]",
                        placeholder: "请输入简单描述信息"
                    },
                    search: {name: "description"},
                    sortable: false
                },
                {
                    title: "创建时间",
                    data: "created_at",
                    defaultOrder: "desc",
                    createdCell: MeTables.dateTimeString
                },
                {
                    title: "修改时间",
                    data: "updated_at",
                    createdCell: MeTables.dateTimeString
                }
            ]
        }
    });

    $.extend(m, {
        beforeShow: function (data) {
            if (this.action === "update") {
                data.newName = data.name;
            }

            return true;
        },
        afterShow: function () {
            $(this.options.sFormId).find('input[name=type]').val(iType);
            return true;
        }
    });

    var mixLayer = null;

    function layerClose() {
        layer.close(mixLayer);
        mixLayer = null;
    }

    function layerOpen(title, url) {
        if (mixLayer) {
            layer.msg("请先关闭当前的弹出窗口");
        } else {
            mixLayer = layer.open({
                type: 2,
                area: ["90%", "90%"],
                title: title,
                content: url,
                anim: 2,
                maxmin: true,
                cancel: function () {
                    mixLayer = null;
                }
            });
        }
    }

    $(function () {
        m.init();

        // 添加查看事件
        $(document).on('click', '.role-see-show-table', function () {
            var data = $.getValue(m.table.data(), $(this).data('row'));
            if (data) {
                layerOpen(
                    "查看" + data["name"] + "(" + data["description"] + ") 详情",
                    "<?=Url::toRoute(['role/view'])?>?name=" + data['name']
                );
            }
        });

        // 添加修改权限事件
        $(document).on('click', '.role-edit-show-table', function () {
            var data = $.getValue(m.table.data(), $(this).data('row'));
            if (data) {
                layerOpen(
                    "编辑" + data["name"] + "(" + data["description"] + ") 信息",
                    "<?=Url::toRoute(['role/edit'])?>?name=" + data['name']
                );
            }
        })
    })
</script>
<?php JsBlock::end() ?>