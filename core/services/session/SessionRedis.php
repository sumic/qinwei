<?php
/**
 * =======================================================
 * @Description : redis session service
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年7月24日 下午5:27:57
 * @version: v1.0.0
 */

namespace core\services\session;

use Yii;
use core\services\Service;

class SessionRedis extends Service implements SessionInterface
{
    protected $_sessionModelName = '\core\models\redis\SessionStorage';
    protected $_sessionModel;
    
    public function init(){
        parent::init();
        list($this->_sessionModelName,$this->_sessionModel) = \Yii::mapGet($this->_sessionModelName);  
    }
    
    public function set($key,$val,$timeout){
        $uuid = Yii::$service->session->getUUID();
        $one = $this->_sessionModel->find()->where([
            'session_uuid' => $uuid,
            'session_key'  => $key,
        ])->one();
        if(!$one['id']){
            $one = new $this->_sessionModelName();
            $one['session_uuid'] = $uuid;
            $one['session_key']  = $key;
        }
        $one['session_value']       = $val;
        $one['session_timeout']     = $timeout;
        $one['session_updated_at']  = time();
        $one->save();
        return true;
    }

    public function get($key,$reflush){
        $uuid = Yii::$service->session->getUUID();
        $one = $this->_sessionModel->find()->where([
            'session_uuid' => $uuid,
            'session_key'  => $key,
        ])->one();
        if($one['id']){
            $timeout = $one['session_timeout'];
            $updated_at = $one['session_updated_at'];
            if($updated_at + $timeout > time()){
                if($reflush){
                    $one['session_updated_at']  = time();
                    $one->save();
                }
                return $one['session_value'];
            }
        }
    }

    public function remove($key){
        $uuid = Yii::$service->session->getUUID();
        $one = $this->_sessionModel->find()->where([
            'session_uuid' => $uuid,
            'session_key'  => $key,
        ])->one();
        if($one['id']){
            $one->delete();
            return true;
        }
        
    }
    
    public function destroy(){
        if(!Yii::$app->user->isGuest){
            $identity = Yii::$app->user->identity;
            $identity->access_token = '';
            $identity->access_token_created_at = null;
            $identity->save();
        }
        $uuid = Yii::$service->session->getUUID();
        $result = $this->_sessionModel->deleteAll([
            'session_uuid' => $uuid,
        ]);
        $access_token_created_at = $identity->access_token_created_at;
        $timeout = Yii::$service->session->timeout;
        if($access_token_created_at + $timeout > time()){
            return $accessToken;
        } 
        return true;
       
    }

    public function setFlash($key,$val,$timeout){
        return $this->set($key,$val,$timeout);
    }
    
    public function getFlash($key){
        $uuid = Yii::$service->session->getUUID();
        $one = $this->_sessionModel->find()->where([
            'session_uuid' => $uuid,
            'session_key'  => $key,
        ])->one();
        if($one['id']){
            $timeout = $one['session_timeout'];
            $updated_at = $one['session_updated_at'];
            if($updated_at + $timeout > time()){
                
                $val = $one['session_value'];
                $one->delete();
                return $val;
            }
        }
    }
    
}