<?php

namespace appadmin\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class PageBlankAsset extends AssetBundle
{
    public $sourcePath = '@appadmin/theme/base/default/assets';
    
    public $css = [
        'js/plugins/tabs/tabs.css',
        'js/plugins/pace-master/themes/orange/pace-theme-flash.css',
        'css/site.css',
    ];
    public $js = [
        'js/plugins/tabs/tabs.js',
        'js/plugins/pace-master/pace.min.js',
    ];
    public $depends = [
        'appadmin\assets\AceAsset',
    ];
}
