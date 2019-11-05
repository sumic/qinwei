<?php

use yii\helpers\Json;
use yii\helpers\Url;
use \backend\models\Auth;
use core\widgets\JsBlock;
use \yii\helpers\Html;
use appadmin\assets\AssignViewAsset;
// 获取权限
//$auth = Auth::getDataTableAuth('auth-rule');

// 定义标题和面包屑信息
$this->title = '角色分配';
AssignViewAsset::register($this);
?>
<div class="well">
    <form id="search-form">
        <div class="row">
            <div class="col-xs-6">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">管理员</label>
                    <div class="col-sm-10">
                       <?= Html::dropDownList('user_id',null,$adminUsers,[
                                'multiple' => 'multiple',
                                'class' => 'chosen-select tag-input-style',
                                'data-placeholder' => '请选择管理员',
                            ]
                        )?> 
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">分配角色</label>
                    <div class="col-sm-10">
                       <?= Html::dropDownList('item_name', null, $role, [
                            'multiple' => 'multiple',
                            'class' => 'chosen-select tag-input-style',
                            'data-placeholder' => '请选择角色',
                        ])?> 
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 pull-right" style="margin-top: 10px;">
                <div class="pull-right" id="me-table-buttons">
                    <button class="btn btn-info btn-sm">
                        <i class="ace-icon fa fa-search"></i> 搜索
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- 表格数据 -->
<?= Yii::$service->page->widget->render('metable',[
    'buttons' => [
        'id' => 'me-buttons'
    ],
]);?>
<?php JsBlock::begin() ?>
<script type="text/javascript">
    var roles = <?=Json::encode($role)?>,
        aAdmins = <?=Json::encode($adminUsers)?>,
        oButtons = <?=Json::encode($buttons['buttons'])?>,
        oOperationsButtons = <?=Json::encode($buttons['operations'])?>;
        oButtons.updateAll = {bShow: false};
        oButtons.deleteAll = {bShow: false};
        oOperationsButtons.see = {bShow: false};
        oOperationsButtons.update = {bShow: false};

    var m = meTables({
        searchType: "top",
        search: {
            render: false
        },
        title: "角色分配",
        bCheckbox: false,
        buttons: oButtons,
        operations: {
            "width": "auto",
            buttons: oOperationsButtons
        },
        url: {
            search: '<?=Url::toRoute('search');?>',
            create: '<?=Url::toRoute('create');?>',
            delete: '<?=Url::toRoute('delete');?>',
            export: "export",
            upload: "upload",
            editable: "editable",
            deleteAll: '<?=Url::toRoute('delete');?>',
        },
        pk:'user_id',
        number:false,
        table: {
            "columns": [
                {
                    "title": "管理员", 
                    "data": "user_id", 
                    "value": aAdmins,
                    "edit": {"type": "select", "required": true},
                    "bSortable": false,
                     render: function (data) {
                            return $.getValue(aAdmins, data, data);
                        }
                },
                {
                    "title": "对应角色", 
                    "data": "item_name", 
                    "value": roles,
                    "edit": {
                        "type": "select",
                        "multiple": true,
                        "id": "select-multiple",
                        "required": true,
                        "class": "tag-input-style width-100 chosen-select",
                        "data-placeholder": "请选择一个角色"
                    },
                    "bSortable": false,
                    "createdCell": function(td, data) {
                        $(td).html(roles[data] ? roles[data] : data);
                    },
//                    "search": {
//                        "type": "select",
//                        "multiple": true,
//                        "id": "search-select",
//                        "class": "chosen-select"
//                    }
                },
                {
                    "title": "最初分配时间", 
                    "data": "created_at", 
                    "sName": "created_at",
                    "createdCell" : MeTables.dateTimeString
                }
            ]       
        }
    });

    var $select = null;

    $.extend(m,{
        // 显示的前置和后置操作
        beforeShow: function(data, child) {
            $("#select-multiple").val([]).trigger("chosen:updated").next().css({'width': "100%"});
            return true;
        }
    });

     $(function(){
         m.init();
         // 选择表
         $select = $(".chosen-select").chosen({
             allow_single_deselect: false,
             width: "100%"
         });
     });
</script>
<?php JsBlock::end() ?>