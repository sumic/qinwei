<?php
/**
 * =======================================================
 * @Description : wechat sync contorller
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: Aug 9, 2018 10:59:04 AM
 * @version: v1.0.0
 */
namespace appadmin\modules\Wechat\controllers;

use core\controllers\BaseController;

class SyncController extends BaseController
{
    public function actionIndex()
    {
        $data = $this->getBlock()->signature();
        return $data;
    }
}