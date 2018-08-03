<?php

namespace appadmin\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class WechatAsset extends AssetBundle
{
    public $sourcePath = '@appadmin/theme/base/default/assets';
    public $css = [
         'css/bootstrap.min.css',
         'css/font-awesome.min.css',
         'wechat/hs_self_menu.css',
         'wechat/hs_wx_base.css',
        'js/plugins/sweetalert/sweetalert.css',
    ];
    public $js = [
         'js/bootstrap.min.js',
        ['js/html5shiv.min.js','condition'=>'lte IE 8'],
        ['js/respond.min.js','condition'=>'lte IE 8'],
        'js/plugins/layer/layer.js',
        'js/plugins/sweetalert/sweetalert.min.js',
        'wechat/hs_js_common.js',
        'wechat/hs_selfmenu.js',
         
    ];
    public $depends = [
        'appadmin\assets\CmsAsset',
    ];
}
