<?php

namespace appadmin\modules\Site\controllers;

use Yii;
use core\controllers\BaseController;

class MainController extends BaseController
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            //'captcha' => [
            //    'class' => 'yii\captcha\CaptchaAction',
            //    'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            //],
        ];
    }
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return Yii::$service->url->redirectByUrlKey('site/main/login');
        }
        Yii::$service->page->theme->layoutFile = false;
        return $this->render($this->action->id);
    }
    
    public function actionSystem()
    {
        // 用户信息
        Yii::$app->view->params['user'] = Yii::$app->getUser()->identity;
        return $this->render($this->action->id,[
            'yii' => 'Yii ' . Yii::getVersion(),                      // Yii 版本
            'upload' => ini_get('upload_max_filesize'),      // 上传文件大小
        ]);
    }
    
    public function actionLogin()
    {
        \Yii::$service->page->theme->layoutFile = "login.php";
        
        if (!Yii::$app->user->isGuest) {
            return Yii::$service->url->redirectByUrlKey('/');
        }
        $param = Yii::$app->request->post('AdminUserLogin');
        if (!empty($param) && is_array($param)) {
            $this->getBlock()->login($param);
            if (!Yii::$app->user->isGuest) {
                $t = \Yii::$app->request->get('t');
                return $t ? Yii::$service->admin->userlogin->loginSuccessRedirect($t) : Yii::$service->admin->userlogin->loginSuccessRedirect('/');
            }
        }
        $data = $this->getBlock()->getLastData($param);
        return $this->render($this->action->id, $data);
    }
    
    /**
     * 登出账户.
     */
    public function actionLogout()
    {
        $rt = Yii::$app->request->get('rt');
        if (!Yii::$app->user->isGuest) {
            \Yii::$service->admin->user->logout();
        }
        if ($rt) {
            $redirectUrl = base64_decode($rt);
            $redirectUrl = \Yii::$service->helper->htmlEncode($redirectUrl);
            //exit;
            Yii::$service->url->redirect($redirectUrl);
        } else {
            Yii::$service->url->redirect(Yii::$service->url->HomeUrl());
        }
    }
    
    public function actionCaptcha()
    {
        Yii::$service->helper->captcha->height = 34;
        Yii::$service->helper->captcha->fontsize = 18;
        Yii::$service->helper->captcha->doimg();
        exit;
    }
    
    public function actionTest()
    {
        $identity = Yii::$app->user->identity;
        $identity->generateAccessToken();
        $identity->access_token_created_at = time();
        $identity->scenario = 'api';
        $result = $identity->save();
        exit;
    }
}
