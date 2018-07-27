<?php

namespace appadmin\rules;

use yii;
use yii\rbac\Rule;

/**
 * Class AuthAssignmentRule 删除角色的规则(不能删除超级管理员的角色)
 * @package backend\rules
 */
class AuthAssignmentRule extends Rule
{
    /**
     * @var string 定义名称
     */
    public $name = 'auth-assignment';

    /**
     * 执行验证
     * @param int|string $user
     * @param \yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($user, $item, $params)
    {
        $isReturn = false;
        $strItemName = empty($params['item_name']) ? Yii::$app->request->post('item_name') : $params['item_name'];
        $superName = \Yii::$service->admin->role::SUPER_ADMIN_NAME;
        $superId   = \Yii::$service->admin->user::SUPER_ADMIN_ID;
        if ($strItemName !== $superName || $user !== $superId) {
            $isReturn = true;
        }

        return $isReturn;
    }
}