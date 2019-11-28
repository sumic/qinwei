<?php
/**
 * =======================================================
 * @Description :菜单设置控制器
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */

namespace appadmin\modules\Cms\controllers;

use appadmin\modules\AppadminController;

class MainController extends AppadminController{
    
    
    public function actionIndex(){
        $data = $this->getBlock()->getLastData();
        return $this->render($this->action->id,$data['params']);
    }
    
    public function actionCreate()
    {
        $data = $this->getBlock('edit')->getLastData();
        return $data ? $this->render($this->action->id,$data) :'';
    }
    
    public function actionView()
    {
        $data = $this->getBlock()->getLastData();
        return $data ? $this->render($this->action->id,$data) :'';
    }
    
    public function actionEdit($id)
    {
        $data = $this->getBlock()->getLastData($id);
        return $data ? $this->render($this->action->id,$data) :'';
    }
}
