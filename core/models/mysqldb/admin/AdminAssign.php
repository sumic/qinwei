<?php
/**
 * =======================================================
 * @Description :权限分配模型
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月5日
 * @version: v1.0.0
 */
namespace core\models\mysqldb\admin;

use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%auth_assignment}}".
 *
 * @property string $item_name
 * @property string $user_id
 * @property integer $created_at
 */
class AdminAssign extends ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at']
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_assignment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_name', 'user_id'], 'required'],
            [['created_at'], 'integer'],
            [['item_name', 'user_id'], 'string', 'max' => 64],
            [['item_name'], 'unique', 'targetAttribute' => ['item_name', 'user_id'],'message'=>'已经有该角色了，请勿重复添加'],
            [['item_name'], 'exist', 'skipOnError' => true, 'targetClass' => AdminRoleItem::className(), 'targetAttribute' => ['item_name' => 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_name' => '角色',
            'user_id' => '管理员',
            'created_at' => '创建时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemName()
    {
        return $this->hasOne(Auth::className(), ['name' => 'item_name']);
    }
}
