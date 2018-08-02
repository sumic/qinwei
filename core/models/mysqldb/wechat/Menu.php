<?php
/**
 * =======================================================
 * @Description :mpwechat menu mysqldb model
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年08月02日 17:29:51
 * @version: v1.0.0
 */

namespace core\models\mysqldb\wechat;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;



/**
 * This is the model class for table "{{%wechat_menu}}".
 *
 * @property int $id 菜单ID
 * @property int $mpid 所属公众号ID
 * @property int $pid 父菜单ID
 * @property string $name 菜单名称
 * @property string $type 菜单类型
 * @property string $mpkey 菜单KEY值，用于消息接口推送，不超过128字节
 * @property string $url 网页链接，用户点击菜单可打开链接
 * @property string $media_id 素材id
 * @property int $crreated_at 创建时间
 * @property int $updated_at 更新时间
 */
class Menu extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%wechat_menu}}';
    }

    public function behaviors()
    {
        TimestampBehavior::className();
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mpid', 'pid', 'name', 'type', 'mpkey', 'url', 'media_id', 'created_at', 'updated_at'], 'required'],
            [['mpid', 'pid', 'crreated_at', 'updated_at'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['type', 'media_id'], 'string', 'max' => 20],
            [['mpkey'], 'string', 'max' => 129],
            [['url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '菜单ID',
            'mpid' => '所属公众号ID',
            'pid' => '父菜单ID',
            'name' => '菜单名称',
            'type' => '菜单类型',
            'mpkey' => '菜单KEY值，用于消息接口推送，不超过128字节',
            'url' => '网页链接，用户点击菜单可打开链接',
            'media_id' => '素材id',
            'crreated_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
