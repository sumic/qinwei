<?php
/**
 * =======================================================
 * @Description :admin service config
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
return [
    'cms' => [
        'class' => 'core\services\Cms',
        // 子服务
        'childService' => [
            'article' => [
                'class'            => 'core\services\cms\Article',
                'storage' => 'ArticleMysqldb', // ArticleMysqldb or ArticleMongodb.
            ],
            'category' => [
                'class'            => 'core\services\cms\Category',
            ],
	    'tags' => [
                'class'            => 'core\services\cms\Tags',
            ],
        ],
    ],
];


