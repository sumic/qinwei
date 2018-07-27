<?php

namespace appadmin\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class RoleEditAsset extends AssetBundle
{
    public $sourcePath = '@appadmin/theme/base/default/assets';
    
    public $css = [
        'js/jstree/default/style.css',
    ];
    public $js = [
        'js/jstree/jstree.min.js',
    ];
    public $depends = [
        'appadmin\assets\AceAsset',
    ];
}
