<?php
/**
 * =======================================================
 * @Description :mpwechat base mysqldb model
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月18日
 * @version: v1.0.0
 */

namespace core\models\mysqldb\wechat;

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
class Base extends ActiveRecord
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
        return '{{%wechat}}';
    }
    public function behaviors()
    {
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
            [['mpname', 'mptype', 'appid', 'appsecret', 'token', 'aeskey'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['mpname', 'appid', 'token'], 'string', 'max' => 20],
            [['mptype', 'status'], 'string', 'max' => 1],
            [['appsecret'], 'string', 'max' => 40],
            [['aeskey'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mpname' => '公众号名称',
            'mptype' => '公众号类型',
            'appid' => '应用ID',
            'appsecret' => '应用密匙',
            'token' => '令牌',
            'aeskey' => '消息加密密钥',
            'status' => '是否是默认 1：默认 0：不默认',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}

