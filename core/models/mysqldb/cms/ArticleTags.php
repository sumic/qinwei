<?php
/**
 * =======================================================
 * @Description :cms article tag mysqldb model
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月18日
 * @version: v1.0.0
 */

namespace core\models\mysqldb\cms;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class ArticleTags extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article_tags}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aid'], 'required'],
            [['aid', 'created_at'], 'integer'],
            [['key','name'], 'string', 'max' => 255],
        ];
    }
}
