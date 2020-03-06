<?php
/**
 * =======================================================
 * @Description :menu service
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */

namespace core\services\admin;

use core\services\Service;
use yii;
use yii\base\InvalidValueException;
use yii\helpers\ArrayHelper;

class AdminMenu extends Service
{
    protected $_menuModelName = '\core\models\mysqldb\admin\AdminMenu';
    protected $_menuModel;
    //当前用户的缓存菜单KEY
    
    public function init()
    {
        list($this->_menuModelName,$this->_menuModel) = \Yii::mapGet($this->_menuModelName);
    }
    
    public function actionGetAll()
    {
        $result = $this->_menuModel->find()
        //->select(['id', 'menu_name', 'pid'])
        ->where([
            'status' => $this->_menuModel::STATUS_ACTIVE,
        ])
        ->indexBy('id')
        ->asArray()
        ->orderBy('sort Asc')
        ->all();
        return $result;
    }
    
    public function actionGetModel()
    {
        return $this->_menuModel;
    }
    
    public function getPrimaryKey()
    {
        return 'id';
    }
    
    public function getByPrimaryKey($primaryKey)
    {
        if ($primaryKey) {
            $one = $this->_menuModel->findOne($primaryKey);
            return $one;
        } else {
            return new $this->_menuModelName();
        }
    }
    
    public function save($param,$scenario = 'default')
    {
        $primaryVal = isset($param[$this->getPrimaryKey()]) ? $param[$this->getPrimaryKey()] : '';
        if ($primaryVal) {
            //更新数据
            $model = $this->getByPrimaryKey($primaryVal);
            if (!$model) {
                Yii::$service->helper->errors->add($this->getPrimaryKey().' 不存在');
                return false;
            }
        } else {
            //新建数据
            $model = new $this->_menuModel();
        }
        // 判断是否存在指定的验证场景，有则使用，没有默认
        $arrScenarios = $model->scenarios();
        if (isset($arrScenarios[$scenario])) {
            $model->scenario = $scenario;
        }
        $this->aftetSave();
        return Yii::$service->helper->ar->save($model, $param);
    }
    
    //根据ID删除数据，使用了事务
    public function remove($ids)
    {
        if (!$ids) {
            Yii::$service->helper->errors->add('没有选中删除项。');
            return false;
        }
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
        $model = $this->_menuModel->findOne($id);
        if (isset($model[$this->getPrimaryKey()]) && !empty($model[$this->getPrimaryKey()])) {
            try {
                $model->delete();
            } catch (\Exception $e) {
                Yii::$service->helper->errors->add($e->getMessage(). "事务已回滚");
                $innerTransaction->rollBack();
                return false;
            }
            $this->aftetSave();
            return $model;
        } else {
            Yii::$service->helper->errors->add("ID $id 不存在.");
            return false;
        }
    }
    /**
     * 通过权限获取导航栏目
     * @param array $permissions 权限信息
     * @return array
     */
    public function getMenusByPermissions($permissions)
    {
        // 查询导航栏目
        $menus = $this->findMenus(['url' => array_keys($permissions), 'status' => $this->_menuModel::STATUS_ACTIVE]);
        if ($menus) {
            $sort = ArrayHelper::getColumn($menus, 'sort');
            array_multisort($sort, SORT_ASC, $menus);
        }
        
        return $menus;
    }
    
    /**
     * @param integer|array $where 查询条件
     * @return array
     */
    public function findMenus($where)
    {
        $parents = $this->_menuModel->find()->where($where)->asArray()->indexBy('id')->all();
        if ($parents) {
            $arrParentIds = [];
            foreach ($parents as $value) {
                if ($value['pid'] != 0 && !isset($parents[$value['pid']])) {
                    $arrParentIds[] = $value['pid'];
                }
            }
            
            if ($arrParentIds) {
                $arrParents = $this->findMenus(['id' => $arrParentIds]);
                if ($arrParents) {
                    $parents += $arrParents;
                }
            }
        }
        return $parents;
    }
    /**
     * 获取jstree 需要的数据
     *
     * @param array $array 数据信息
     * @param array $arrHaves  需要选中的数据
     * @return array
     */
    public static function getJsMenus($array, $arrHaves)
    {
        if (empty($array) || !is_array($array)) {
            return [];
        }
        
        $arrReturn = [];
        foreach ($array as $value) {
            $array = [
                'text' => $value['menu_name'],
                'id' => $value['id'],
                'data' => $value['url'],
                'state' => [],
            ];
            
            $array['state']['selected'] = in_array($value['url'], $arrHaves);
            $array['icon'] = $value['pid'] == 0 || !empty($value['children']) ? 'menu-icon fa fa-list orange' : false;
            if (!empty($value['children'])) {
                $array['children'] = self::getJsMenus($value['children'], $arrHaves);
            }
            
            $arrReturn[] = $array;
        }
        
        return $arrReturn;
    }
    
    //保存更新后删除菜单缓存
    protected function aftetSave()
    {
        $menuCacheKey =  Yii::$service->page->widget->getcachekey('menu');
        $menuCacheKey = substr($menuCacheKey,0,strripos($menuCacheKey,'_'));
        $users = \Yii::$service->admin->user->getall();
        foreach ($users as $v){
            \Yii::$app->cache->delete($menuCacheKey.'_'.$v->id);
        }
        return;
    }
}