<?php

use yii\helpers\Json;
use \backend\models\Auth;
use core\widgets\JsBlock;
use yii\helpers\Url;
use appadmin\assets\DropzoneAsset;
// 获取权限
//$auth = Auth::getDataTableAuth('auth-rule');

// 定义标题和面包屑信息
$this->title = '文件列表';
DropzoneAsset::register($this);
?>
<?= Yii::$service->page->widget->render('metable');?>
<?php JsBlock::begin() ?>
<script type="text/javascript">
var myDropzone = null;
$.extend(MeTables, {
    /**
     * 定义编辑表单(函数后缀名Create)
     * 使用配置 edit: {"type": "email", "id": "user-email"}
     * edit 里面配置的信息都通过 params 传递给函数
     */
    "dropzoneCreate": function (params) {
        return '<div id="dropzone" class="dropzone"></div>';
    }
});
var aAdmins = <?=Json::encode($adminUsers)?>;
var m = meTables({
    title: "上传文件",
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
        upload: '<?=Url::toRoute('upload');?>',
        editable: "editable",
        deleteAll: '<?=Url::toRoute('delete');?>',
    },
    oViewConfig: {
        area: ['50%', 'auto']
    },
    table: {
        "columns": [
            {
                "title": "Id",
                "data": "id",
                "defaultOrder": "desc",
                "edit": {"type": "hidden"},
                "bViews":false
            },
            {
                "title": "原始名称",
                "data": "name",
                "bSortable": false
            },
            {
                "title": "保存名称",
                "data": "savename",
                "bSortable": false,
                "search": {"type": "text"},
            },
            {
                "title": "保存路径",
                "data": "savepath",
                "bSortable": false,
                "isHide":true,
            },
            {
                "title": "文件后缀",
                "data": "ext",
                "bSortable": false
            },
            {
                "title": "MiMe类型",
                "data": "mime",
                "bSortable": false
            },
            {
                "title": "文件大小 Byte",
                "data": "size",
                "bSortable": true,
            },
            {
                "title": "MD5",
                "data": "md5",
                "bSortable": false,
                "isHide":true,
            },
            {
                "title": "SHA1",
                "data": "sha1",
                "bSortable": false,
                "isHide":true,
            },
            {
                "title": "Url地址",
                "data": "url",
                "bSortable": false,
                "isHide":true,
            },
            {
                "title": "保存名称",
                "data": "name",
                "edit": {"type": "dropzone"},
                "bSortable": false,
                "isHide":true,
                "bViews":false,
            },
            {
                "title": "上传时间",
                "data": "created_at",
                "createdCell": meTables.dateTimeString
            },
            {
                "title": "上传者",
                "data": "created_id",
                render: function (data) { return $.getValue(aAdmins, data, data); },
            },
        ]
    }
});

var $form = null;
$.extend(m,{
    // 显示的前置和后置操作
    afterShow: function (data, child) {
        if (!$form) $form = $("#edit-form");
        myDropzone.removeAllFiles();
        $("#dropzone").find("div.dz-image-preview").remove();
        $form.find("input[name='url[]']").remove();
        if (this.action === "update" && data["url"]) {
            try {
                var imgs = JSON.parse(data["url"]);
                for (var i in imgs) {
                    var mockFile = { name: "Filename" + i, size: 12345 };
                    myDropzone.emit("addedfile", mockFile);
                    myDropzone.emit("thumbnail", mockFile, imgs[i]);
                    myDropzone.emit("complete", mockFile);
                    addInput(mockFile.name, imgs[i]);
                }
            } catch (e) {
                console.error(e)
            }
        }
        return true;
    }
});

function addInput(name, url) {
    $form.append('<input type="hidden" data-name="' + name + '" name="url[]" value="' + url + '">');
}

$(function () {
    m.init();

    $form = $("#edit-form");

    Dropzone.autoDiscover = false;

    try {
        myDropzone = new Dropzone("#dropzone", {
            url: "<?=Url::toRoute(['uploads', 'sField' => 'name','sType'=>'file'])?>",
            // The name that will be used to transfer the file
            paramName: "Uploads[file]",
            params: {
                "_csrf-appadmin": $('meta[name=csrf-token]').attr('content')
            },
            parallelUploads:10,//同时上传的文件个数
            autoProcessQueue: false,
            maxFilesize: 5, // MB
            addRemoveLinks: true,
            dictDefaultMessage:
                '<span class="bigger-150 bolder"><i class="ace-icon fa fa-caret-right red"></i> 拖拽文件</span> 上传 \
                <span class="smaller-80 grey">(或点击)</span> <br /> \
                <i class="upload-icon ace-icon fa fa-cloud-upload blue fa-3x"></i>'
            ,
            dictResponseError: '上传时遇到错误!',
            dictFileTooBig: '文件大小超过{{maxFilesize}} MB',
            dictRemoveFile: '删除文件',
            dictUploadCanceled:'上传已取消',
            dictCancelUpload:'取消上传',
            //change the previewTemplate to use Bootstrap progress bars
            previewTemplate: "<div class=\"dz-preview dz-file-preview\">\n<div class=\"dz-details\">\n<div class=\"dz-filename\"><span data-dz-name></span></div>\n<div class=\"dz-size\" data-dz-size></div>\n<img data-dz-thumbnail />\n</div>\n<div class=\"progress progress-small progress-striped active\"><div class=\"progress-bar progress-bar-success\" data-dz-uploadprogress></div></div>\n<div class=\"dz-success-mark\"><span></span></div>\n<div class=\"dz-error-mark\"><span></span></div>\n<div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n</div>"
            , init: function () {
            	
            	this.on("addedfile", function () {
            		$("#show-table-save").attr('disabled',false);
                });
                this.on("success", function (file, response) {
               		swal('上传成功',':) '+response.message, 'success');
               		//重新加载datatables
               		m.table.draw();
                });

                this.on("error", function (file, response) {
                    console.log(response);
                    $(".dz-error-message").text(response.message);
               		swal('上传失败',':( '+response.message, 'error');
                });

                this.on("removedfile", function(file){
                    $form.find("input[data-name='" + file.name + "']").remove();
                });
                this.on("queuecomplete", function(){
                	$("#show-table-save").attr('disabled',true);
                });
            }
        });
    } catch (e) {
        console.error(e);
    }
    $("#show-table-save").attr('disabled',true).click(function(){myDropzone.processQueue();return false;}).html('开始上传');
});
    </script>
<?php JsBlock::end() ?>
