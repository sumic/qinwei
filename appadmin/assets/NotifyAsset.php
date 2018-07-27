<?php

namespace appadmin\assets;

use yii\web\AssetBundle;

/**
 * notify asset bundle.
 */
class NotifyAsset extends AssetBundle
{
    public $sourcePath = '@appadmin/theme/base/default/assets';
    
    public $css = [
        'css/animate.min.css',
        'css/site.css',
    ];
    public $js = [
        'js/plugins/bootstrap-notify-master/bootstrap-notify.min.js',
    ];
    public $depends = [
        'appadmin\assets\AppAsset',
    ];
}
