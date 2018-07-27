<?php

use yii\helpers\Json;
use \backend\models\Auth;
use core\widgets\JsBlock;
use yii\helpers\Url;
// 获取权限
//$auth = Auth::getDataTableAuth('auth-rule');

// 定义标题和面包屑信息
$this->title = '操作日志';
?>
<?= Yii::$service->page->widget->render('datatables');?>
<?php JsBlock::begin() ?>
<script type="text/javascript">
        var oTypes = <?=Json::encode($otypes)?>,
            aAdmins = <?=Json::encode($adminUsers)?>;
        var m = meTables({
            title: "<?=$this->title?>",
            buttons: <?=Json::encode($buttons['buttons'])?>,
            operations: {
                width: "auto",
                buttons: <?=Json::encode($buttons['operations'])?>
            },
            bViewFull: true,
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
                        "title": "操作人",
                        "data": "created_id",
                        "sName": "created_id",
                        "edit": {"type": "text", "required": true, "number": true},
                        "createdCell": mt.adminString
                    },
                    {
                        "title": "类型",
                        "data": "type",
                        "sName": "type",
                        "edit": {"type": "text", "required": true, "number": true},
                        "value": oTypes,
                        "search": {"type": "select"},
                        "bSortable": false,
                        "createdCell": function (td, data) {
                            $(td).html(oTypes[data] ? oTypes[data] : data);
                        }
                    },
                    {
                        "title": "操作模块/控制器",
                        "data": "controller",
                        "sName": "controller",
                        "edit": {"type": "text", "required": true, "rangelength": "[2, 32]"},
                        "search": {"type": "text"},
                        "bSortable": false
                    },
                    {
                        "title": "操作方法",
                        "data": "action",
                        "sName": "action",
                        "edit": {"type": "text", "required": true, "rangelength": "[2, 32]"},
                        "search": {"type": "text"},
                        "bSortable": false
                    },
                    {
                        "title": "请求URL",
                        "data": "url",
                        "sName": "url",
                        "edit": {"type": "text", "required": true, "rangelength": "[2, 64]"},
                        "search": {"type": "text"},
                        "bSortable": false
                    },
                    {
                        "title": "数据唯一标识",
                        "data": "index",
                        "sName": "index",
                        "edit": {"type": "text"},
                        "bSortable": false
                    },
                    {
                        "title": "请求参数",
                        "data": "params",
                        "sName": "params",
                        "edit": {"type": "text"},
                        "bSortable": false,
                        "isHide": true,
                        "createdCell": function (td, data) {
                            var json = data, x, html = "[ <br/>";
                            try {
                                json = JSON.parse(data);
                                if (typeof json == 'object') {
                                    for (x in json) {
                                        html += "   " + x + " => " + json[x] + "<br/>";
                                    }
                                }
                            } catch (e) {

                            }

                            html += "]";

                            $(td).html(html);
                        }
                    },
                    {
                        "title": "创建时间",
                        "data": "created_at",
                        "sName": "created_at",
                        "edit": {"type": "text", "required": true, "number": true},
                        "createdCell": meTables.dateTimeString,
                        "defaultOrder": "desc"
                    }
                ]
            }
        });

        $(function () {
            m.init();
        });
    </script>
<?php JsBlock::end() ?>