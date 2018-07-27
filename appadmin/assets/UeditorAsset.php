<?php

namespace appadmin\assets;

use yii\web\AssetBundle;

/**
 * ueditor asset bundle.
 */
class UeditorAsset extends AssetBundle
{
    public $sourcePath = '@appadmin/theme/base/default/assets/js/plugins/ueditor';
    
    public $js = [
        'ueditor.all.min.js',
    ];
    
    public $publishOptions = [
        'except' => [
            'php/',
            'index.html',
            '.gitignore'
        ]
    ];
}
