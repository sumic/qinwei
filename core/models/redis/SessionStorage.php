<?php
/**
 * =======================================================
 * @Description : session redis model
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年7月24日 下午5:30:22
 * @version: v1.0.0
 */

namespace core\models\redis;

use yii\redis\ActiveRecord;

class SessionStorage extends ActiveRecord
{
    public static function primaryKey()
    {
        return ['id'];
    }
    
    public function attributes()
    {
        return [
            'id', 'session_uuid',
            'session_key', 'session_value',
            'session_timeout','session_updated_at'
        ];
    }
    /**
     * relations can not be defined via a table as there are not tables in redis. 
     * You can only define relations via other records.
     */
}
