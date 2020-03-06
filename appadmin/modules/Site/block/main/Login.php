<?php
/**
 * =======================================================
 * @Description :login block
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月17日
 * @version: v1.0.0
 */

namespace appadmin\modules\site\block\main;

use core\helper\mailer\Email;
use Yii;

class Login
{
    public function getLastData($param = '')
    {
        $loginParam = \Yii::$app->getModule('admin')->params['login'];
        $loginPageCaptcha = isset($loginParam['loginPageCaptcha']) ? $loginParam['loginPageCaptcha'] : false;

        return [
            'loginPageCaptcha' => $loginPageCaptcha,
            'model' =>\Yii::$service->admin->userlogin->getUserLoginModel($param),
            //'googleLoginUrl' => Yii::$service->customer->google->getLoginUrl('customer/google/loginv'),
            //'facebookLoginUrl' => Yii::$service->customer->facebook->getLoginUrl('customer/facebook/loginv'),
        ];
    }

    public function login($param)
    {
        if (is_array($param) && !empty($param)) {
            if (Yii::$service->admin->userlogin->login($param)) {
                // 发送邮件
                if ($param['email']) {
                    $this->sendLoginEmail($param);
                }
            }
        }
        Yii::$service->page->message->addByHelperErrors();
    }

    /**
     * 发送登录邮件.
     */
    public function sendLoginEmail($param)
    {
        if ($param) {
            Yii::$service->email->customer->sendLoginEmail($param);
        }
    }
}
