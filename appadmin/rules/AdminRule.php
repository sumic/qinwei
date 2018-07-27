<?php

namespace appadmin\rules;

use yii;
use yii\rbac\Rule;

/**
 * Class AdminRule 管理员编辑权限控制
 * 只能修改自己或者自己添加的管理员信息
 * @package backend\rules
 */
class AdminRule extends Rule
{
    /**
     * @var string 定义名称
     */
    public $name = 'admin';

    /**
     * 执行验证
     * @param int|string $user
     * @param \yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($user, $item, $params)
    {
        $superName = \Yii::$service->admin->role::SUPER_ADMIN_NAME;
        $superId   = \Yii::$service->admin->user::SUPER_ADMIN_ID;
        // 管理员不做验证
        if ($user === $superId) {
            return true;
        } else {
            // 先使用传递的值，再使用请求的值
            $id = empty($params['id']) ? Yii::$app->request->post('id') : $params['id'];
            if ($id) {
                // 查询数据，先验证自己的修改自己或者修改自己添加的
                $admin = Yii::$service->admin->user->getByPrimaryKey($id);
                if ($admin && ($admin->id === $user || $admin->created_id === $user)) {
                    return true;
                }
            }
        }

        return false;
    }
}