<?php

use yii\helpers\Json;
use \backend\models\Auth;
use core\widgets\JsBlock;
use yii\helpers\Url;
use appadmin\assets\DropzoneAsset;
// 获取权限
//$auth = Auth::getDataTableAuth('auth-rule');

// 定义标题和面包屑信息
$this->title = '录音列表';
DropzoneAsset::register($this);
?>
<?= Yii::$service->page->widget->render('metable'); ?>
<?= Yii::$service->page->widget->render('flashmessage'); ?>

<?php JsBlock::begin() ?>
<script type="text/javascript">
    var myDropzone = null;
    $.extend(MeTables, {
        /**
         * 定义编辑表单(函数后缀名Create)
         * 使用配置 edit: {"type": "email", "id": "user-email"}
         * edit 里面配置的信息都通过 params 传递给函数
         */
        "dropzoneCreate": function(params) {
            return '<div id="dropzone" class="dropzone"></div>';
        }
    });
    var aAdmins = <?= Json::encode($adminUsers) ?>,
        aParents = <?= Json::encode($parents) ?>;
        aStatus = <?= Json::encode($status) ?>;
        oButtons = <?= Json::encode($buttons['operations']) ?>;
        oButtons.see = {
            "cClass": "role-see"
        };

    var m = meTables({
        title: "录音文件",
        buttons: <?= Json::encode($buttons['buttons']) ?>,
        operations: {
            buttons: oButtons,
        },
        number: false,
        url: {
            search: '<?= Url::toRoute('search'); ?>',
            create: '<?= Url::toRoute('create'); ?>',
            update: '<?= Url::toRoute('update'); ?>',
            delete: '<?= Url::toRoute('delete'); ?>',
            export: "export",
            upload: '<?= Url::toRoute('upload'); ?>',
            editable: "editable",
            deleteAll: '<?= Url::toRoute('delete'); ?>',
        },
        viewConfig: {
            area: ['50%', 'auto']
        },
        table: {
            "columns": [{
                    "title": "Id",
                    "data": "id",
                    "defaultOrder": "desc",
                    "edit": {
                        "type": "hidden"
                    },
                    "bViews": false
                },

                {
                    data: "cid",
                    title: "项目分类",
                    edit: {
                        type: "selectOptions",
                        number: 1,
                        required: 1,
                        id: "select-options"
                    },
                    search: {
                        type: "selectOptions"
                    },
                    createdCell: parentStatus
                },
                {
                    "title": "状态",
                    "data": "status",
                    value:aStatus,
                    "search": {
                        "type": "select"
                    },
                    render: function(data) {
                        return $.getValue(aStatus, data, data);
                    },
                },

                {
                    "title": "录音名称",
                    "data": "name",
                    "bSortable": false,
                    "search": {
                        "type": "text"
                    },
                },

                {
                    "title": "文件上传",
                    "data": "fid",
                    "edit": {
                        "type": "dropzone"
                    },
                    "bSortable": false,
                    "isHide": true,
                    "bViews": false,
                },
                {
                    "title": "上传时间",
                    "data": "created_at",
                    "createdCell": MeTables.dateTimeString
                },
                {
                    "title": "上传者",
                    "data": "created_id",
                    render: function(data) {
                        return $.getValue(aAdmins, data, data);
                    },
                },
            ]
        }
    });

    function parentStatus(td, data) {
        $(td).html($.getValue(aParents, data, '顶级分类'));
    }
    $.extend(MeTables, {
        selectOptionsCreate: function(params) {
            return '<select ' + this.handleParams(params) + '><option value="">请选择</option><?= $options ?></select>';
        },
        selectOptionsSearchMiddleCreate: function(params) {
            delete params.type;
            params.id = "search-" + params.name;
            return '<label for="' + params.id + '"> ' + params.title + ': <select ' + this.handleParams(params) + '>' +
                '<option value="All">请选择</option>' +
                '<?= $options ?>' +
                '</select></label>';
        }
    });

    var $form = null;
    $.extend(m, {
        // 显示的前置和后置操作
        afterShow: function(data, child) {
            if (!$form) $form = $("#edit-form");
            myDropzone.removeAllFiles();
            $("#dropzone").find("div.dz-image-preview").remove();
            $form.find("input[name='fid[]']").remove();
            $form.find("input[name='name[]']").remove();
            return true;
        }
    });

    function addInput(name, fileinfo) {
        $form.append('<input type="hidden" data-name="' + fileinfo.original + '" name="fid[]"  value="' + fileinfo.fid + '">');
        $form.append('<input type="hidden" data-name="' + fileinfo.original + '" name="name[]" value="' + fileinfo.original + '">');
    }

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
                cancel: function() {
                    mixLayer = null;
                }
            });

        }
    }

    $(function() {
        m.init();
        $form = $("#edit-form");
        // 新版本上传修改
        var csrfParam = $('meta[name=csrf-param]').attr('content') || "_csrf",
            csrfToken = $('meta[name=csrf-token]').attr('content'),
            params = {};
        params[csrfParam] = csrfToken;

        Dropzone.autoDiscover = false;

        try {
            myDropzone = new Dropzone("#dropzone", {
                url: "<?= Url::toRoute(['uploads', 'sField' => 'Playback[url]', 'sType' => 'playback']) ?>",
                // The name that will be used to transfer the file
                paramName: "Playback[url]",
                params: params,
                parallelUploads: 100, //同时上传的文件个数
                autoProcessQueue: false,
                maxFilesize: 10, // MB
                addRemoveLinks: true,
                dictDefaultMessage: '<span class="bigger-150 bolder"><i class="ace-icon fa fa-caret-right red"></i> 拖拽文件</span> 上传 \
                <span class="smaller-80 grey">(或点击)</span> <br /> \
                <i class="upload-icon ace-icon fa fa-cloud-upload blue fa-3x"></i>',
                dictResponseError: '上传时遇到错误!',
                dictFileTooBig: '文件大小超过{{maxFilesize}} MB',
                dictRemoveFile: '删除文件',
                dictUploadCanceled: '上传已取消',
                dictCancelUpload: '取消上传',
                //change the previewTemplate to use Bootstrap progress bars
                previewTemplate: "<div class=\"dz-preview dz-file-preview\">\n<div class=\"dz-details\">\n<div class=\"dz-filename\"><span data-dz-name></span></div>\n<div class=\"dz-size\" data-dz-size></div>\n<img data-dz-thumbnail />\n</div>\n<div class=\"progress progress-small progress-striped active\"><div class=\"progress-bar progress-bar-success\" data-dz-uploadprogress></div></div>\n<div class=\"dz-success-mark\"><span></span></div>\n<div class=\"dz-error-mark\"><span></span></div>\n<div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n</div>",
                init: function() {
                    this.on("addedfile", function() {
                        $("#show-table-save").attr('disabled', false).html('开始上传');
                    });
                    this.on("success", function(file, response) {
                        swal('上传成功', ':) ' + response.msg, 'success');
                        //重新加载datatables
                        if (response.code === 0) {
                            addInput(file.name, response.data);
                        } else {
                            this.removeFile(file);
                            swal('上传失败', ':( ' + response.msg, 'error');
                        }
                    });
                    this.on("error", function(file, response) {
                        console.log(response);
                        $(".dz-error-message").text(response.msg);
                        //swal('上传失败', ':( ' + response.msg, 'error');
                        if (response.msg) {
                            $.notify({
                                icon: 'fa fa-warning',
                                message: "<strong>" + response.msg + "( " + response.data.original + " )</<strong>"
                            });
                        } else {
                            $.notify({
                                icon: 'fa fa-warning',
                                message: "<strong>" + response + "</<strong>"
                            });
                        }
                        return false;
                    });
                    this.on("removedfile", function(file) {
                        $form.find("input[data-name='" + file.name + "']").remove();
                    });
                    this.on("queuecomplete", function(file) {
                        if ($("input[name='fid[]']").length > 0) {
                            m.save();
                        }
                        $("#show-table-save").attr('disabled', true).html('上传完成');
                    });
                }
            });
        } catch (e) {
            console.error(e);
        }
        $("#show-table-save").attr('disabled', true).click(function() {
            if ($("#select-options").val() === '') {
                swal('错误', ':（ 请选择项目分类', 'error');
                return false;
            }
            myDropzone.processQueue();
            return false;
        }).html('开始上传');

        $(document).on('click', '.role-see-show-table', function() {
            var data = $.getValue(m.table.data(), $(this).data('row'));
            if (data) {
                layerOpen(
                    "查看" + data["name"] + "详情",
                    "<?= Url::toRoute(['view']) ?>?id=" + data['id']
                );
            }
        });
    });
</script>
<?php JsBlock::end() ?>