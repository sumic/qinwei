<?php
/**
 * =======================================================
 * @Description :site modules 
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月17日
 * @version: v1.0.0
 */
namespace appadmin\modules\Site;

use Yii;
use appadmin\modules\AppadminModule;


class Module extends AppadminModule
{
    public $blockNamespace;

    public function init()
    {
        // 以下代码必须指定
        $nameSpace = __NAMESPACE__;
        // 如果 Yii::$app 对象是由类\yii\web\Application 实例化出来的。
        if (Yii::$app instanceof \yii\web\Application) {
            // 设置模块 controller namespace的文件路径
            $this->controllerNamespace = $nameSpace . '\\controllers';
            // 设置模块block namespace的文件路径
            $this->blockNamespace = $nameSpace . '\\block';
            
            $this->defaultRoute = 'main/login';
        // console controller
        //} elseif (Yii::$app instanceof \yii\console\Application) {
            //$this->controllerNamespace = $nameSpace . '\\console\\controllers';
            //$this->blockNamespace = $nameSpace . '\\console\\block';
        }
        //$this->_currentDir			= 	__DIR__ ;
        //$this->_currentNameSpace	=   __NAMESPACE__;

        // 设置该模块的view(theme)的默认layout文件。
        //Yii::$service->page->theme->layoutFile = 'home.php';
        parent::init();
    }
}
