<?php
use yii\helpers\Json;
use core\widgets\JsBlock;
use yii\helpers\Url;
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
            columns: [
                    {
                        title: "类型",
                        data: "type",
                        hide: true,
                        edit: {type: "hidden", value: iType}
                    },
                    {
                        title: "名称",
                        data: "name",
                        hide: true,
                        edit: {type: "hidden"},
                        search: {name: "name"},
                        sortable: false
                    },
                    {
                        title: "权限名称",
                        data: "name",
                        edit: {
                            required: true,
                            name: "newName",
                            rangeLength: "[2, 64]",
                            placeholder: "请输入英文字母、数字、_、/等字符串"
                        },
                        sortable: false
                    },
                    {
                        title: "说明描述",
                        data: "description",
                        edit: {
                            required: true,
                            rangeLength: "[2, 64]",
                            placeholder: "请输入简单描述信息"
                        },
                        search: {name: "description"},
                        sortable: false
                    },
                    {
                        title: "使用规则",
                        data: "rule_name",
                        value: rules,
                        edit: {"type": "select"},
                        search: {name: "rule_name"},
                        sortable: false,
                        createdCell: function (td, data) {
                            $(td).html(rules[data] ? rules[data] : data);
                        }
                    },
                    {
                        title: "创建时间",
                        data: "created_at",
                        createdCell: MeTables.dateTimeString,
                        defaultOrder: "desc"
                    },
                    {
                        title: "修改时间",
                        data: "updated_at",
                        createdCell: MeTables.dateTimeString
                    }
                ]
        }
    });

    $.extend(m,{
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