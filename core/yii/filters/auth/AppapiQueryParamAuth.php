<?php
/**
 * =======================================================
 * @Description : TODO
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年7月24日 下午2:38:44
 * @version: v1.0.0
 */

namespace core\yii\filters\auth;

use Yii; 
use yii\filters\RateLimiter;  
use yii\filters\auth\QueryParamAuth as YiiQueryParamAuth;

class AppapiQueryParamAuth extends YiiQueryParamAuth
{
    
    /**
     * 重写该方法。该方法从request header中读取access-token。
     */
    public function authenticate($user, $request, $response)
    {   
        $identity = Yii::$service->admin->userlogin->loginByAccessToken(get_class($this));
        if($identity){
            return $identity;
        }else{
            $code = Yii::$service->helper->appapi->account_no_login_or_login_token_timeout;
            $result = [ 'code' => $code,'message' => 'token is time out'];
            Yii::$app->response->data = $result;
            Yii::$app->response->send();
            Yii::$app->end();
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
}