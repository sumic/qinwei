<?php
/**
 * =======================================================
 * @Description :权限、角色模型
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月2日
 * @version: v1.0.0
 *
 */
namespace core\models\mysqldb\admin;

use yii\db\ActiveRecord;
use Yii;

class AdminRoleItem extends ActiveRecord
{
    /**
     * @var integer 角色
     */
    const TYPE_ROLE = 1;
    
    /**
     * @var integer 权限
     */
    const TYPE_PERMISSION = 2;
    
    /**
     * @var string 定义名称
     */
    
    public $newName;
    /**
     * @var array 权限信息
     */
    public $_permissions = [];
    
    public static function tableName()
    {
        return '{{%auth_item}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type', 'newName', 'description'], 'required'],
            [['name', 'type', 'newName', 'description'], 'trim'],
            [['name', 'newName'], 'match', 'pattern' => '/^([a-zA-Z0-9_-]|([a-zA-z0-9_-]\\/[0-9_-a-zA-z]))+$/'],
            ['name', 'string', 'min' => 3],
            ['type', 'integer'],
            ['type', 'in', 'range' => [self::TYPE_PERMISSION, self::TYPE_ROLE]],
            [['name', 'newName'], 'unique', 'targetAttribute' => 'name'],
            ['name', 'validatePermission'],
            [['rule_name', 'name', 'newName'], 'string', 'max' => 64],
            ['description', 'string', 'min' => 1, 'max' => 400],
        ];
    }
    
    /**
     * 定义验证场景需要验证的字段
     * @return array
     */
    public function scenarios()
    {
        return [
            'default' => ['name', 'data', 'type', 'rule_name', 'description'],
            'create' => ['newName', 'data', 'type', 'rule_name', 'description'],
            'update' => ['name', 'newName', 'data', 'type', 'rule_name', 'description']
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rule_name' => '使用规则',
            'name' => '名称',
            'newName' => '新名称',
            'description' => '描述',
            '_permissions' => '权限数组',
        ];
    }
    
    public function validatePermission()
    {
        if (!$this->hasErrors()) {
            $auth = Yii::$app->getAuthManager();
            if ($this->isNewRecord && $auth->getPermission($this->newName)) {
                $this->addError('name', '已有重复的路由名称');
            }
            if ($this->isNewRecord && $auth->getRole($this->newName)) {
                $this->addError('name', '已有重复的角色名称');
            }
        }
    }
}
