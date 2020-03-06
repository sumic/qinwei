<?php
/**
 * =======================================================
 * @Description :servicelog config
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
return [
    'serviceLog' => [
        'class' => 'core\components\ServiceLog',
        'log_config' => [
            // service log config
            'services' => [
                // if enable is false , all services will be close
                'enable' => false,
                // print log info to db.
                'dbprint'        => false,
                // print log info to front html
                'htmlprint'    => false,
                // print log
                'htmlprintbyparam'  => [
                    // like :http://fecshop.appfront.fancyecommerce.com/cn/?servicelog=xxxxxxxx
                    'enable'        => false,
                    'paramKey'        => 'servicelog',
                    'paramVal'            => 'xxxxxxxx',
                ],
            ],
        ],
    ],
];