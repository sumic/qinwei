<?php
/**
 * =======================================================
 * @Description :后台配置文件
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月2日
 * @version: v1.0.0
 *
 */

// 本文件在app/web/index.php 处引入。
// 核心模块
$modules = [];
foreach (glob(__DIR__ . '/modules/*.php') as $filename) {
    $modules = array_merge($modules, require($filename));
}
$params = require __DIR__ .'/params.php';
return [
    'modules'=>$modules,
    /* only config in front web */
    //'bootstrap' => ['store'],
    'params'    => $params,
    'components' => [
        'user' => [
            'identityClass' => 'core\models\mysqldb\admin\AdminUser',
            'enableAutoLogin' => true,
        ],
    ],
    
];
