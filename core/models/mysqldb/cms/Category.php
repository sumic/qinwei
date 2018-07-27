<?php
/**
 * =======================================================
 * @Description :cms category mysqldb model
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月18日
 * @version: v1.0.0
 */

namespace core\models\mysqldb\cms;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "{{%category}}".
 *
 * @property int $id 导航栏目ID
 * @property int $pid 父类ID
 * @property string $menu_name 导航栏目
 * @property string $url 访问地址
 * @property int $status 状态
 * @property int $sort 排序
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class Category extends ActiveRecord
{
    /**
     * 状态
     */
    const STATUS_ACTIVE = 1; // 启用
    const STATUS_DELETE = 2; // 关闭
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }
    
    public function behaviors(){
        return [
            TimestampBehavior::className(),
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'required'],
            [['name', 'url'], 'string', 'max' => 64],
            [['status'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '栏目ID',
            'pid' => '父类ID',
            'name' => '栏目名称',
            'url' => '访问地址',
            'status' => '状态',
            'sort' => '排序',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

}

