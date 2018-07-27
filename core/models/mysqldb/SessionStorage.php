<?php
/**
 * =======================================================
 * @Description :php session model
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月17日
 * @version: v1.0.0
 */

namespace core\models\mysqldb;

use yii\db\ActiveRecord;

class SessionStorage extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%session_storage}}';
    }
}
