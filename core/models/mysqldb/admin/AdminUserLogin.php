<?php
/**
 * =======================================================
 * @Description :admin user login model
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月18日
 * @version: v1.0.0
 */
namespace core\models\mysqldb\admin;

use core\models\mysqldb\admin\AdminUser;
use Yii;
use yii\base\Model;


class AdminUserLogin extends Model
{
    public $username;
    public $password;
    public $verifyCode;
    public $rememberMe;
    private $_adminUser;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
            //开启验证码时验证验证码
            ['verifyCode', 'required','when' => function($model){
                return \Yii::$app->params['admin_verifyCode'] == TRUE;
            }],
            ['verifyCode', 'validateCaptcha','when' => function($model){
                return \Yii::$app->params['admin_verifyCode'] == TRUE;
            }],
            ['rememberMe', 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username'	 	=> '用户名',
            'password' => '密码',
            'verifyCode' => '验证码',
            'rememberMe' => '保持登录',
        ];
    }
    
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
           
            $adminUser = $this->getAdminUser();
            if (!$adminUser) {
                $this->addError($attribute, '用户不存在');
            } elseif ($adminUser['status'] == AdminUser::STATUS_INACTIVE) {
                $this->addError($attribute, '用户被禁用');
            } elseif ($adminUser['status'] == AdminUser::STATUS_DELETED) {
                $this->addError($attribute, '用户被删除');
            }elseif ($adminUser['status'] == AdminUser::STATUS_ACTIVE && !$adminUser->validatePassword($this->password)) {
                $this->addError($attribute, '密码错误');
            }
        }
    }
    
    public function validateCaptcha($attribute,$params){
        if(!\Yii::$service->helper->captcha->validateCaptcha($this->$attribute)){
            $this->addError($attribute , '验证码不正确');
        }
    }

    public function getAdminUser()
    {
        if ($this->_adminUser === null) {
            $this->_adminUser = AdminUser::findByUsername($this->username);
        }

        return $this->_adminUser;
    }

    /**
     * @property $duration | Int
     * 对于参数$duration：
     * 1. 当不开启cookie时，$duration的设置是无效的，yii2只会从user组件Yii::$app->user->authTimeout
     *    中读取过期时间
     * 2. 当开启cookie，$duration是有效的，会设置cookie的过期时间。
     *	  如果不传递时间，默认使用 Yii::$service->session->timeout的值。
     * 总之，为了方便处理cookie和session的超时时间，统一使用
     * session的超时时间，这样做的好处为，可以让account 和 cart session的超时时间保持一致
     */
    public function login($duration = 0)
    {
        if (!$duration) {
            if (Yii::$service->session->timeout) {
                $duration = Yii::$service->session->timeout;
            }
        }
        if ($this->validate()) {
            return \Yii::$app->user->login($this->getAdminUser(), $this->rememberMe ? 3600 * 24 * 30 : $duration);
        } else {
            return false;
        }
    }
}
