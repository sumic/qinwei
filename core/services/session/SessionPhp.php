<?php
/**
 * =======================================================
 * @Description :php session service
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月17日
 * @version: v1.0.0
 */

namespace core\services\session;

use Yii;
use core\services\Service;
use core\models\mysqldb\SessionStorage;


class SessionPhp extends service implements SessionInterface
{
    public $timeout;
    
    public function init(){
        parent::init();
        $this->timeout = Yii::$app->session->timeout;
    }
    
    public function set($key,$val,$timeout){
        if($timeout){
            $this->timeout = $timeout;
            Yii::$app->session->setTimeout($timeout);
        }
        return Yii::$app->session->set($key,$val);
    }

    public function get($key,$reflush){
        return Yii::$app->session->get($key);
    }

    public function remove($key){
        return Yii::$app->session->remove($key);
        
    }

    public function setFlash($key,$val,$timeout){
        if($timeout){
            $this->timeout = $timeout;
            Yii::$app->session->setTimeout($timeout);
        }
        return Yii::$app->session->setFlash($key,$val);
    }
    
    public function getFlash($key){
        return Yii::$app->session->getFlash($key);
    }
    
    public function destroy(){
        return Yii::$app->getSession()->destroy();
    }
    
}