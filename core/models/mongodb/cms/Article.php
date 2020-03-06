<?php
/**
 * =======================================================
 * @Description :cms article mongodb model
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月18日
 * @version: v1.0.0
 */

namespace core\models\mongodb\cms;

use yii\mongodb\ActiveRecord;

class Article extends ActiveRecord
{
    
    const STATUS_DELETED = 10;
    const STATUS_ACTIVE = 1;
    
    /**
     * mongodb collection 的名字，相当于mysql的table name
     */
    public static function collectionName()
    {
        return 'article';
    }
    /**
     * mongodb是没有表结构的，因此不能像mysql那样取出来表结构的字段作为model的属性
     * 因此，需要自己定义model的属性，下面的方法就是这个作用
     */
    public function attributes()
    {
        return [
            '_id',
            'url_key',
            'title',
            'meta_keywords',
            'meta_description',
            'content',
            'status',
            'created_at',
            'updated_at',
            'created_user_id',

        ];
    }
    /**
     * 给model对应的表创建索引的方法
     * 在indexs数组中填写索引，如果有多个索引，可以填写多行
     * 在migrate的时候会运行创建索引，譬如：
     * @fecshop/migrations/mongodb/m170228_072455_fecshop_tables
     */
    public static function create_index()
    {
        $indexs = [
            ['url_key'        => -1],

        ];

        $options = ['background' => true];
        foreach ($indexs as $columns) {
            self::getCollection()->createIndex($columns, $options);
        }
    }
}
