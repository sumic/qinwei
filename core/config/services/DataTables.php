<?php
/**
 * =======================================================
 * @Description :admin service config
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
return [
    'datatables' => [
        'class' => 'core\services\DataTables',
        // 子服务
        'childService' => [
            'logs' => [
                'class'      => 'core\services\admin\AdminLogs',
                'storage'    => 'mysqldb', // ArticleMysqldb or ArticleMongodb.
            ],

            'role' => [
                'class'      => 'core\services\admin\AdminRole',
            ],
            'menu' => [
                'class'      => 'core\services\admin\AdminMenu',
            ],
        ],
    ],
];

