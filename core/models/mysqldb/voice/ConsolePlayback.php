<?php
/**
 * =======================================================
 * @Description :admin voice model
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月6日
 * @version: v1.0.0
 */
namespace core\models\mysqldb\voice;

use core\models\mysqldb\voice\Playback;
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
class ConsolePlayback extends Playback
{
    public $config = [];
    // 定义字段
    public $file;
    public $image;
    public $video;

    //敏感词
    public $sensitive;

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            //UpdateBehavior::className()
        ];
    }
   
}
