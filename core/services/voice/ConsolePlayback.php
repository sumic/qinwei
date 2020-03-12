<?php

/**
 * =======================================================
 * @Description :base service
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @version: v1.0.0
 */

namespace core\services\voice;

use core\services\voice\Playback;
use yii;

class ConsolePlayback extends Playback
{
    protected $_modelName = '\core\models\mysqldb\voice\ConsolePlayback';
    protected $_model;

    public function init()
    {
        list($this->_modelName, $this->_model) = \Yii::mapGet($this->_modelName);
    }    
}
