<?php

use core\widgets\JsBlock;
use yii\widgets\DetailView;
use \backend\widgets\Nestable;
use yii\bootstrap\ActiveForm;
use appadmin\assets\RoleViewAsset;
use appadmin\assets\LoginAsset;
use yii\helpers\Html;

$this->title = '录音详情';
RoleViewAsset::register($this);
LoginAsset::register($this);
/* @var $model \backend\models\Auth */
$checkboxOptions = [
    'options' => [
        'class' => 'from-group col-sm-6'
    ],
    'inputTemplate' => "{input}",
    'checkboxTemplate' => "{input}\n<span class='lbl'>&nbsp;&nbsp;{label}</span>"
];
?>
<!-- flashmessage -->
<?= Yii::$service->page->widget->render('flashmessage'); ?>

<div class="col-xs-8 col-sm-8 widget-container-col min-height">
    <!-- #section:custom/widget-box -->
    <div class="widget-box  ui-sortable-handle">
        <div class="widget-header">
            <h5 class="widget-title"> 通话内容 </h5>
            <!-- #section:custom/widget-box.toolbar -->
            <div class="widget-toolbar">
                <a class="orange2" data-action="fullscreen" href="#">
                    <i class="ace-icon fa fa-expand"></i>
                </a>

                <a data-action="collapse" href="#">
                    <i class="ace-icon fa fa-chevron-up"></i>
                </a>
            </div>

            <!-- /section:custom/widget-box.toolbar -->
        </div>

        <div class="widget-body">
            <div class="widget-main">
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <?php if ($model->content) : ?>
                            <?php foreach ($model->content as $value) : ?>
                                <div class="conversation-start">
                                    <span><?= ($value->bg) / 1000 ?> 秒 ~ <?= $value->ed / 1000 ?> 秒</span>
                                </div>
                                <?php if ($value->speaker == 1) : ?>
                                    <div class="message">
                                        <div class="content">
                                            <i class="ace-icon fa fa-microphone bigger-110 playaudio" starttime="<?= ($value->bg) / 1000 ?>"></i>
                                            <?= $value->onebest ?>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div class="message fromme">
                                        <div class="content">
                                            <i class="ace-icon fa fa-microphone bigger-110 playaudio" starttime="<?= ($value->bg) / 1000 ?>"></i>
                                            <?= $value->onebest ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else : ?>
                            转换中
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-xs-4 col-sm-4 widget-container-col ui-sortable">
    <!-- #section:custom/widget-box -->
    <div class="widget-box  ui-sortable-handle">
        <div class="widget-header">
            <h5 class="widget-title"> 敏感词 </h5>
            <!-- #section:custom/widget-box.toolbar -->
            <div class="widget-toolbar">
                <a class="orange2" data-action="fullscreen" href="#">
                    <i class="ace-icon fa fa-expand"></i>
                </a>

                <a data-action="collapse" href="#">
                    <i class="ace-icon fa fa-chevron-up"></i>
                </a>
            </div>

            <!-- /section:custom/widget-box.toolbar -->
        </div>
        <div class="widget-body">
            <div class="widget-main">
                <?php if ($model->sensitive) : ?>
                    <?php foreach ($model->sensitive as $value) : ?>
                        <span class="label label-danger arrowed-in playaudio" starttime="<?= ($value->bg) / 1000 ?>"><?= $value->keywords[0] ?></span>
                    <?php endforeach; ?>
                <?php else : ?>
                    <span class="label label-success arrowed">无敏感词</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="col-xs-4 col-sm-4 widget-container-col ui-sortable">
    <!-- #section:custom/widget-box -->
    <div class="widget-box  ui-sortable-handle">
        <div class="widget-header">
            <h5 class="widget-title"> 文件信息 </h5>
            <!-- #section:custom/widget-box.toolbar -->
            <div class="widget-toolbar">
                <a class="orange2" data-action="fullscreen" href="#">
                    <i class="ace-icon fa fa-expand"></i>
                </a>

                <a data-action="collapse" href="#">
                    <i class="ace-icon fa fa-chevron-up"></i>
                </a>
            </div>

            <!-- /section:custom/widget-box.toolbar -->
        </div>

        <div class="widget-body">
            <div class="widget-main">
                <audio controls="controls" id="players">
                    <source src="<?= Yii::$app->params['site-img'] . $fileinfo->url ?>" type="<?= $fileinfo->mime ?>"></audio>
                <?php
                echo DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'name',
                        'cid',
                        ['label' => '创建时间', 'value' => date('Y-m-d H:i:s', $model->created_at)],
                        ['label' => '更新时间', 'value' => date('Y-m-d H:i:s', $model->updated_at)],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
<div class="col-xs-4 col-sm-4 widget-container-col ui-sortable">
    <!-- #section:custom/widget-box -->
    <div class="widget-box  ui-sortable-handle">
        <div class="widget-header">
            <h5 class="widget-title"> 操作 </h5>
            <!-- #section:custom/widget-box.toolbar -->
            <div class="widget-toolbar">
                <a class="orange2" data-action="fullscreen" href="#">
                    <i class="ace-icon fa fa-expand"></i>
                </a>

                <a data-action="collapse" href="#">
                    <i class="ace-icon fa fa-chevron-up"></i>
                </a>
            </div>

            <!-- /section:custom/widget-box.toolbar -->
        </div>
        <?php
        $form = ActiveForm::begin([
            'action' => ['update'],
            'options' => [
                'class' => 'form-horizontal'
            ]
        ]);
        ?>
        <div class="widget-body">
            <div class="widget-main">
            <?= Html::activeHiddenInput($model,'id') ?>

                <?= $form->field($model, 'is_checked', $checkboxOptions)->checkbox(['class' => 'ace ace-switch ace-switch-4 btn-rotate']); ?>
                <div style="clear:both;text-align:center"><button class="btn btn-info" type="submit">
                    <i class="ace-icon fa fa-check bigger-110"></i> 提交
                </button></div>
            </div>
        </div>
        <?php $form = ActiveForm::end() ?>

    </div>
</div>

<?php JsBlock::begin() ?>
<script type="text/javascript">
    $(function() {
        $('.dd').add('.myclass').nestable();
        $('.dd-handle a').on('mousedown', function(e) {
            e.stopPropagation();
        });
        $(".playaudio").click(function() {
            $("#players")[0].pause();
            $("#players")[0].currentTime = $(this).attr('starttime');
            $("#players")[0].play();
        })
    });
</script>

<?php JsBlock::end() ?>
<style>
    .miniheight {
        min-height: 70%
    }

    .playaudio {
        cursor: pointer
    }

    .conversation-start {
        position: relative;
        width: 100%;
        margin-bottom: 10px;
        text-align: center;
    }

    .conversation-start span {
        font-size: 14px;
        display: inline-block;
        color: grey;
    }

    .conversation-start span:before,
    .conversation-start span:after {
        position: absolute;
        top: 10px;
        display: inline-block;
        width: 30%;
        height: 1px;
        content: '';
        background-color: #80808038;
    }

    .conversation-start span:before {
        left: 10px;
    }

    .conversation-start span:after {
        right: 10px;
    }

    .content_a {
        max-width: 70%;
        width: fit-content;
        height: fit-content;
        background-color: #f4f4f4;
        padding: 15px;
        border-radius: 0 10px 10px 10px;
        margin-left: 30px;
        color: #626c76;
    }

    .content_b {
        max-width: 70%;
        width: fit-content;
        height: fit-content;
        border-radius: 10px 0px 10px 10px;
        margin-left: unset;
        margin-right: 30px;
        background-color: #1f6945;
        color: #fff;
    }

    .fromme {
        flex-direction: row-reverse;

    }

    .message {
        display: flex;
        margin: 20px;
        position: relative;
    }

    .message .content {
        font-size: 14px;
        max-width: 70%;
        width: fit-content;
        height: fit-content;
        background-color: #f4f4f4;
        padding: 15px;
        border-radius: 0 10px 10px 10px;
        margin-left: 30px;
        color: #626c76;
    }

    .message.fromme .content {
        background-color: #1f6945;
        color: #fff;
        border-radius: 10px 0px 10px 10px;
        margin-left: unset;
        margin-right: 30px;
    }

    .message:after {
        content: '';
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 20px 15px 0;
        border-color: transparent #f4f4f4 transparent transparent;
        position: absolute;
        left: 10px;
    }

    .message.fromme:after {
        border-width: 15px 20px 0 0;
        border-color: #1f6945 transparent transparent transparent;
        right: 10px;
        left: unset;
    }
</style>