<?php

use yii\helpers\Json;
use \backend\models\Auth;
use core\widgets\JsBlock;
use yii\helpers\Url;
// 获取权限
//$auth = Auth::getDataTableAuth('auth-rule');

// 定义标题和面包屑信息
$this->title = '文章管理';
?>
<?= Yii::$service->page->widget->render('datatables'); ?>
<?= Yii::$service->page->widget->render('flashmessage'); ?>

<?php JsBlock::begin() ?>
<script type="text/javascript">
    var aAdmins = <?= Json::encode($users) ?>,
        imgPrefix = '<?= Yii::$app->params['site-img'] ?>',
        aParents = <?= Json::encode($parents) ?>,
        arrStatus = <?= Json::encode($status) ?>,
        arrStatus2 = <?= Json::encode($status2) ?>,
        oButtons = <?= Json::encode($buttons['operations']) ?>;
    oButtons.see = {
        "cClass": "role-see"
    };
    oButtons.other = {
        bShow: <?= Yii::$app->user->can('cms/main/edit') ? 'true' : 'false' ?>,
        "title": "编辑内容",
        "button-title": "编辑内容",
        "className": "btn-warning",
        "cClass": "role-edit",
        "icon": "fa-pencil",
        "sClass": "yellow"
    };

    function parentStatus(td, data) {
        $(td).html(aParents[data] ? aParents[data] : '顶级分类');
    }

    $.extend(MeTables, {
        selectOptionsCreate: function(params) {
            return '<select ' + this.handleParams(params) + '><option value="0">顶级分类</option><?= $options ?></select>';
        },
        selectOptionsSearchMiddleCreate: function(params) {
            delete params.type;
            params.id = "search-" + params.name;
            return '<label for="' + params.id + '"> ' + params.title + ': <select ' + this.handleParams(params) + '>' +
                '<option value="All">请选择</option>' +
                '<option value="0">顶级分类</option>' +
                '<?= $options ?>' +
                '</select></label>';
        }
    });
    var m = meTables({
        title: '<?= $this->title; ?>',
        buttons: <?= Json::encode($buttons['buttons']) ?>,
        operations: {
            width: "200px",
            buttons: oButtons,
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
                    "data": "cid",
                    "title": "分类",
                    "edit": {
                        "type": "selectOptions",
                        "number": 1,
                        id: "select-options"
                    },
                    "search": {
                        type: "selectOptions"
                    },
                    "createdCell": parentStatus,
                    "bSortable": false
                },
                {
                    "data": "sort",
                    "title": "排序",
                    "edit": {
                        "required": 1,
                        "rangelength": "[1, 50]"
                    },
                },
                {
                    "data": "title",
                    "bViews": false,
                    "title": "标题",
                    "bSortable": false,
                    "createdCell": function(td, data) {
                       $(td).html(MeTables.subString(data, 25));
                    }
                },
                {
                    "data": "title",
                    "title": "标题",
                    "edit": {
                        "rangelength": "[2, 50]"
                    },
                    "bSortable": false,
                    "isHide": true,
                    "search": {
                        "type": "text"
                    },
                },
                {
                    "data": "sub_title",
                    "title": "副标题",
                    "edit": {
                        "rangelength": "[2, 50]"
                    },
                    "bSortable": false,
                    "isHide": true,
                },
                {
                    "data": "author_name",
                    "title": "作者",
                },
                {
                    "data": "thumb",
                    "title": "图",
                    "bViews": false,
                    "createdCell": function(td, data) {
                        if (data != '') {
                            $(td).html('<span class="fa fa-file-picture-o"></span>');
                            $(td).hover(function() {
                                t = setTimeout(function() {}, 200);
                                layer.tips('<img style="max-width: 200px;max-height: 120px" src=' + imgPrefix + encodeURI(data) + '>', $(this))
                            }, function() {
                                clearTimeout(t);
                            });
                        }
                    },
                    "bSortable": false
                },
                {
                    "data": "thumb",
                    "title": "属性",
                    "bSortable": false,
                    "createdCell": function(td, data, rowData, row, col) {
                        $(td).html('<span onclick="changeStatus(\'' + data + '\')"' + 'class="label label-' + (parseInt(rowData.flag_headline) === 1 ? 'success">' : 'disable">') + '头条</span>');
                        $(td).append(' <span class="label label-' + (parseInt(rowData.flag_recommend) === 1 ? 'success">' : 'disable">') + '推荐</span>');
                        $(td).append(' <span class="label label-' + (parseInt(rowData.flag_slide_show) === 1 ? 'success">' : 'disable">') + '幻灯</span>');
                        $(td).append(' <span class="label label-' + (parseInt(rowData.flag_special_recommend) === 1 ? 'success">' : 'disable">') + '特荐</span>');
                        $(td).append(' <span class="label label-' + (parseInt(rowData.flag_roll) === 1 ? 'success">' : 'disable">') + '滚动</span>');
                        $(td).append(' <span class="label label-' + (parseInt(rowData.flag_bold) === 1 ? 'success">' : 'disable">') + '加粗</span>');
                        $(td).append(' <span class="label label-' + (parseInt(rowData.flag_picture) === 1 ? 'success">' : 'disable">') + '图片</span>');
                        $(td).attr('style', 'white-space: nowrap;');
                    }
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
                    "createdCell": function(td, data) {
                        $(td).html('<span class="label label-' + (parseInt(data) === 1 ? 'success">发布' : 'disable">草稿') + '</span>');
                    },
                },
                {
                    "data": "flag_headline",
                    "title": "头条",
                    "value": arrStatus2,
                    "edit": {
                        "type": "radio",
                        "default": 1,
                        "required": 1,
                        "number": 1
                    },
                    "search": {
                        "type": "select"
                    },
                    "isHide": true,
                    "bViews": false,
                    "bSortable": false
                },
                {
                    "data": "flag_recommend",
                    "title": "推荐",
                    "value": arrStatus2,
                    "edit": {
                        "type": "radio",
                        "default": 1,
                        "required": 1,
                        "number": 1
                    },
                    "search": {
                        "type": "select"
                    },
                    "isHide": true,
                    "bViews": false,
                    "bSortable": false
                },
                {
                    "data": "flag_slide_show",
                    "title": "幻灯",
                    "value": arrStatus2,
                    "edit": {
                        "type": "radio",
                        "default": 1,
                        "required": 1,
                        "number": 1
                    },
                    "search": {
                        "type": "select"
                    },
                    "isHide": true,
                    "bViews": false,
                    "bSortable": false
                },
                {
                    "data": "flag_special_recommend",
                    "title": "特荐",
                    "value": arrStatus2,
                    "edit": {
                        "type": "radio",
                        "default": 1,
                        "required": 1,
                        "number": 1
                    },
                    "search": {
                        "type": "select"
                    },
                    "isHide": true,
                    "bViews": false,
                    "bSortable": false
                },
                {
                    "data": "flag_roll",
                    "title": "滚动",
                    "value": arrStatus2,
                    "edit": {
                        "type": "radio",
                        "default": 1,
                        "required": 1,
                        "number": 1
                    },
                    "search": {
                        "type": "select"
                    },
                    "isHide": true,
                    "bViews": false,
                    "bSortable": false
                },
                {
                    "data": "flag_bold",
                    "title": "加粗",
                    "value": arrStatus2,
                    "edit": {
                        "type": "radio",
                        "default": 1,
                        "required": 1,
                        "number": 1
                    },
                    "search": {
                        "type": "select"
                    },
                    "isHide": true,
                    "bViews": false,
                    "bSortable": false
                },
                {
                    "data": "flag_picture",
                    "title": "图片",
                    "value": arrStatus2,
                    "edit": {
                        "type": "radio",
                        "default": 1,
                        "required": 1,
                        "number": 1
                    },
                    "search": {
                        "type": "select"
                    },
                    "isHide": true,
                    "bViews": false,
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
            window.location.reload();
            return true;
        },
        create: function() {
            layer.open({
                type: 2,
                area: ["100%", "100%"],
                title: '添加文章',
                content: '<?= Url::toRoute('create'); ?>',
                anim: 2,
                maxmin: true,
                cancel: function() {
                    mixLayer = null;
                }
            })
        }
    });

    var mixLayer = null;

    function layerClose() {
        layer.close(mixLayer);
        mixLayer = null;
    }

    function layerOpen(title, url) {
        console.log(mixLayer);
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
                cancel: function() {
                    mixLayer = null;
                }
            });

        }
    }
    // 表单初始化
    $(function() {
        m.init();
        // 添加查看事件
        $(document).on('click', '.role-see-show-table', function() {
            var data = $.getValue(m.table.data(), $(this).data('row'));

            if (data) {
                layerOpen(
                    "查看文章",
                    "<?= Url::toRoute('view') ?>?id=" + data['id']
                );
            }
        });

        // 添加修改内容事件
        $(document).on('click', '.role-edit-show-table', function() {
            var data = $.getValue(m.table.data(), $(this).data('row'));
            if (data) {
                layerOpen(
                    "编辑(" + data["title"] + ") 文章",
                    "<?= Url::toRoute('edit') ?>?id=" + data['id']
                );
            }
        })
    });

    
</script>
<?php JsBlock::end() ?>