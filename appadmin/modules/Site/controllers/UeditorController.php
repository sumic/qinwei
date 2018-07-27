<?php

namespace appadmin\modules\Site\controllers;

use appadmin\modules\AppadminController;
use yii;

class UeditorController extends AppadminController
{
    public function actions(){
        //CSRF 基于 POST 验证，UEditor 无法添加自定义 POST 数据，同时由于这里不会产生安全问题，故简单粗暴地取消 CSRF 验证。
        //如需 CSRF 防御，可以使用 server_param 方法，然后在这里将 Get 的 CSRF 添加到 POST 的数组中。。。
        Yii::$app->request->enableCsrfValidation = false;
    }
    
    public function actionIndex()
    {
        $data = $this->getBlock()->getlastdata();
        return $data;
    }
}
