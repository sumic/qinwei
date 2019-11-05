<?php
/**
 * =======================================================
 * @Description :admin控制器基类
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年3月30日
 * @version: v1.0.0
 *
 */
namespace appadmin\modules;
use Yii;
use core\controllers\BaseController;
use yii\web\UnauthorizedHttpException;
use yii\helpers\Json;

class AppadminController extends BaseController{
    
    public $blockNamespace;
    
    public function __construct($id, $module, $config = [])
    {
       parent::__construct($id, $module, $config);
    }
    
    #进行账户权限的验证。
    public function beforeAction($action)
    {
        # 进行是否登录的验证
        $rolename = $action->controller->module->id. '/' . $action->controller->id . '/' . $action->id;
        
        if (Yii::$app->user->isGuest) {
            Yii::$service->url->redirectByUrlKey('site/main/login',['t'=>$rolename]);
            return;
        }
        
        //验证权限 ueditor编辑器不需要验证
        if ($action->controller->id != 'ueditor' && !Yii::$app->user->can($rolename) && Yii::$app->getErrorHandler()->exception === null)
        {
            // 没有权限AJAX返回
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->statusCode = 401;
                Yii::$app->response->content = Json::encode(\Yii::$service->helper->json->error(216));
                return false;
            }
            
            throw new UnauthorizedHttpException('对不起，您现在还没获得该操作的权限!');
        }
        parent::beforeAction($action);
       return true;
    }

    
    
    public function actionSearch()
    {
        $data = $this->getBlock('index')->getLastData();
        return \Yii::$service->helper->json->success($data['tables']);
    }
    public function actionCreate()
    {
        return $this->getBlock('index')->update('create');
    }
    public function actionUpdate()
    {
        return $this->getBlock('index')->update('update');
    }
    public function actionDelete()
    {
        return $this->getBlock('index')->delete();
    }
    public function actionUpload()
    {
        return $this->getBlock('index')->upload();
    }
}
?>
