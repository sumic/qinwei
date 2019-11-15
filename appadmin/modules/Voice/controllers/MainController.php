<?php
/**
 * =======================================================
 * @Description :主控制器
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @version: v1.0.0
 */

namespace appadmin\modules\Voice\controllers;

use appadmin\modules\AppadminController;

class MainController extends AppadminController{
    
    public function actionIndex(){
        $data = $this->getBlock()->getLastData();
        return $this->render($this->action->id,$data['params']);
    }
    
    public function actionUploads()
    {
        return $this->getBlock('index')->doUpload();
    }
}
