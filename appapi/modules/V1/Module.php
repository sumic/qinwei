<?php
/**
 * =======================================================
 * @Description : appapi modules
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年7月24日 下午2:24:31
 * @version: v1.0.0
 */
namespace appapi\modules\V1;

use appapi\modules\AppapiModule;
use Yii;

class Module extends AppapiModule
{
    public $blockNamespace;

    public function init()
    {
        // 以下代码必须指定
        $nameSpace = __NAMESPACE__;
        // web controller
        $this->controllerNamespace = $nameSpace . '\\controllers';
        $this->blockNamespace = $nameSpace . '\\block';
        // 指定默认的man文件
        //$this->layout = "home.php";
        //Yii::$service->page->theme->layoutFile = 'home.php';
        parent::init();
    }
}
