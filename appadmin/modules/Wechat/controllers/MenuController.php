<?php
/**
 * =======================================================
 * @Description : wechat menu controller
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年7月27日 下午4:49:57
 * @version: v1.0.0
 */

namespace appadmin\modules\Wechat\controllers;

use Yii;
use appadmin\modules\AppadminController;

class MenuController extends AppadminController{
    
    public function actionIndex(){
        $data = $this->getBlock()->getLastData();
        return $this->render($this->action->id,$data['params']);
    }
    
    public function actionAsyncwxmenu()
    {
        $data = $this->getBlock('async')->menu();
        return $data;
    }
}