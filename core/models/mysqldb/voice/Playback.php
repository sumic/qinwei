<?php
/**
 * =======================================================
 * @Description :admin voice model
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月6日
 * @version: v1.0.0
 */
namespace core\models\mysqldb\voice;

use core\behaviors\UpdateBehavior;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%voice}}".
 *
 * @property int $id 文件ID
 * @property int $fid 上传文件Uploads表ID
 * @property int $cid 项目分类ID
 * @property string $taskid 任务ID
 * @property string $name 原始文件名
 * @property string $content 转换内容
 * @property int $created_at 上传时间
 * @property int $created_id 用户id
 * @property int $updated_at 更新时间
 * @property int $updated_id 更新用户id
 */
class Playback extends ActiveRecord
{
    public $config = [];
    // 定义字段
    public $file;
    public $image;
    public $video;

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            UpdateBehavior::className()
        ];
    }
    
    public static function tableName()
    {
        return '{{%voice}}';
    }
  
    // 验证场景
	public function scenarios()
	{
	    return [
	        'default' => ['fid', 'cid', 'name', 'created_at', 'created_id', 'updated_at', 'updated_id'],
            'create'  => ['fid', 'cid', 'name', 'created_at', 'created_id', 'updated_at', 'updated_id'],
            'update'  => ['taskid','status', 'updated_at', 'updated_id'],
	    ];
	}
    public function rules()
    {
        return [
            [['fid', 'cid', 'endtime', 'status', 'created_at', 'created_id', 'updated_at', 'updated_id'], 'integer'],
            [['status'],'default','value' => '-1'],
            [['content'], 'string'],
            [['taskid'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 255],
            [['taskid', 'status'], 'required','on' => 'update'],

        ];
    }

    /**
     * @inheritdoc
     * 
     */
    public function attributeLabels()
    {
        return [
            'id' => '文件ID',
            'fid' => '上传文件ID',
            'cid' => '项目分类ID',
            'taskid' => '任务ID',
            'name' => '原始文件名',
            'content' => '转换内容',
            'endtime' => '语音时长',
            'status' => '语音状态',
            'created_at' => '上传时间',
            'created_id' => '用户id',
            'updated_at' => '更新时间',
            'updated_id' => '更新用户id',
        ];
    }

   
}
