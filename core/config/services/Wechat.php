<?php
/**
 * =======================================================
 * @Description :wechat service config
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年07月31日 15:59:00
 * @version: v1.0.0
 */
return [
    'mpwechat' => [
        'class' => 'core\services\Wechat',
        // 子服务
        'childService' => [
	         'api' => [
                'class'  => 'core\services\mpwechat\Api',
            ],
            'base' => [
                'class'  => 'core\services\mpwechat\Base',
            ],
            'menu' => [
                'class'  => 'core\services\mpwechat\Menu',
            ]
        ],
    ],
];


