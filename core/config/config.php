<?php
/**
 * =======================================================
 * @Description :services配置文件
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
// 本文件在app/web/index.php 处引入。
// 服务
$services = [];
foreach (glob(__DIR__ . '/services/*.php') as $filename) {
    $services = array_merge($services, require($filename));
}

// 组件
$components = [];
foreach (glob(__DIR__ . '/components/*.php') as $filename) {
    $components = array_merge($components, require($filename));
}

return [
    'components'    => $components,
    'services'        => $services,
    'params'        => [
        
    ],
];
?>
