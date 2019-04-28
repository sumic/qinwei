<?php
use yii\helpers\Json;
use core\widgets\JsBlock;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use appadmin\assets\WechatAsset;
use appadmin\assets\NotifyAsset;
use appadmin\assets\DropzoneAsset;
use appadmin\assets\DataTablesAsset;

WechatAsset::register($this);
NotifyAsset::register($this);
// 获取权限
// $auth = Auth::getDataTableAuth('auth-rule');

// 定义标题和面包屑信息
$this->title = '素材管理';
?>
<div style="margin-bottom: 10px">
	<?=Html::dropDownList('mpid', $currentMp, ArrayHelper::map($mpbase, 'id', 'mpname'),
	    ['class' => 'chosen-select','encode' => false,
	        'prompt' => '选择公众号...','id' => 'mpid']);?>
</div>
<div class="tabbable">

	<ul id="myTab" class="nav nav-tabs">
		<li class="active"><a href="#news" data-toggle="tab">图文消息</a></li>
		<li><a href="#pic" data-toggle="tab">图片</a></li>
		<li><a href="#voice" data-toggle="tab">语音</a></li>
		<li><a href="#video" data-toggle="tab">视频</a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane in active" id="news">
		<!-- news 表格数据 -->
        <?= Yii::$service->page->widget->render('metable',[
            'buttons' => [
                'id' => 'news-buttons'
            ],
            'table' => [
                'id' => 'news_tables'
        ],
        ]);?>
		news
		</div>
		<div class="tab-pane" id="pic">
		<!-- pic 表格数据 -->
        <?= Yii::$service->page->widget->render('metable',[
            'buttons' => [
                'id' => 'pic-buttons'
            ],
            'table' => [
                'id' => 'pic_tables'
        ],
        ]);?>
		pics
		</div>
		<div class="tab-pane" id="voice">
		voice
		</div>
		<div class="tab-pane" id="video">video</div>
	</div>

</div>
<?php JsBlock::begin() ?>
<script>
$.notifyDefaults({
    icon: 'fa fa-bell',
    placement: {
        from: "top",
        align: "center"
    },
    template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0} alert-with-icon alert-rose" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
        '<i data-notify="icon"></i>' +
        '<span data-notify="title">{1}</span>' +
        '<span data-notify="message">{2}</span>' +
        '<div class="progress" data-notify="progressbar">' +
        '	<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
        '</div>' +
        '<a href="{3}" target="{4}" data-notify="url"></a>' +
        '</div>',
})
$(function(){
        //选择公众号后初始化
    	$('#mpid').change(function(){
    		initTab($('#myTab .active > a').attr('href'));
        	});
    	$('.chosen-select').chosen({
			allow_single_deselect:false,
			no_results_text: "没有找到相关公众号",
			search_contains:true
			});
		$(window)
		.off('resize.chosen')
		.on('resize.chosen', function() {
			$('.chosen-select').each(function() {
				 var $this = $(this);
				 $this.next().css({'width': $this.parent().width()});
			})
		}).trigger('resize.chosen'); 
		//切换TABS
		$('#myTab a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			if($("#mpid").val() == ''){
				$.notify({icon: 'fa fa-bell',message: "<strong>请先选择公众号</strong>"},{type: "info"});
				return false;
			}
			initTab($(e.target).attr('href'));
		})
})

//初始化TAB内容
function initTab(tabs)
{
	var mptext =  $("#mpid option:selected").text();
	switch(tabs){
	case("#news"):
		$.notify({icon: 'fa fa-bell',message: "<strong>"+mptext+" 图文素材已刷新</strong>"},{type: "info"});
		break;
	case("#pic"):
		$.notify({icon: 'fa fa-bell',message: "<strong>"+mptext+" 图片素材已刷新</strong>"},{type: "info"});
		break;
	case("#voice"):
		$.notify({icon: 'fa fa-bell',message: "<strong>"+mptext+" 语音素材已刷新</strong>"},{type: "info"});
		break;
	case("#video"):
		$.notify({icon: 'fa fa-bell',message: "<strong>"+mptext+" 视频素材已刷新</strong>"},{type: "info"});
		break;
	default:
		return false;
	}
}
        var news_tables = mt({
            title: "文章管理",
            sTable:"#news_tables",
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
                        "data": "id",
                        "sName": "id",
                        "title": "Id",
                        "defaultOrder": "desc",
                        "edit": {"type": "hidden"},
                    },
                    {
                        "data": "mpname",
                        "sName": "mpname",
                        "title": "名称",
                        "edit": {"required": 1, "rangelength": "[2, 50]"},
                        "search": {type: "text"},
                    },
                    // 公共属性字段信息
                    {"data": "created_at", "sName": "created_at", "title": "创建时间", "createdCell": mt.dateTimeString},
                    {"data": "updated_at", "sName": "updated_at", "title": "修改时间", "createdCell": mt.dateTimeString},
                ]
            }
        });

        var pic_tables = mt({
            title: "图片管理",
            sTable:"#pic_tables",
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
                        "data": "id",
                        "sName": "id",
                        "title": "Id",
                        "defaultOrder": "desc",
                        "edit": {"type": "hidden"},
                    },
                    {
                        "data": "mpname",
                        "sName": "mpname",
                        "title": "名称",
                        "edit": {"required": 1, "rangelength": "[2, 50]"},
                        "search": {type: "text"},
                    },
                    // 公共属性字段信息
                    {"data": "created_at", "sName": "created_at", "title": "创建时间", "createdCell": mt.dateTimeString},
                    {"data": "updated_at", "sName": "updated_at", "title": "修改时间", "createdCell": mt.dateTimeString},
                ]
            }
        });
        // 表单初始化
        $(function () {
            news_tables.init();
            pic_tables.init();
        });
</script>	
<?php JsBlock::end() ?>