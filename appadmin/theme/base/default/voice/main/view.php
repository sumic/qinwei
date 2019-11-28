<?php

use core\widgets\JsBlock;
use yii\widgets\DetailView;
use \backend\widgets\Nestable;
use appadmin\assets\RoleViewAsset;
use appadmin\assets\LoginAsset;

$this->title = '录音详情';
RoleViewAsset::register($this);
LoginAsset::register($this);
/* @var $model \backend\models\Auth */
?>
<!-- flashmessage -->
<?= Yii::$service->page->widget->render('flashmessage'); ?>
<div class="col-xs-12 col-sm-12">
    <div class="col-xs-12 col-sm-12 widget-container-col  ui-sortable">
        <!-- #section:custom/widget-box -->
        <div class="widget-box  ui-sortable-handle">
            <div class="widget-header">
                <h5 class="widget-title"> 文件信息 </h5>
                <!-- #section:custom/widget-box.toolbar -->
                <div class="widget-toolbar">
                    <a class="orange2" data-action="fullscreen" href="#">
                        <i class="ace-icon fa fa-expand"></i>
                    </a>
                    <a data-action="reload" href="#">
                        <i class="ace-icon fa fa-refresh"></i>
                    </a>
                    <a data-action="collapse" href="#">
                        <i class="ace-icon fa fa-chevron-up"></i>
                    </a>
                </div>

                <!-- /section:custom/widget-box.toolbar -->
            </div>

            <div class="widget-body">
                <div class="widget-main">
                <audio controls="controls"><source src="<?= Yii::$app->params['site-img'].$fileinfo->url?>" type="<?=$fileinfo->mime?>"></audio>
                    <?php
                    echo DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'name',
                            'cid',
                            ['label' => '添加时间', 'value' => date('Y-m-d H:i:s', $model->created_at)],
                            ['label' => '修改时间', 'value' => date('Y-m-d H:i:s', $model->updated_at)],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="col-xs-12 col-sm-12">
    <div class="col-xs-12 col-sm-12 widget-container-col  ui-sortable">
        <!-- #section:custom/widget-box -->
        <div class="widget-box  ui-sortable-handle">
            <div class="widget-header">
                <h5 class="widget-title"> 通话内容 </h5>
                <!-- #section:custom/widget-box.toolbar -->
                <div class="widget-toolbar">
                    <a class="orange2" data-action="fullscreen" href="#">
                        <i class="ace-icon fa fa-expand"></i>
                    </a>
                    <a data-action="reload" href="#">
                        <i class="ace-icon fa fa-refresh"></i>
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
                            <?php if($model->content):?>
                        <?php foreach ($model->content as $value): ?>
                        <div class="conversation-start">
                            <span><?=($value->bg)/1000?> 秒 ~ <?=$value->ed/1000?> 秒</span>
                        </div>
                            <div class="alert alert-success" style="padding:10px; margin:5px;">
                                <i class="ace-icon fa fa-microphone bigger-110 red"></i>
                                <?=$value->onebest?>
                            </div>
                            <?php endforeach; ?>
                            <?php else :?>
                            转换中    
                        <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php JsBlock::begin() ?>
<script type="text/javascript">
    $(function() {
        $('.dd').add('.myclass').nestable();
        $('.dd-handle a').on('mousedown', function(e) {
            e.stopPropagation();
        });
    });
</script>

<?php JsBlock::end() ?>
<style>
.conversation-start {
  position: relative;
  width:100%;
  margin-bottom: 10px;
  text-align: center;
}
.conversation-start span {
  font-size: 14px;
  display: inline-block;
  color: grey;
}
.conversation-start span:before, .conversation-start span:after {
  position: absolute;
  top: 10px;
  display: inline-block;
  width: 30%;
  height: 1px;
  content: '';
  background-color:#80808038;
}
.conversation-start span:before {
  left: 10px;
}
.conversation-start span:after {
  right: 10px;
}
</style>