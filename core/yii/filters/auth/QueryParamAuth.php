<?php
/**
 * =======================================================
 * @Description : TODO
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年7月24日 下午2:39:34
 * @version: v1.0.0
 */
namespace core\yii\filters\auth;

use Yii; 
use yii\filters\RateLimiter;  
use yii\filters\auth\QueryParamAuth as YiiQueryParamAuth;

class QueryParamAuth extends YiiQueryParamAuth
{
    /**
     * 重写该方法。该方法从request header中读取access-token。
     */
    public function authenticate($user, $request, $response)
    {   
        $identity = Yii::$service->customer->loginByAccessToken(get_class($this));
        if($identity){
            return $identity;
        }else{
            $fecshop_uuid = Yii::$service->session->fecshop_uuid;
            $cors_allow_headers = [$fecshop_uuid,'fecshop-lang','fecshop-currency','access-token'];
            
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, ".implode(', ',$cors_allow_headers));
            header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE');
            $code = Yii::$service->helper->appserver->account_no_login_or_login_token_timeout;
            $result = [ 'code' => $code,'message' => 'token is time out'];
            Yii::$app->response->data = $result;
            Yii::$app->response->send();
            Yii::$app->end();
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
}