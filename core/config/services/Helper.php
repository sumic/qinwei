<?php
/**
 * =======================================================
 * @Description :helper service config
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月17日
 * @version: v1.0.0
 */
return [
    'helper' => [
        'class' => 'core\services\Helper',
        // 子服务
        'childService' => [
            'ar' => [
                'class' => 'core\services\helper\AR',
            ],
            'errors' => [
                'class' => 'core\services\helper\Errors',
            ],
            'captcha' => [
                'class'        => 'core\services\helper\Captcha',
                'codelen'        => 4,  //验证码长度
                'width'        => 130, //宽度
                'height'        => 50, //高度
                'fontsize'        => 20, //子体大小
                'case_sensitive'=> false, // 是否区分大小写，false代表不区分
            ],
            'tree' => [
                'class' => 'core\services\helper\Tree',
            ],
            'json' => [
                'class' => 'core\services\helper\Json',
            ],
            'base64img' => [
                'class' => 'core\services\helper\Base64Img',
            ],
            'uploader' => [
                'class' => 'core\services\helper\Uploader',
                'config'=> file_exists((__DIR__ . '/uploads.json')) ? json_decode(preg_replace("/\/\*[\s\S]+?\*\//", '', file_get_contents(__DIR__ . '/uploads.json')), true) :'',
            ],
            'appapi' => [
                'class' => 'core\services\helper\Appapi',
            ],
        ],
    ],
];
