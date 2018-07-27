<?php
/**
 * =======================================================
 * @Description :URL service config
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月17日
 * @version: v1.0.0
 */
return [
    'url' => [
        'class'        => 'core\services\Url',
        'showScriptName'=> false, // if is show index.php in url.  if set false ,you must config nginx rewrite
        'randomCount'=> 8,  // if url key  is exist in url write table ,  add a random string  behide the url key, this param is define random String length
        // 子服务
        'childService' => [
            'rewrite' => [
                'class' => 'core\services\url\Rewrite',
                'storage' => 'RewriteMongodb',
            ],
            'category' => [
                'class' => 'core\services\url\Category',

            ],
        ],
    ],
];
