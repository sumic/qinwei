<?php
/**
 * =======================================================
 * @Description :flash message templates
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月17日
 * @version: v1.0.0
 */
use core\widgets\JsBlock;
use appadmin\assets\NotifyAsset;
NotifyAsset::register($this);
?>
<?php JsBlock::begin();?>
<script>
$(document).ready(function(){
<?php 	$corrects = Yii::$service->page->message->getCorrects(); ?>
<?php 	$errors   = Yii::$service->page->message->getErrors(); ?>
<?php 	if((is_array($corrects) && !empty($corrects)) || (is_array($errors) && !empty($errors)   )):  ?>
<?php 		if(is_array($corrects) && !empty($corrects)):  ?>
<?php 			foreach($corrects as $one): ?>
$.notify(
		{	icon: 'fa fa-bell',
			message: "<strong><?=$one?></<strong>" },
		{
			
			placement: {from: "top",align: "center"},
			type: "success",
			template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0} alert-with-icon alert-rose" role="alert">'+
			'<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>'+
			'<i data-notify="icon"></i>'+
			'<span data-notify="title">{1}</span>'+
			'<span data-notify="message">{2}</span>'+
			'<div class="progress" data-notify="progressbar">'+
			'	<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>'+
			'</div>'+
			'<a href="{3}" target="{4}" data-notify="url"></a>'+
		'</div>'
			}
);
<?php			endforeach; ?>
<?php		endif; ?>
<?php 		if(is_array($errors) && !empty($errors)):  ?>
<?php 			foreach($errors as $one): ?>
$.notify(
		{	icon: 'fa fa-bell',
			message: "<strong><?=$one?></<strong>" },
		{
			
			placement: {from: "top",align: "center"},
			type: "danger",
			template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0} alert-with-icon alert-rose" role="alert">'+
			'<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>'+
			'<i data-notify="icon"></i>'+
			'<span data-notify="title">{1}</span>'+
			'<span data-notify="message">{2}</span>'+
			'<div class="progress" data-notify="progressbar">'+
			'	<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>'+
			'</div>'+
			'<a href="{3}" target="{4}" data-notify="url"></a>'+
		'</div>'
			});
<?php			endforeach; ?>
<?php		endif; ?>
<?php 	endif; ?>
});
</script>
<?php JsBlock::end(); ?> 
