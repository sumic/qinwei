<?php

namespace appadmin\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class RoleViewAsset extends AssetBundle
{
    public $sourcePath = '@appadmin/theme/base/default/assets';
    
    public $css = [
    ];
    public $js = [
        'js/jquery.nestable.min.js',
    ];
    public $depends = [
        'appadmin\assets\AceAsset',
    ];
}
