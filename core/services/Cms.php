<?php
/**
 * =======================================================
 * @Description :cms service 
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月18日
 * @version: v1.0.0
 */

namespace core\services;

class Cms extends Service
{
    /**
     * cms storage db, you can set value: mysqldb,mongodb.
     */
    public $storage = 'mysqldb';
}
