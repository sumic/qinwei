<?php
/**
 * =======================================================
 * @Description :wechat menu block
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年08月02日 17:21:14
 * @version: v1.0.0
 */
namespace appadmin\modules\Wechat\block\sync;

use Yii;

class Index{
    public function signature()
    {
        $signatrue = \Yii::$service->mpwechat->api->checkSignature();
        if($signatrue)return \Yii::$app->request->get('echostr');
    }
}
