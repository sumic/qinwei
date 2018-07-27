<?php
/**
 * =======================================================
 * @Description :model 共用行为
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月3日
 * @version: v1.0.0
 */
namespace core\models\mysqldb;

use yii\behaviors\TimestampBehavior;


class AppadminModel extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}

?>