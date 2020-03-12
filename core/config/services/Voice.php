<?php

/**
 * =======================================================
 * @Description :voice service config
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
return [
    'voice' => [
        'class' => 'core\services\Voice',
        // 子服务
        'childService' => [
            'playback' => [
                'class'            => 'core\services\voice\Playback',
            ],
            'console' => [
                'class'            => 'core\services\voice\ConsolePlayback',
            ],
            'xfyun' => [
                'class'            => 'core\services\voice\Api',
                'appId'            => '5dd4f7a8',
                'secretKey'        => '63a24911754f1cb539aaa81c21623e79'
            ]
        ],
    ],
];
