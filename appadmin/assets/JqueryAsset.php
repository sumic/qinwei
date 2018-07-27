<?php

namespace appadmin\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class JqueryAsset extends AssetBundle
{
    public $sourcePath = '@appadmin/theme/base/default/assets';
    
    public $js = [
        ['js/jquery.min.js','condition'=>'!IE'],
        ['js/jquery1x.min.js','condition'=>'IE']
    ];
}
