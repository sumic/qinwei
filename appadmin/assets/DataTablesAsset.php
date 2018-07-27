<?php

namespace appadmin\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class DataTablesAsset extends AssetBundle
{
    public $sourcePath = '@appadmin/theme/base/default/assets';
    
    public $css = [
    ];
    public $js = [
        'js/common/meTables.js',
        'js/jquery.dataTables.min.js',
        'js/jquery.dataTables.bootstrap.js',
        'js/jquery.validate.min.js',
        'js/validate.message.js'
    ];
    public $depends = [
        //notice js
        'appadmin\assets\LoginAsset',
    ];
}
