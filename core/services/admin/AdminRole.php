<?php
/**
 * =======================================================
 * @Description :role service
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
use yii\helpers\ArrayHelper;

class AdminRole extends Service
{
    protected $_roleItemModelName = '\core\models\mysqldb\admin\AdminRoleItem';
    protected $_roleItemModel;
    
    /**
     * @var string 定义超级管理员角色
     */
    const SUPER_ADMIN_NAME = 'administrator';

    /**
     * @var array 默认的权限
     */
    public $array_default_auth = [
        'index'      => '显示数据',
        'search'     => '搜索数据',
        'create'     => '添加数据',
        'update'     => '修改数据',
        'delete'     => '删除数据',
        'delete-all' => '批量删除',
        'export'     => '导出数据'
    ];
    
    /**
     * @var array 权限信息
     */
    public $_permissions = [];

    public function init()
    {
        list($this->_roleItemModelName,$this->_roleItemModel) = \Yii::mapGet($this->_roleItemModelName);
    }
    //返回主键
    public function getPrimaryKey()
    {
        return 'id';
    }
    //返回模型
    public function actionGetModel()
    {
        return $this->_roleItemModel;
    }
    //根据主键查找数据
    public function getByPrimaryKey($primaryKey)
    {
        if ($primaryKey) {
            $one = $this->_roleItemModel->findOne($primaryKey);
            return $one;
        } else {
            return $this->_roleItemModel;
        }
    }
     //根据名称查找数据
    public function actionGetRoleByName($name)
    {
        if ($name) {
            $one = $this->_roleItemModel->findOne(['name'=>$name]);
            return $one;
        }else {
            return $this->_roleItemModel;
        }
    }
    //查询所有数据
    public function getAll()
    {
        $query = $this->_roleItemModel->find();
        return $query->all();
    }
    //查询所有角色
    public function getAllRoleArray($isDelete = true)
    {
        $uid = Yii::$app->user->id;    // 用户ID
        $auth = Yii::$app->authManager; // 权限对象
        
        if($uid && $auth){
            // 管理员
            $superID = \Yii::$service->admin->user::SUPER_ADMIN_ID;
            $superName = self::SUPER_ADMIN_NAME;
            $roles = $uid == $superID ? $auth->getRoles() : $auth->getRolesByUser($uid);
            if ($roles && $isDelete && isset($roles[$superName])) {
                unset($roles[$superName]);
            }
            return ArrayHelper::map($roles, 'name', 'description');
        }else{
            return array();
        }
    }
    //保存数据
    public function save($param,$scenario = 'default')
    {
        $primaryVal = isset($param[$this->getPrimaryKey()]) ? $param[$this->getPrimaryKey()] : '';
        if ($primaryVal) {
            //更新数据
            $model = $this->getByPrimaryKey($param[$this->getPrimaryKey()]);
            if (!$model) {
                Yii::$service->helper->errors->add($this->getPrimaryKey().' 不存在');
                return;
            }
        } else {
            //新建数据
            $model = new $this->_roleItemModel();
            if(isset($param['rule_name']) && empty($param['rule_name']))unset($param['rule_name']);
        }
        
        // 判断是否存在指定的验证场景，有则使用，没有默认
        $arrScenarios = $model->scenarios();
        if (isset($arrScenarios[$scenario])) {
            $model->scenario = $scenario;
        }
        
        if (!$model->load($param, '')) {
            Yii::$service->helper->errors->addByModelErrors($model->getErrors());
            return false;
        }
        if($model->validate()){
            $type = (int)$model->type;
            $auth = Yii::$app->getAuthManager();
            //新增数据
            if ($model->isNewRecord) {
                if ($type === $this->_roleItemModel::TYPE_ROLE) {
                    // 角色
                    $item = $auth->createRole($model->newName);
                } else {
                    // 权限
                    $item = $auth->createPermission($model->newName);
                    if ($model->rule_name) {
                        $item->ruleName = $model->rule_name;
                    }
                }
                
                $item->description = $model->description;
                if ($model->data) {
                    $item->data = $model->data;
                }
                
                // 添加数据
                $auth->add($item);
                if ($type === $this->_roleItemModel::TYPE_PERMISSION) {
                    // 添加权限的话，要给超级管理员加上
                    $admin = $auth->getRole(Yii::$app->params['adminRoleName']);
                    if ($admin) {
                        $auth->addChild($admin, $item);
                    }
                } else {
                    // 将角色添加给用户
                    $uid = (int)Yii::$app->user->id;
                    $superid = \Yii::$service->admin->user::SUPER_ADMIN_ID;
                    if ($uid !== $superid) {
                        $auth->assign($item, $uid);
                    }
                }
            } else {
                if ($type === $this->_roleItemModel::TYPE_ROLE) {
                    // 角色
                    $item = $auth->getRole($model->name);
                } else {
                    // 权限
                    $item = $auth->getPermission($model->name);
                    if ($model->rule_name) {
                        $item->ruleName = $model->rule_name;
                    }
                }
                
                $item->name = $model->newName;
                $item->description = $model->description;
                if ($model->data) {
                    $item->data = $this->data;
                }
                return $auth->update($model->name, $item);
            }
            
            return $model;
        }
        Yii::$service->helper->errors->addByModelErrors($model->getErrors());
        return false;
    }
    
    //根据ID删除数据，使用了事务
    public function remove($ids)
    {
        if (!$ids) {
            Yii::$service->helper->errors->add('没有选中删除项。');
            return false;
        }
        $auth = Yii::$app->getAuthManager();
        $innerTransaction = Yii::$app->db->beginTransaction();
        if (is_array($ids) && !empty($ids)) {
            foreach ($ids as $id) {
                if(!$result = $this->removeOne($id, $innerTransaction))return false;
            }
        } else {
            $id = $ids;
            $result = $this->removeOne($id, $innerTransaction);
        }
        if($result){
            $innerTransaction->commit();
            return $result;
        }else{
            return false;
        }
    }
    
    public function removeOne($id,$innerTransaction){
        $auth = Yii::$app->getAuthManager();
        $model = $this->_roleItemModel->findOne($id);
        if (isset($model[$this->getPrimaryKey()]) && !empty($model[$this->getPrimaryKey()])) {
            try {
                //删除权限
                if($model->type == $this->_roleItemModel::TYPE_PERMISSION){
                    $item = $auth->getPermission($model->name);
                    if(!$item)throw new \Exception("$model->name 不存在.");
                    if(!$auth->remove($item))throw new \Exception("$model->name 不存在.");
                }else{
                    // 角色
                    if ($this->hasUsersByRole($model->name) || $model->name == Yii::$app->params['adminRoleName']) {
                        throw new \Exception("角色 $model->name 还在使用.");
                    }
                    // 清除这个角色的所有权限
                    $role = $auth->getRole($model->name);
                    $permissions = $auth->getPermissionsByRole($model->name);
                    foreach ($permissions as $permission) {
                        if(!$auth->removeChild($role, $permission)){
                            throw new \Exception("删除角色权限失败: $permission.");
                        }
                    }
                    // 删除角色成功
                    if(!$auth->remove($role)) throw new \Exception("删除角色失败: $role.");
                }
            } catch (\Exception $e) {
                Yii::$service->helper->errors->add($e->getMessage(). "删除事务已回滚");
                $innerTransaction->rollBack();
                return false;
            }
            return $model;
        } else {
            Yii::$service->helper->errors->add("ID $id 不存在.");
            return false;
        }
        
    }
    public function edit($name)
    {
        $params = \Yii::$app->request->post();
        $model = $this->getRoleByName($name);
        
        $permissions = isset($params['AdminRoleItem']['_permissions']) && is_array($params['AdminRoleItem']['_permissions']) ?
                        $params['AdminRoleItem']['_permissions']:[];
        if($model && $model->load($params)){
            if ($model->validate()) {
                $auth = Yii::$app->getAuthManager();
                $role = $auth->getRole($name);
                $role->description = $model->description;
                // save role
                if ($auth->update($name, $role)) {
                    // remove old permissions
                    $oldPermissions = $auth->getPermissionsByRole($name);
                    foreach ($oldPermissions as $permission) {
                        $auth->removeChild($role, $permission);
                    }
                    
                    // add new permissions
                    foreach ($permissions as $permission) {
                        $obj = $auth->getPermission($permission);
                        $auth->addChild($role, $obj);
                    }
                    Yii::$service->page->message->addcorrect('修改成功！');
                    Yii::$service->url->redirectByUrlKey('/role/main/view',['name'=>$model->name]);
                    return true;
                }
            }
        }
        Yii::$service->helper->errors->addByModelErrors($model->getErrors());
        return false;
    }
    
    public function getPermissions()
    {
        $uid = Yii::$app->user->id;
        $superId = \Yii::$service->admin->user::SUPER_ADMIN_ID;
        $models = $uid == $superId ? $this->_roleItemModel->find()->where([
            'type' => $this->_roleItemModel::TYPE_PERMISSION
        ])->orderBy(['name' => SORT_ASC])->all() : Yii::$app->getAuthManager()->getPermissionsByUser($uid);
        $permissions = [];
        foreach ($models as $model) {
            $permissions[$model->name] = $model->name . ' (' . $model->description . ')';
        }
        return $permissions;
    }
    
    
    public function loadRolePermissions($name)
    {
        $models = Yii::$app->authManager->getPermissionsByRole($name);
        foreach ($models as $model) {
            $this->_roleItemModel->_permissions[] = $model->name;
        }
        return $this->_roleItemModel->_permissions;
    }
    
    public function hasUsersByRole($name)
    {
        $tablePrefix = Yii::$app->getDb()->tablePrefix;
        return $this->_roleItemModel->find()
        ->where(['name' => $name])
        ->InnerJoin("{$tablePrefix}auth_assignment", ['item_name' => $name])
        ->count();
    }
    
       /**
     * 获取dataTable 表格需要的权限
     *
     * @param string $user 使用的用户名称
     *
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDataTableAuth($user = 'admin')
    {
        $controller = explode('/', Yii::$app->controller->action->getUniqueId());
        array_pop($controller);
        $controller = implode('/', $controller) . '/';
        $arrReturn  = [
            'buttons'    => ['create' => ['show' => true]],
            'operations' => ['see' => ['show' => true]],
        ];

        // 添加
        if (!Yii::$app->get($user)->can($controller . 'create')) {
            $arrReturn['buttons']['create'] = null;
        }

        // 删除全部
        if (!Yii::$app->get($user)->can($controller . 'delete-all')) {
            $arrReturn['buttons']['deleteAll'] = null;
        }

        // 导出
        if (!Yii::$app->get($user)->can($controller . 'export')) {
            $arrReturn['buttons']['export'] = null;
        }

        // 删除
        if (!Yii::$app->get($user)->can($controller . 'delete')) {
            $arrReturn['operations']['delete'] = null;
        }

        // 修改
        if (!Yii::$app->get($user)->can($controller . 'update')) {
            $arrReturn['buttons']['updateAll'] = $arrReturn['operations']['update'] = null;
        }

        return $arrReturn;
    }
}