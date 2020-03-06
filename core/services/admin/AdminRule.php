<?php
/**
 * =======================================================
 * @Description :auth rule service
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

class AdminRule extends Service
{
    protected $_modelName = '\core\models\mysqldb\admin\AdminRule';
    protected $_model;
    
    public function init()
    {
        list($this->_modelName,$this->_model) = \Yii::mapGet($this->_modelName);
    }
    //返回主键
    public function getPrimaryKey()
    {
        return 'id';
    }
    //返回模型
    public function actionGetModel()
    {
        return $this->_model;
    }
    //根据主键查找数据
    public function getByPrimaryKey($primaryKey)
    {
        if ($primaryKey) {
            $one = $this->_model->findOne($primaryKey);
            return $one;
        } else {
            return $this->_model;
        }
    }
    //查询所有数据
    public function getAll()
    {
        $query = $this->_model->find();
        return $query->asArray()->all();
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
            $model = new $this->_model();
        }
        
        // 判断是否存在指定的验证场景，有则使用，没有默认
        $arrScenarios = $model->scenarios();
        if (isset($arrScenarios[$scenario])) {
            $model->scenario = $scenario;
        }
        //验证数据
        if (!$model->load($param, '')) {
            Yii::$service->helper->errors->addByModelErrors($model->getErrors());
            return false;
        }
        //写入数据
        if ($model->validate()) {
            /* @var $manager \yii\rbac\DbManager */
            $manager = Yii::$app->getAuthManager();
            $class = new $model->data;
            $class->name = $model->newName;
            // 新增数据
            if ($model->isNewRecord) {
                return $manager->add($class);
            } else {
                return $manager->update($model->name, $class);
            }
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
        $innerTransaction = Yii::$app->db->beginTransaction();
        if (is_array($ids) && !empty($ids)) {
            foreach ($ids as $id) {
                if(!$this->removeOne($id, $innerTransaction))return false;
            }
            $innerTransaction->commit();
            return true;
        } else {
            $id = $ids;
            $result = $this->removeOne($id, $innerTransaction);
            return $result ? $innerTransaction->commit() : false;
        }
    }
    
    public function removeOne($id,$innerTransaction){
        $manager = Yii::$app->getAuthManager();
        $model = $this->_model->findOne($id);
        if (isset($model[$this->getPrimaryKey()]) && !empty($model[$this->getPrimaryKey()])) {
            try {
                $class = unserialize($model->data);
                $class->name = $model->name;
                if(!$manager->remove($class))throw new \Exception("规则删除失败.");
            } catch (\Exception $e) {
                Yii::$service->helper->errors->add($e->getMessage(). "事务已回滚");
                $innerTransaction->rollBack();
                return false;
            }
            return true;
        } else {
            Yii::$service->helper->errors->add("ID $id 不存在.");
            return false;
        }
        
    }
}
