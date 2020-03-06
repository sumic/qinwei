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
    'admin' => [
        'class' => 'core\services\Admin',
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
            'user' => [
                'class'      => 'core\services\admin\AdminUser',
            ],
            'userlogin' => [
                'class'      => 'core\services\admin\AdminUserLogin',
            ],
            'assign' => [
                'class'      => 'core\services\admin\AdminAssign',
            ],
            'rule' => [
                'class'      => 'core\services\admin\AdminRule',
            ],
            'logs' => [
                'class'      => 'core\services\admin\AdminLog',
            ],
            'upload' => [
                'class'      => 'core\services\admin\AdminUpload',
            ],
        ],
    ],
];

