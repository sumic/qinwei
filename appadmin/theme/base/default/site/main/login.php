<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$fieldOptions3 = [
    'options' => [
        'class' => 'row form-group col-xs-7'
    ],
    'inputTemplate' => "{input}"
];
?>
<?= Yii::$service->page->widget->render('flashmessage'); ?>
<?php $form = ActiveForm::begin(); ?>
<fieldset>
    <label class="block clearfix">
        <span class="block input-icon input-icon-right">
            <?= $form->field($model, 'username')->textInput(['placeholder' => $model->getAttributeLabel('username')])->label(false) ?>
            <i class="ace-icon fa fa-user"></i>
        </span>
    </label>

    <label class="block clearfix">
        <span class="block input-icon input-icon-right">
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->getAttributeLabel('password')])->label(false) ?>
            <i class="ace-icon fa fa-lock"></i>
        </span>
    </label>
    <?php if(Yii::$app->params['admin_verifyCode'] == true):?>
    <label class="block clearfix">
        <span class="block input-icon input-icon-right">
			<?= $form->field($model, 'verifyCode',$fieldOptions3)->textInput(['placeholder' => $model->getAttributeLabel('verifyCode')])->label(false) ?> 
         <div class="col-xs-5">
					<img class="login-captcha-img" title="点击刷新"
						src="<?= Yii::$service->url->getUrl('site/main/captcha'); ?>"
						align="absbottom"
						onclick="this.src='<?= Yii::$service->url->getUrl('site/main/captcha'); ?>?'+Math.random();"></img>
					<i class="refresh-icon"></i>
				</div>
        </span>
    </label>
     <?php endif;?>
    <div class="space"></div>
    <div class="clearfix">
        <label class="inline">
            <?= $form->field($model, 'rememberMe')->checkbox() ?>
        </label>
        <?= Html::submitButton('登录', ['class' => 'btn bg-olive btn-block width-35 pull-right btn btn-sm btn-primary']) ?>
    </div>
    <div class="space-4"></div>
</fieldset>
<?php ActiveForm::end(); ?>
 
								
