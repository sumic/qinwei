<?php

/**
 * =======================================================
 * @Description :admin uploads model
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月6日
 * @version: v1.0.0
 */

namespace core\models\mysqldb\uploads;

use yii\db\ActiveRecord;
use core\behaviors\UpdateBehavior;
use yii\behaviors\TimestampBehavior;

class Uploads extends ActiveRecord
{
    public $config = [];
    // 定义字段
    public $file;
    public $image;
    public $video;
    public $playback;

    public static function tableName()
    {
        return '{{%uploads}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            UpdateBehavior::className(),
        ];
    }
    // 设置应用场景
    public function scenarios()
    {
        return [
            'file' => ['file'],
            'image'   => ['image'],
            'video' => ['video'],
            'playback' => ['playback']
        ];
    }

    // 验证规则
    public function rules()
    {
        return [
            [
                ['file'], 'file',
                'extensions' => $this->config['fileAllowFiles'],
                'maxSize' => $this->config['fileMaxSize'],
                'wrongExtension' => '不支持的文件扩展名',
                'checkExtensionByMimeType' => false,
                'on' => 'file'
            ],
            [
                ['image'], 'file',
                'extensions' => $this->config['imageAllowFiles'],
                'maxSize' => $this->config['imageMaxSize'],
                'checkExtensionByMimeType' => false,
                'wrongExtension' => '不支持的文件扩展名',
                'on' => 'image'
            ],
            [
                ['video'], 'file',
                'extensions' => $this->config['videoAllowFiles'],
                'maxSize' => $this->config['videoMaxSize'],
                'checkExtensionByMimeType' => false,
                'wrongExtension' => '不支持的文件扩展名',
                'on' => 'video'
            ],
            [
                ['playback'], 'file',
                'extensions' => $this->config['playbackAllowFiles'],
                'maxSize' => $this->config['playbackMaxSize'],
                'wrongExtension' => '不支持的文件扩展名',
                'checkExtensionByMimeType' => false,
                'on' => 'playback'
            ],
            [['size', 'created_at', 'created_id'], 'integer'],
            [['name', 'savepath'], 'string', 'max' => 255],
            [['savename'], 'string', 'max' => 255],
            [['ext'], 'string', 'max' => 5],
            [['mime', 'sha1'], 'string', 'max' => 40],
            [['md5'], 'string', 'max' => 32],
            [['location'], 'string', 'max' => 3],
            [['url'], 'string', 'max' => 255],
            [['md5'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '文件ID',
            'name' => '原始文件名',
            'savename' => '保存名称',
            'savepath' => '文件保存路径',
            'ext' => '文件后缀',
            'mime' => '文件mime类型',
            'size' => '文件大小',
            'md5' => '文件md5',
            'sha1' => '文件 sha1编码',
            'location' => '文件保存位置',
            'url' => '远程地址',
            'created_at' => '上传时间',
            'created_id' => '用户id',
        ];
    }
}
