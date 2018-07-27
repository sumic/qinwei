<?php
use yii\bootstrap\ActiveForm;
use appadmin\widgets\Ueditor;
use appadmin\assets\CmsAsset;
use core\widgets\JsBlock;
use yii\helpers\Url;
use yii\helpers\Html;
CmsAsset::register($this);
$fieldOptions1 = [
    'labelOptions' => [
        'class' => 'col-sm-2 control-label '
    ],
    'inputOptions' => [
        'class' => 'col-xs-10 col-sm-5'
    ],
    'template' => "{label}\n<div class='col-sm-9'>{input} {error}</div>"
];

$fieldOptions2 = [
    'labelOptions' => [
        'class' => 'col-sm-2 control-label no-padding-right'
    ],
    'inputOptions' => [
        'class' => 'col-xs-10'
    ],
    'template' => "{label}\n<div class='col-sm-10'>{input} {error}</div>"
];

$fieldOptions3 = [
    'labelOptions' => [
        'class' => 'col-sm-3 control-label '
    ],
    'inputOptions' => [
        'class' => 'col-xs-9'
    ],
    'template' => "{label}\n<div class='col-sm-9'>{input} {error}</div>"
];

$fieldPassword = [
    'options' => [
        'class' => 'form-group',
        'style' => 'display:none;'
    ],
    'labelOptions' => [
        'class' => 'col-sm-3 control-label'
    ],
    'inputOptions' => [
        'class' => 'col-xs-9'
    ],
    'template' => "{label}\n<div class='col-sm-9'>{input} {error}</div>"
];


$checkboxOptions = [
    'options' => [
        'class' => 'from-group col-sm-4'
    ],
    'inputTemplate' => "{input}",
    'checkboxTemplate' => "{input}\n<span class='lbl'>&nbsp;&nbsp;{label}</span>"
];

$dropdownOptions = [
    'options' => [
        'class' => 'from-group col-sm-4 control-label no-padding-right'
    ],
];

$chosenOptions = [
    'options' => [
        'class' => 'no-padding-right'
    ],
];

?>
<?php
$form = ActiveForm::begin([
    'action' => ['create'],
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'form-horizontal'
    ]
]);
?>
<?= Yii::$service->page->widget->render('flashmessage');?>
<!-- left start-->
<div class="col-xs-12 col-sm-8">
	<div class="widget-box">
		<div class="widget-header">
			<h4 class="widget-title">基本信息</h4>

			<div class="widget-toolbar">
				<a href="#" data-action="collapse"> <i
					class="ace-icon fa fa-chevron-up"></i>
				</a>
			</div>
		</div>

		<div class="widget-body" style="display: block;">
			<div class="widget-main">
				<?= Html::activeHiddenInput($model,'id') ?>
				<?= $form->field($model, 'title',$fieldOptions1)->textInput(); ?>
				<?= $form->field($model, 'sub_title',$fieldOptions1)->textInput(); ?>
                <?= $form->field($model, 'summary',$fieldOptions2)->textArea(); ?>
                <?= $form->field($model, 'thumb',$fieldOptions1)->fileInput() ?>
                <?= $form->field($model, 'content',$fieldOptions2)->widget(Ueditor::className()) ?>
                
			</div>
		</div>
	</div>
</div>
<!-- left end -->

<!-- right start -->
<!-- 分类开始 -->
<div class="col-xs-12 col-sm-4">
	<div class="widget-box">
		<div class="widget-header">
			<h4 class="widget-title">文章分类</h4>

			<div class="widget-toolbar">
				<a href="#" data-action="collapse"> <i
					class="ace-icon fa fa-chevron-up"></i>
				</a>
			</div>
		</div>

		<div class="widget-body" style="display: block;">
			<div class="widget-main">
				<div>
					
					<?= $form->field($model, 'cid', $chosenOptions)->label(false)->dropDownList($options,['prompt' => '选择文章分类...','class'=>'chosen-select','encode'=>false]); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- 分类结束 -->

<!-- 属性开始 -->
<div class="col-xs-12 col-sm-4">
	<div class="widget-box">
		<div class="widget-header">
			<h4 class="widget-title">文章属性</h4>

			<div class="widget-toolbar">
				<a href="#" data-action="collapse"> <i
					class="ace-icon fa fa-chevron-up"></i>
				</a>
			</div>
		</div>

		<div class="widget-body" style="display: block;">
			<div class="widget-main">
    				<?= $form->field($model, 'flag_headline',$checkboxOptions)->checkbox(['class'=>'ace ace-switch ace-switch-4 btn-rotate']); ?>
    				<?= $form->field($model, 'flag_recommend',$checkboxOptions)->checkbox(['class'=>'ace ace-switch ace-switch-4 btn-rotate']); ?>
    				<?= $form->field($model, 'flag_special_recommend',$checkboxOptions)->checkbox(['class'=>'ace ace-switch ace-switch-4 btn-rotate']); ?>
    				<?= $form->field($model, 'flag_slide_show',$checkboxOptions)->checkbox(['class'=>'ace ace-switch ace-switch-4 btn-rotate']); ?>
    				<?= $form->field($model, 'flag_roll',$checkboxOptions)->checkbox(['class'=>'ace ace-switch ace-switch-4 btn-rotate']); ?>
    				<?= $form->field($model, 'flag_bold',$checkboxOptions)->checkbox(['class'=>'ace ace-switch ace-switch-4 btn-rotate']); ?>
    				<?= $form->field($model, 'flag_picture',$checkboxOptions)->checkbox(['class'=>'ace ace-switch ace-switch-4 btn-rotate']); ?>
    			<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
<!-- 属性结束 -->
<!-- SEO开始 -->
<div class="col-xs-12 col-sm-4">
	<div class="widget-box">
		<div class="widget-header">
			<h4 class="widget-title">SEO 设置</h4>

			<div class="widget-toolbar">
				<a href="#" data-action="collapse"> <i
					class="ace-icon fa fa-chevron-up"></i>
				</a>
			</div>
		</div>

		<div class="widget-body" style="display: block;">
			<div class="widget-main">
			<?= $form->field($model, 'seo_title' ,$fieldOptions3)->textInput(); ?>
			<?= $form->field($model, 'seo_keywords' ,$fieldOptions3)->textInput(); ?>
			<?= $form->field($model, 'seo_description' ,$fieldOptions3)->textInput(); ?>
    			<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
<!-- SEO结束 -->
<!-- 其他设置 -->
<div class="col-xs-12 col-sm-4">
	<div class="widget-box">
		<div class="widget-header">
			<h4 class="widget-title">其他设置</h4>

			<div class="widget-toolbar">
				<a href="#" data-action="collapse"> <i
					class="ace-icon fa fa-chevron-up"></i>
				</a>
			</div>
		</div>

		<div class="widget-body" style="display: block;">
			<div class="widget-main">
			<?= $form->field($model, 'tag' ,$fieldOptions3)->textInput(['placeholder'=>'输入标签...']); ?>
			<?= $form->field($model, 'sort' ,$fieldOptions3)->textInput(); ?>
			<?= $form->field($model, 'author_name' ,$fieldOptions3)->textInput(); ?>
			<?= $form->field($model, 'password' ,$fieldPassword)->textInput(); ?>
			<?= $form->field($model, 'status', $dropdownOptions)->label(false)->dropDownList($status,['prompt' => '状态']); ?>
			<?= $form->field($model, 'can_comment', $dropdownOptions)->label(false)->dropDownList($commit,['prompt' => '评论']); ?>
			<?= $form->field($model, 'visibility', $dropdownOptions)->label(false)->dropDownList($visable,['prompt' => '可见性']); ?>
			
    			<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
<!-- 其他结束 -->
<!-- 提交 -->
<div class="col-xs-12 col-sm-4">
	<div class="widget-box">
		<div class="widget-header">
			<h4 class="widget-title">提交</h4>

			<div class="widget-toolbar">
				<a href="#" data-action="collapse"> <i
					class="ace-icon fa fa-chevron-up"></i>
				</a>
			</div>
		</div>

		<div class="widget-body" style="display: block;">
			<div class="widget-main">
			 <?= $form->field($model, 'type',['template'=>'{input}'])->label(false)->hiddenInput(['value'=>1]); ?>
    		<button class="btn btn-info" type="submit">
    			<i class="ace-icon fa fa-check bigger-110"></i> 发布
    		</button>
    
    		&nbsp; &nbsp; &nbsp;
    		<button class="btn" type="reset">
    			<i class="ace-icon fa fa-undo bigger-110"></i> 重置
    		</button>
			</div>
		</div>
	</div>
</div>
<!-- right end -->

<?php $form = ActiveForm::end() ?>
<?php JsBlock::begin()?>
<script type="text/javascript">
	jQuery(function($) {
		$('.chosen-select').chosen({
			allow_single_deselect:false,
			no_results_text: "没有找到相关栏目",
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

		//password input
		if($("#article-visibility").val() == 3){
			$(".field-article-password").show();
			} 
		$("#article-visibility").on('change',function(){
			$(".field-article-password").hide();
				if($(this).val() == 3){
					$(".field-article-password").show();
				}
			})
		//tag input
		var tag_input = $('#article-tag');
		var queryUrl = '<?=Url::toRoute('tags/search',true)?>';
		var tagArray = [];
		try{
		   tag_input.tag({
		      placeholder: tag_input.attr('placeholder'),

		       source: function(query, process) {
		         $.ajax({
			         url: queryUrl,
			         type:'post',
			         data:'params[name]='+encodeURIComponent(query)
			         })
		          .done(function(result_items){
		        	  $.each(result_items.data.aaData,function(i,e){
			        	  tagArray.push(e.name);
			        	  });
		             process(tagArray);
		             tagArray = [];
		         });
		      } 
		   });
		   $("#article-tag").next("input").attr('class','col-xs-9')
		}
		catch(e) {
		   //display a textarea for old IE, because it doesn't support this plugin or another one I tried!
		   tag_input.after('<textarea id="'+tag_input.attr('id')+'" name="'+tag_input.attr('name')+'" rows="3">'+tag_input.val()+'</textarea>').remove();
		}
		// file input
		$('#article-thumb').ace_file_input({
			style:'well',
			btn_choose:'点击或拖动文件到此上传',
			btn_change:null,
			no_icon:'ace-icon fa fa-cloud-upload',
			droppable:true,
			allowExt:  ['jpg', 'jpeg', 'png', 'gif', 'tif', 'tiff', 'bmp'],
		    allowMime: ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/tif', 'image/tiff', 'image/bmp'],//html5 browsers only
			thumbnail:'fit',
		}).on('change', function() {
			//上传未完成不能提交
			$(":submit").attr('disabled',true);
			var ie_timeout = null;
			var sFileUploadUrl = '<?=Url::to(['/uploads/main/uploads', 'sField' => 'Article[thumb]','sType'=>'image']);?>';
	        var deferred = aceFileInputAjax($('#article-thumb'), sFileUploadUrl );
	        // 成功执行
	        deferred.done(function(json) {
	            if (json.state == 'SUCCESS') {
	                swal({title:"上传文件成功!",text:json.url,type:'success'});
	                $('input[type=hidden][name="Article[thumb]"]').val(json.url);
	            } else {
	                swal({title:"上传文件错误!",text:json.state,type:'error'});
	                $('#article-thumb').ace_file_input('apply_settings').ace_file_input('reset_input');
	            }
	            $(":submit").attr('disabled',false);
	        }).fail(function() {
	            ajaxFail();
	        }).always(function() {
	            if(ie_timeout) clearTimeout(ie_timeout);
	            ie_timeout = null;
	            $('#article-thumb').ace_file_input('loading', false);
	        });

	        deferred.promise();
	        // 错误处理
	    }).on('file.error.ace', function(event, info) {
	        // 判断错误
	        swal({title:"文件上传出现错误",text:validateFile(info),type:'error'});
	        event.preventDefault();
	    });

	    <?php if($model->thumb):?>
		$("input[type='hidden'][name='Article[thumb]']").val('<?=$model->thumb?>');
		$('#article-thumb').ace_file_input("show_file_list", [{type: 'image', name: '缩略图', path: '<?=$base64_thumb?>'}]);
		<?php endif;?>
		})
</script>
<?php JsBlock::end()?>