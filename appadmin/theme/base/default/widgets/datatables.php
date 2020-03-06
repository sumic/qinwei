<?php

use yii\helpers\Html;
use appadmin\assets\DataTablesAsset;

DataTablesAsset::register($this);
/**
 * =======================================================
 * @Description :dataTables templates
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月28日
 * @version: v1.0.0
 */
?>
<p <?= Html::renderTagAttributes($defaultButtons) ?>></p>
<table <?= Html::renderTagAttributes($defaultOptions) ?>></table>