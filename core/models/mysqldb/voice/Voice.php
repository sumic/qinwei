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
use core\models\mysqldb\uploads\Uploads;
use yii\behaviors\TimestampBehavior;

class Voice extends Uploads
{
    public $config = [];
    // 定义字段
    public $file;
    public $image;
    public $video;
    
    public static function tableName()
    {
        return '{{%voice}}';
    }
  
}
