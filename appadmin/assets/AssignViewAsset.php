<?php

namespace appadmin\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AssignViewAsset extends AssetBundle
{
    public $sourcePath = '@appadmin/theme/base/default/assets';
    
    public $css = [
        'css/bootstrap.min.css',
        'css/font-awesome.min.css',
        'css/ace-fonts.css',
        'css/chosen.css',
        ['css/ace.min.css','id'=>'main-ace-style'],
        ['css/ace-part2.min.css','condition'=>'lte IE 9'],
        'css/ace-skins.min.css',
        'css/ace-rtl.min.css',
        ['css/ace-ie.min.css','condition'=>'lte IE 9'],
        'js/plugins/sweetalert/sweetalert.css',
    ];
    public $js = [
        'js/bootstrap.min.js',
        'js/ace-extra.min.js',
        ['js/html5shiv.min.js','condition'=>'lte IE 8'],
        ['js/respond.min.js','condition'=>'lte IE 8'],
        'js/ace-elements.min.js',
        'js/ace.min.js',
        'js/common/tools.js',
        'js/plugins/layer/layer.js',
        'js/plugins/sweetalert/sweetalert.min.js',
        'js/chosen.jquery.min.js',
    ];
    public $depends = [
        'appadmin\assets\AppAsset',
    ];
}
