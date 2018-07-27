<?php
use yii\helpers\Url;
$this->title = "编辑文章";
?>
<!-- flashmessage -->
<?= $this->render('_form', [
    'model' => $model,
    'options' => $options,
    'status' => $status,
    'commit' => $commit,
    'visable' => $visable,
    'base64_thumb' => $base64_thumb,
]);
