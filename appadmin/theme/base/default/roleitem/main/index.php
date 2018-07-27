<?php

use yii\helpers\Json;
use \backend\models\Auth;
use core\widgets\JsBlock;
use yii\helpers\Url;
// 获取权限
//$auth = Auth::getDataTableAuth('auth-rule');

// 定义标题和面包屑信息
$this->title = '路由管理';
?>
<?= Yii::$service->page->widget->render('datatables');?>
<?php JsBlock::begin() ?>
<script type="text/javascript">
	var rules = <?=Json::encode($rules)?>,
        iType = <?=$type?>;
    var m = meTables({
        title: '<?=$this->title?>',
        pk: "id",
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
            upload: "upload",
            editable: "editable",
            deleteAll: '<?=Url::toRoute('delete');?>',
        },
        table: {
            "aoColumns": [
                {
                    "title": "类型",
                    "data": "type",
                    "sName": "type",
                    "isHide": true,
                    "bViews":false,//详情页面是否显示
                    "edit": {"type": "hidden", "value": iType}
                },
                {
                    "title": "ID",
                    "data": "id",
                    "sName": "id",
                    "isHide": false,
                    "bViews":false,//详情页面是否显示
                    "edit": {"type": "hidden"},
                    "bSortable": true,
                    "defaultOrder": "desc"
                },
                {
                    "title": "路由名称",
                    "data": "name",
                    "sName": "name",
                    "isHide": true,
                    "bViews":false,//详情页面是否显示
                    "edit": {"type": "hidden"},
                    "search": {"type": "text"},
                    "bSortable": false
                },
                {
                    "title": "路由名称",
                    "data": "name",
                    "sName": "newName",
                    "edit": {
                        "type": "text",
                        "required": true,
                        "rangelength": "[2, 64]",
                        placeholder: "请输入英文字母、数字、_、/等字符串,必须以/开头"
                    },
                    "bSortable": false
                },
                {
                    "title": "说明描述",
                    "data": "description",
                    "sName": "description",
                    "edit": {
                        "type": "text",
                        "required": true,
                        "rangelength": "[2, 64]",
                        placeholder: "请输入简单描述信息"
                    },
                    "search": {"type": "text"},
                    "bSortable": false
                },
                {
                    "title": "使用规则",
                    "data": "rule_name",
                    "sName": "rule_name",
                    "value": rules,
                    "edit": {"type": "select"},
                    "search": {"type": "text"},
                    "bSortable": false,
                    "createdCell": function (td, data) {
                        $(td).html(rules[data] ? rules[data] : data);
                    }
                },
                {
                    "title": "创建时间",
                    "data": "created_at",
                    "sName": "created_at",
                    "createdCell": mt.dateTimeString,
                    
                },
                {"title": "修改时间", "data": "updated_at", "sName": "updated_at", "createdCell": mt.dateTimeString}
            ]
        }
    });

    meTables.fn.extend({
        beforeShow: function(data) {
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

    $(function(){
        m.init();
    });
</script>
<?php JsBlock::end() ?>