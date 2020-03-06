<?php
/**
 * =======================================================
 * @Description : wechat main controller
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年7月27日 下午4:49:57
 * @version: v1.0.0
 */

namespace appadmin\modules\Wechat\controllers;

use appadmin\modules\AppadminController;

class MainController extends AppadminController{
    
    public function actionIndex(){
        $data  = $this->getBlock()->getLastData();
        return $this->render($this->action->id,$data['params']);
    }
    
}