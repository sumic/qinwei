<?php
/**
 * =======================================================
 * @Description :session service config
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月17日
 * @version: v1.0.0
 */
return [
    'session' => [
        'class' => 'core\services\Session',
        // 【下面的三个参数，在使用php session的时候无效】
        //  只有 \Yii::$app->user->enableSession == false的时候才有效。
        //  说的更明确点就是：这些参数的设置是给无状态api使用的。
        //  实现了一个类似session的功能，供appserver端使用
        // 【对phpsession 无效】设置session过期时间,
        'timeout' => 3600,
        // 【对phpsession 无效】当过期时间+session创建时间 - 当前事件 < $updateTimeLimit ,则更新session创建时间
        'updateTimeLimit' => 600,
        // 【不可以设置phpsession】默认为php session，只有当 \Yii::$app->user->enableSession == false时，下面的设置才有效。
        // 存储引擎  mongodb mysqldb redis
        'storage' => 'SessionRedis',  //'SessionMysqldb',
        
        
    ],
];
