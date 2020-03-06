<?php
/**
 * =======================================================
 * @Description :admin log model
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月6日
 * @version: v1.0.0
 */
namespace core\models\mysqldb\admin;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%admin_log}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $controller
 * @property string $action
 * @property string $index
 * @property string $url
 * @property string $params
 * @property integer $created_id
 * @property integer $created_at
 */
class AdminLog extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'created_id'], 'integer'],
            [['params'], 'string'],
            [['controller', 'action'], 'string', 'max' => 64],
            [['url', 'index'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '日志ID',
            'type' => '类型',
            'controller' => '操作控制器',
            'action' => '操作方法',
            'index' => '数据唯一标识',
            'url' => '操作的URL',
            'params' => '请求参数',
            'created_id' => '创建管理员ID',
            'created_at' => '创建时间',
        ];
    }

   
}
