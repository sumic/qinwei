<?php
/**
 * =======================================================
 * @Description :roleedit block
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
namespace appadmin\modules\Role\block\main;

use Yii;

class View{
    
    public function getLastData($name)
    {
        // 查询角色信息
        $data['model'] = \Yii::$service->admin->role->getrolebyname($name);
        if(!$data['model']){
            Yii::$service->page->message->adderror('没有数据或ID不正确');
            Yii::$service->url->redirect(['role/main/index']);
            return;
        }
        //获得所有权限
        $data['permissions'] = \Yii::$service->admin->role->getPermissions();
        
        // 获取角色权限信息
        $data['permissions'] = Yii::$app->authManager->getPermissionsByRole($name);
        //获得tree数据
        $treeData = \Yii::$service->admin->menu->getMenusByPermissions($data['permissions']);
        $trees = \Yii::$service->helper->tree->setParam([
            'parentIdName' => 'pid',
            'childrenName' => 'child',
            'data' => $treeData
        ])->getTreeArray(0);
        
        $data['menus'] = $trees;
        return $data;
    }
    
}