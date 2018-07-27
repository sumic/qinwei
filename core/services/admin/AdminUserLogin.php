<?php
/**
 * =======================================================
 * @Description :logs service
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */

namespace core\services\admin;

use core\services\Service;
use yii;
use yii\base\InvalidValueException;

class AdminUserLogin extends Service
{
    const USER_LOGIN_SUCCESS_REDIRECT_URL_KEY = 'usr_login_success_redirect_url';
    
    protected $_adminLoginModelName = '\core\models\mysqldb\admin\AdminUserLogin';
    protected $_adminLoginModel;
    
    
    public function init(){
        parent::init();
        list($this->_adminLoginModelName,$this->_adminLoginModel) = \Yii::mapGet($this->_adminLoginModelName);
    }
    
    #返回model为active form使用
    protected function actionGetUserLoginModel($data){
        $model = new $this->_adminLoginModel();
        $model->username    = $data['username'];
        $model->password    = $data['password'];
        $model->verifyCode  = $data['verifyCode'];
        return $model;
    }
    
    protected function actionLogin($data)
    {
        $model = new $this->_adminLoginModelName();
        $model->username    = $data['username'];
        $model->password    = $data['password'];
        $model->verifyCode    = $data['verifyCode'];
        $model->rememberMe    = $data['rememberMe'];
        $loginStatus        = $model->login();
        $errors             = $model->errors;
        if (!empty($errors)) {
            Yii::$service->helper->errors->addByModelErrors($errors);
        }
        return $loginStatus;
    }
    
    /**
     * @property $url|string
     * **注意**：该方法不能在接口类型里面使用
     * 在一些功能中，需要用户进行登录操作，等用户操作成功后，应该跳转到相应的页面中，这里通过session存储需要跳转到的url。
     * 某些页面 ， 譬如评论页面，需要用户登录后才能进行登录操作，那么可以通过这个方法把url set 进去，登录成功
     * 后，页面不会跳转到账户中心，而是需要操作的页面中。
     */
    protected function actionSetLoginSuccessRedirectUrl($url)
    {
        return Yii::$service->session->set($this::USER_LOGIN_SUCCESS_REDIRECT_URL_KEY, $url);
    }
    /**
     * @property $url|string
     * **注意**：该方法不能在接口类型里面使用
     * **注意**：该方法不能在接口类型里面使用
     * 在一些功能中，需要用户进行登录操作，等用户操作成功后，应该跳转到相应的页面中，这里通过session得到需要跳转到的url。
     */
    protected function actionGetLoginSuccessRedirectUrl()
    {
        $url = Yii::$service->session->get($this::USER_LOGIN_SUCCESS_REDIRECT_URL_KEY);
        
        return $url ? $url : '';
    }
    /**
     * @property $urlKey | String
     * **注意**：该方法不能在接口类型里面使用
     * 登录用户成功后，进行url跳转。
     */
    protected function actionLoginSuccessRedirect($urlKey = '')
    {
        $url = $this->getLoginSuccessRedirectUrl();
        
        if ($url) {
            // 这个优先级最高
            // 在跳转之前，去掉这个session存储的值。跳转后，这个值必须失效。
            Yii::$service->session->remove($this::USER_LOGIN_SUCCESS_REDIRECT_URL_KEY);
            //echo Yii::$service->session->get($this::USER_LOGIN_SUCCESS_REDIRECT_URL_KEY);
            //exit;
            return Yii::$service->url->redirect($url);
        } else if($urlKey) {
            return Yii::$service->url->redirectByUrlKey($urlKey);
        } else {
            return Yii::$service->url->redirectHome();
        }
    }
    
    /**
     * @property $ids | Int Array
     * @return 得到相应用户的数组。
     */
    protected function actionGetIdAndNameArrByIds($ids)
    {
        $user_coll = \fecadmin\models\AdminUser::find()->asArray()->select(['id', 'username'])->where([
            'in', 'id', $ids,
        ])->all();
        $users = [];
        foreach ($user_coll as $one) {
            $users[$one['id']] = $one['username'];
        }
        
        return $users;
    }
    
    /** AppServer 部分使用的函数
     * @property $email | String
     * @property $password | String
     * 无状态登录，通过email 和password进行登录
     * 登录成功后，合并购物车，返回accessToken
     * ** 该函数是未登录用户，通过参数进行登录需要执行的函数。
     */
    protected function actionLoginAndGetAccessToken($username,$password){
        $header = Yii::$app->request->getHeaders();
        if(isset($header['access-token']) && $header['access-token']){
            $accessToken = $header['access-token'];
        }
        // 如果request header中有access-token，则查看这个 access-token 是否有效
        if($accessToken){
            $identity = Yii::$app->user->loginByAccessToken($accessToken);
            if ($identity !== null) {
                $access_token_created_at = $identity->access_token_created_at;
                $timeout = Yii::$service->session->timeout;
                if($access_token_created_at + $timeout > time()){
                    return $accessToken;
                }
            }
        }
        // 如果上面access-token不存在
        $data = [
            'username'     => $username,
            'password'  => $password,
        ];
       
        if($this->login($data)){
            $identity = Yii::$app->user->identity;
            $identity->generateAccessToken();
            $identity->access_token_created_at = time();
            $identity->scenario = 'api';
            $identity->save();
            $this->setHeaderAccessToken($identity->access_token);
            return $identity->access_token;
            
        }
    }
    
    protected function actionSetHeaderAccessToken($accessToken){
        if($accessToken){
            Yii::$app->response->getHeaders()->set('access-token',$accessToken);
            return true;
        }
    }
    
    /** AppServer 部分使用的函数
     * @property $type | null or  Object
     * 从request headers中获取access-token，然后执行登录
     * 如果登录成功，然后验证时间是否过期
     * 如果不过期，则返回identity
     * ** 该方法为appserver用户通过access-token验证需要执行的函数。
     */
    protected function actionLoginByAccessToken($type = null){
        $header = Yii::$app->request->getHeaders();
        if(isset($header['access-token']) && $header['access-token']){
            $accessToken = $header['access-token'];
        }
        if($accessToken){
            $identity = Yii::$app->user->loginByAccessToken($accessToken, $type);
            if ($identity !== null) {
                $access_token_created_at = $identity->access_token_created_at;
                $timeout = Yii::$service->session->timeout;
                // 如果时间没有过期，则返回identity
                if($access_token_created_at + $timeout > time()){
                    //如果时间没有过期，但是快要过期了，在过$updateTimeLimit段时间就要过期，那么更新access_token_created_at。
                    $updateTimeLimit = Yii::$service->session->updateTimeLimit;
                    if($access_token_created_at + $timeout <= (time() + $updateTimeLimit )){
                        $identity->access_token_created_at = time();
                        $identity->save();
                    }
                    return $identity;
                }else{
                    $this->logoutByAccessToken();
                    return false;
                }
            }
        }
    }
    
    /**
     * 通过accessToek的方式，进行登出从操作。
     */
    public function logoutByAccessToken()
    {
        $userComponent = Yii::$app->user;
        $identity = $userComponent->identity;
        if ($identity !== null ) {
            if(!Yii::$app->user->isGuest){
                $identity->access_token = null;
                $identity->access_token_created_at = null;
                $identity->save();
            }
            $userComponent->switchIdentity(null);
        }
        
        return $userComponent->getIsGuest();
    }
}