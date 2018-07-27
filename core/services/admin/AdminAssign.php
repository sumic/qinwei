<?php
/**
 * =======================================================
 * @Description :assign service
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

class AdminAssign extends Service
{
    protected $_assignModelName = '\core\models\mysqldb\admin\AdminAssign';
    protected $_assignModel;
    
    public function init()
    {
        list($this->_assignModelName,$this->_assignModel) = \Yii::mapGet($this->_assignModelName);
    }
    //返回主键
    public function getPrimaryKey()
    {
        return 'item_name';
    }
    //返回模型
    public function actionGetModel()
    {
        return $this->_assignModel;
    }
    //根据主键查找数据
    public function getByPrimaryKey($primaryKey)
    {
        if ($primaryKey) {
            $one = $this->_assignModel->findOne($primaryKey);
            return $one;
        } else {
            return $this->_assignModel;
        }
    }
    //查询所有数据
    public function getAll()
    {
        $query = $this->_assignModel->find();
        return $query->all();
    }
    //保存数据
    public function save($param,$scenario = 'default')
    {
        if (empty($param['user_id']) || empty($param['item_name']) || !is_array($param['item_name'])) {
            Yii::$service->helper->errors->add('非法数据');
            return false;
        }
        foreach ($param['item_name'] as $name) {
            $model = new $this->_assignModel();
            $data['item_name'] = $name;
            $data['user_id'] = $param['user_id'];
            Yii::$service->helper->ar->save($model, $data);
        }
        return $model;
    }
    //删除数据
    public function remove()
    {
        $data = Yii::$app->request->post();
        if (empty($data['item_name']) || empty($data['user_id'])) {
            Yii::$service->helper->errors->add('非法数据');
            return false;
        }
        
        // 通过传递过来的唯一主键值查询数据
        /* @var $model \yii\db\ActiveRecord */
        $model = $this->_assignModel::findOne(['item_name' => $data['item_name'], 'user_id' => $data['user_id']]);
        if (empty($model)) {
            Yii::$service->helper->errors->add('数据不存在');
            return false;
        }
        
        // 删除数据成功
        if ($model->delete()) {
            return $model;
        } else {
            Yii::$service->helper->errors->addByModelErrors($model->getErrors());
            return false;
        }
    }
}