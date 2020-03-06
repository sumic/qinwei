<?php
/**
 * =======================================================
 * @Description : TODO
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年7月24日 下午3:01:40
 * @version: v1.0.0
 */

namespace appapi\modules;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use core\yii\filters\auth\AppapiQueryParamAuth;  
use yii\web\Response;
use yii\filters\RateLimiter; 
use appapi\modules\AppapiController;

class AppapiTokenController extends AppapiController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [  
            'class' => CompositeAuth::className(),  
            'authMethods' => [  
                # 下面是三种验证access_token方式  
                //HttpBasicAuth::className(),  
                //HttpBearerAuth::className(),  
                # 这是GET参数验证的方式  
                # http://10.10.10.252:600/user/index/index?access-token=xxxxxxxxxxxxxxxxxxxx  
                AppapiQueryParamAuth::className(),  
            ],  
          
        ];  
          
        # rate limit部分，速度的设置是在  
        #   \myapp\code\core\Erp\User\models\User::getRateLimit($request, $action){  
        /*  官方文档：  
            当速率限制被激活，默认情况下每个响应将包含以下HTTP头发送 目前的速率限制信息：  
            X-Rate-Limit-Limit: 同一个时间段所允许的请求的最大数目;  
            X-Rate-Limit-Remaining: 在当前时间段内剩余的请求的数量;  
            X-Rate-Limit-Reset: 为了得到最大请求数所等待的秒数。  
            你可以禁用这些头信息通过配置 yii\filters\RateLimiter::enableRateLimitHeaders 为false, 就像在上面的代码示例所示。  
  
        */  
        $rateLimit = Yii::$app->params['rateLimit'];
        if(isset($rateLimit['enable']) && $rateLimit['enable']){
            $behaviors['rateLimiter'] = [  
                'class' => RateLimiter::className(),  
                'enableRateLimitHeaders' => true,  
            ]; 
        }
        
        return $behaviors;
    }

    
}
