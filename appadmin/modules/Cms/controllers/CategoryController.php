<?php
/**
 * =======================================================
 * @Description :cms category controller
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */

namespace appadmin\modules\Cms\controllers;

use appadmin\modules\AppadminController;

class CategoryController extends AppadminController{
    
    
    public function actionIndex(){
        $data = $this->getBlock()->getLastdata();
        return $this->render($this->action->id,$data['params']);
    }
    
}
