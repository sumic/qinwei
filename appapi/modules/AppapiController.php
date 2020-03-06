<?php
/**
 * =======================================================
 * @Description : TODO
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年7月24日 下午2:29:45
 * @version: v1.0.0
 */

namespace appapi\modules;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use core\yii\filters\auth\AppapiQueryParamAuth;  
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\RateLimiter; 


class AppapiController extends Controller
{
    public $blockNamespace;
    public $enableCsrfValidation = false ;
    
    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
    }


   public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;

        //$behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    /**
     * get current block
     * you can change $this->blockNamespace.
     */
    public function getBlock($blockName = '')
    {
        if (!$blockName) {
            $blockName = $this->action->id;
        }
        if (!$this->blockNamespace) {
            $this->blockNamespace = Yii::$app->controller->module->blockNamespace;
        }
        if (!$this->blockNamespace) {
            throw new \yii\web\HttpException(406, 'blockNamespace is empty , you should config it in module->blockNamespace or controller blockNamespace ');
        }

        $relativeFile = '\\'.$this->blockNamespace;
        $relativeFile .= '\\'.$this->id.'\\'.ucfirst($blockName);
        //查找是否在rewriteMap中存在重写
        $relativeFile = Yii::mapGetName($relativeFile);
        
        return new $relativeFile();
    }
}
