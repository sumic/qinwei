<?php
/**
 * =======================================================
 * @Description :wechat api async block
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年08月02日 17:21:14
 * @version: v1.0.0
 */
namespace appadmin\modules\Wechat\block\menu;

use Yii;
use yii\base\InvalidValueException;

class Async 
{
    public function menu()
    {
        $mpid = (int)\Yii::$app->request->post('mpid');
        if($mpid){
            $menuData = \Yii::$service->mpwechat->menu->getByMpid($mpid);
        }
        
        if($menuData){
            //格式化数据 type:1 click 2:view
            foreach ($menuData as $k=>$v){
                $v['type'] == 1 ? $menuData[$k]['type'] = 'click' : $menuData[$k]['type'] = 'view';
                if($v['type'] == 2){
                    $menuData[$k]['url'] = $menuData[$k]['message'];
                }else{
                    $menuData[$k]['key'] = $menuData[$k]['message'];
                    unset($menuData[$k]['message']);
                }
                unset($menuData[$k]['message']);
            }
            #return button tree array
            $treeParam['data'] = $menuData;
            $treeParam['parentIdName'] = 'pid';
            $treeParam['childrenName'] = 'sub_button';
            $buttons = \Yii::$service->helper->tree->setParam($treeParam)->getTreeArray(0);
           /*  foreach ($tree as $v){
                unset($v['id']);
                
            } */
            //同步微信服务器
            $result = \Yii::$service->mpwechat->api->createMenu($buttons);
            if($result['errcode'] == 0){
                return \Yii::$service->helper->json->success();
            }else{
                return \Yii::$service->helper->json->error(1004, $result['errcode']);
            }
        }else{
            return \Yii::$service->helper->json->error(1004, '未找到数据');
        }
    }
}
