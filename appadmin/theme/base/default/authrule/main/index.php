<?php

use yii\helpers\Json;
use \backend\models\Auth;
use core\widgets\JsBlock;
use yii\helpers\Url;
// 获取权限
//$auth = Auth::getDataTableAuth('auth-rule');

// 定义标题和面包屑信息
$this->title = '规则管理';
?>
<?= Yii::$service->page->widget->render('datatables'); ?>
<?php JsBlock::begin() ?>
<script type="text/javascript">
    var m = meTables({
        title: "<?= $this->title ?>",
        pk: "id",
        number: false,
        buttons: <?= Json::encode($buttons['buttons']) ?>,
        operations: {
            buttons: <?= Json::encode($buttons['operations']) ?>
        },
        url: {
            search: '<?= Url::toRoute('search'); ?>',
            create: '<?= Url::toRoute('create'); ?>',
            delete: '<?= Url::toRoute('delete'); ?>',
            update: '<?= Url::toRoute('update'); ?>',
            export: "export",
            upload: "upload",
            editable: "editable",
            deleteAll: '<?= Url::toRoute('delete'); ?>',
        },
        table: {
            "columns": [{
                    "title": "ID",
                    "data": "id",
                    "isHide": false,
                    "edit": {
                        "type": "hidden"
                    },
                    "bSortable": true,
                    "bViews": false, //详情页面是否显示
                    "defaultOrder": "desc"
                },

                {
                    "title": "名称",
                    "data": "name",
                    "edit": {
                        "type": "text",
                        "required": true,
                        "rangelength": "[2, 64]"
                    },
                    "isHide": true,
                    "bViews": false, //详情页面是否显示
                },
                {
                    "title": "对应规则类",
                    "data": "data",
                    "edit": {
                        "type": "text",
                        "required": true,
                        "rangelength": "[2, 100]"
                    },
                    "bSortable": false
                },
                {
                    "title": "创建时间",
                    "data": "created_at",
                    "createdCell": MeTables.dateTimeString
                },
                {
                    "title": "修改时间",
                    "data": "updated_at",
                    "sName": "updated_at",
                    "createdCell": MeTables.dateTimeString
                }
            ]
        }
    });

    $.extend(m, {
        beforeShow: function(data) {
            if (this.action === "update") {
                data.newName = data.name;
            }

            return true;
        }
    });

    $(function() {
        m.init();
    });
</script>
<?php JsBlock::end() ?>