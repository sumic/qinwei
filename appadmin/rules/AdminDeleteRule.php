<?php

namespace appadmin\rules;

use yii;
use yii\rbac\Rule;

/**
 * Class AdminRule 管理员的删除的权限控制
 * 不能删除超级管理员和自己的信息
 * @package backend\rules
 */
class AdminDeleteRule extends Rule
{
    /**
     * @var string 定义名称
     */
    public $name = 'admin-delete';

    /**
     * 执行验证
     * @param int|string $user
     * @param \yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($user, $item, $params)
    {
        $isReturn = true;
        // 先使用传递的值，再使用请求的值
        $id = intval(empty($params['id']) ? Yii::$app->request->post('id') : $params['id']);
        // 不能删除自己和超级管理员
        $superName = \Yii::$service->admin->role::SUPER_ADMIN_NAME;
        $superId   = \Yii::$service->admin->user::SUPER_ADMIN_ID;
        if ($id === $superId || $id == $user) {
            //不能删除自己
            $isReturn = false;
        } else {
            // 不是超级管理员添加验证
            if ($user !== $superId) {
                // 查询数据，先验证自己的修改自己或者修改自己添加的
                $admin = \Yii::$service->admin->user->getByCreateId($id,$user);
                $isReturn = $admin ? true : false;
            }
        }

        return $isReturn;
    }
}