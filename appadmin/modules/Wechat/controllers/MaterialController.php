<?php
/**
 * =======================================================
 * @Description : wechat material contorller
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年08月15日15:28:23
 * @version: v1.0.0
 */
namespace appadmin\modules\Wechat\controllers;

use appadmin\modules\AppadminController;

class MaterialController extends AppadminController
{
    public function actionIndex()
    {
        $data = $this->getBlock()->getLastData();
        return $this->render($this->action->id,$data['params']);
    }
}