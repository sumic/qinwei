<?php
/**
 * =======================================================
 * @Description :admin_menu model
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年3月30日
 * @version: v1.0.0
 *
 */
namespace core\models\mysqldb\admin;

use core\models\mysqldb\AppadminModel;
use core\behaviors\UpdateBehavior;
use yii\behaviors\TimestampBehavior;

class AdminMenu extends AppadminModel
{
    /**
     * 状态
     */
    const STATUS_ACTIVE = 1; // 启用
    const STATUS_DELETE = 2; // 关闭
	
    public static function tableName()
    {
        return '{{%admin_menu}}';
    }
	
    public function behaviors(){
       return [
           TimestampBehavior::className(),
           UpdateBehavior::className(),
        ];
    }
    
    public function rules()
    {
        return [
            [['pid', 'status', 'sort'], 'integer'],
            [['menu_name', 'status'], 'required'],
            [['menu_name', 'icons', 'url'], 'string', 'max' => 50]
        ];
    }
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => '上级分类',
            'menu_name' => '栏目名称',
            'icons' => '图标',
            'url' => '访问地址',
            'status' => '状态',
            'sort' => '排序字段',
            'created_at' => '创建时间',
            'created_id' => '创建用户',
            'updated_at' => '修改时间',
            'updated_id' => '修改用户',
        ];
    }
}
