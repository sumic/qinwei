<?php
/**
 * =======================================================
 * @Description :admin auth service
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */

namespace core\services;
use Yii;
use yii\rbac\ManagerInterface;

class Auth extends Service implements ManagerInterface
{
    //角色模型
    //protected $_roleModelName = '\core\models\mysqldb\admin\AdminRole';
    //protected $_roleModel;
    
    //角色&权限模型
    protected $_roleItemModelName = '\core\models\mysqldb\admin\AdminRoleItem';
    protected $_roleItemModel;
    
     public function init(){
        parent::init();
        //list($this->_roleModelName,$this->_roleModel) = \Yii::mapGet($this->_roleModelName);
        list($this->_roleItemModelName,$this->_roleItemModel) = \Yii::mapGet($this->_roleItemModelName);
    }
    
    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::createRole()
     */
    public function createRole($name)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::createPermission()
     */
    public function createPermission($name)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::add()
     */
    public function add($object)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::remove()
     */
    public function remove($object)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::update()
     */
    public function update($name, $object)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::getRole()
     */
    public function getRole($name)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::getRoles()
     */
    public function getRoles()
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::getRolesByUser()
     */
    public function getRolesByUser($userId)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::getChildRoles()
     */
    public function getChildRoles($roleName)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::getPermission()
     */
    public function getPermission($name)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::getPermissions()
     */
    public function getPermissions()
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::getPermissionsByRole()
     */
    public function getPermissionsByRole($roleName)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::getPermissionsByUser()
     */
    public function getPermissionsByUser($userId)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::getRule()
     */
    public function getRule($name)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::getRules()
     */
    public function getRules()
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::canAddChild()
     */
    public function canAddChild($parent, $child)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::addChild()
     */
    public function addChild($parent, $child)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::removeChild()
     */
    public function removeChild($parent, $child)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::removeChildren()
     */
    public function removeChildren($parent)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::hasChild()
     */
    public function hasChild($parent, $child)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::getChildren()
     */
    public function getChildren($name)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::assign()
     */
    public function assign($role, $userId)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::revoke()
     */
    public function revoke($role, $userId)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::revokeAll()
     */
    public function revokeAll($userId)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::getAssignment()
     */
    public function getAssignment($roleName, $userId)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::getAssignments()
     */
    public function getAssignments($userId)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::getUserIdsByRole()
     */
    public function getUserIdsByRole($roleName)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::removeAll()
     */
    public function removeAll()
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::removeAllPermissions()
     */
    public function removeAllPermissions()
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::removeAllRoles()
     */
    public function removeAllRoles()
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::removeAllRules()
     */
    public function removeAllRules()
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\ManagerInterface::removeAllAssignments()
     */
    public function removeAllAssignments()
    {
        // TODO Auto-generated method stub
        
    }

    /**
     * {@inheritDoc}
     * @see \yii\rbac\CheckAccessInterface::checkAccess()
     */
    public function checkAccess($userId, $permissionName, $params = array())
    {
        // TODO Auto-generated method stub
        
    }

}
