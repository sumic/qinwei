<?php

use yii\helpers\Json;
use \backend\models\Auth;
use core\widgets\JsBlock;
use yii\helpers\Url;
// 获取权限
//$auth = Auth::getDataTableAuth('auth-rule');

// 定义标题和面包屑信息
$this->title = '素材管理';
?>
<div class="tabbable">

 <ul id="myTab" class="nav nav-tabs">
   <li class="active">
      <a href="#new" data-toggle="tab">图文消息</a>
   </li>
   <li>
      <a href="#pic" data-toggle="tab">图片</a>
   </li>
   <li>
      <a href="#voice" data-toggle="tab">语音</a>
   </li>
   <li>
      <a href="#video" data-toggle="tab">视频</a>
   </li>
 </ul>

 <div class="tab-content">
   <div class="tab-pane in active" id="new">
     news
   </div>
   <div class="tab-pane" id="pic">
     pic
   </div>
   <div class="tab-pane" id="voice">
    voice
   </div>
   <div class="tab-pane" id="video">
     video
   </div>
 </div>

</div>
<?php JsBlock::begin() ?>

<?php JsBlock::end() ?>