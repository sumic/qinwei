<?php
use yii\helpers\Url;
$this->title = "添加文章";

?>
<!-- flashmessage -->
<?= $this->render('_form', [
    'model' => $model,
    'options' => $options,
    'status' => $status,
    'commit' => $commit,
    'visable' => $visable,
]);
