<?php
/**
 * =======================================================
 * @Description :主控制器
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */

namespace appadmin\modules\Role\controllers;

use appadmin\modules\AppadminController;

class MainController extends AppadminController
{
    public function actionIndex(){
        $data = $this->getBlock()->getLastData();
        return $data ? $this->render($this->action->id,$data['params']) : '';
    }
    
    public function actionEdit($name)
    {
        $data = $this->getBlock()->getLastData($name);
        return $data ? $this->render($this->action->id,$data) : false;
    }
    
    public function actionView($name)
    {
        $data = $this->getBlock()->getLastData($name);
        return $data ? $this->render($this->action->id,$data) : '';
    }
}
