<?php

namespace appadmin\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $sourcePath = '@appadmin/theme/base/default/assets';
    public $css = [
     //   'css/site.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'appadmin\assets\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
