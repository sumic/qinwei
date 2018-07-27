<?php
/**
 * =======================================================
 * @Description :roleedit block
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
namespace appadmin\modules\Role\block\main;

use Yii;
use \yii\web\UnauthorizedHttpException;

class Edit{
    
    public function getLastData($name)
    {
        //获取超管ID Name
        $adminId = \Yii::$service->admin->user::SUPER_ADMIN_ID;
        // 管理员直接返回
        $adminName = \Yii::$service->admin->role::SUPER_ADMIN_NAME;
        if ($name === $adminName) {
            Yii::$service->page->message->adderror('不能修改超级管理员的权限');
            Yii::$service->url->redirect(['role/main/view','name'=>$name]);
            return;
        }
        //判断自己是否有这个权限
        //用户ID
        $uid = Yii::$app->user->id; 
        //获得角色数据
        $data['role'] = \Yii::$service->admin->role->getrolebyname($name);
        if(!$data['role'])throw new UnauthorizedHttpException('角色不存在');
        // 获取用户是否有改权限
        $objAuth = Yii::$app->getAuthManager(); 
        // 权限对象
        $mixRoles = $objAuth->getAssignment($name, $uid);
        // 获取用户是否有改权限
        if (!$mixRoles && $uid != $adminId) {
            throw new UnauthorizedHttpException('对不起，您没有修改该角色的权限!');
        }
        //获得POST数据
        $params = \Yii::$app->request->post();
        if($params){
            $result = \Yii::$service->admin->role->edit($name);
        }
        //获得所有权限
        $data['permissions'] = \Yii::$service->admin->role->getPermissions();
        //获得角色权限
        $data['rolepermissions'] = \Yii::$service->admin->role->loadRolePermissions($name);
        //获得tree数据
        $treeData = \Yii::$service->admin->menu->getMenusByPermissions($data['permissions']);
        $trees = \Yii::$service->helper->tree->setParam([
            'parentIdName' => 'pid',
            'childrenName' => 'children',
            'data' => $treeData
        ])->getTreeArray(0);
        
        $data['trees'] = \Yii::$service->admin->menu->getJsMenus($trees, $data['rolepermissions']);
        return $data;
    }
    
}