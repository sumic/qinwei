<?php
/**
 * =======================================================
 * @Description :权限规则模型
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月5日
 * @version: v1.0.0
 */
namespace core\models\mysqldb\admin;

use \yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%auth_assignment}}".
 *
 * @property string $item_name
 * @property string $user_id
 * @property integer $created_at
 */
class AdminRule extends ActiveRecord
{
    /**
     * @var string 定义使用的旧名称
     */
    public $newName;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_rule}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['newName'], 'required'],
            [['newName'], 'unique', 'targetAttribute' => 'name'],
            [['name'], 'required', 'on' => ['update']],
            [['data'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'newName'], 'string', 'max' => 64],
            [['data'], 'classExists'],
        ];
    }

    /**
     * 定义验证场景需要验证的字段
     * @return array
     */
    public function scenarios()
    {
        return [
            'default' => ['name', 'data'],
            'create' => ['newName', 'data'],
            'update' => ['name', 'newName', 'data']
        ];
    }
    
    /**
     * Validate class exists
     */
    public function classExists()
    {
        if (!class_exists($this->data)) {
            $message = '验证规则类'.$this->data.'不存在';
            $this->addError('data', $message);
            return;
        }
        if (!is_subclass_of($this->data, \yii\rbac\Rule::className())) {
            $message = '验证规则类'.$this->data.'必须继承自 yii\rbac\Rule 类';
            $this->addError('data', $message);
        }
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'data' => 'Data',
            'newName' => '名称',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
