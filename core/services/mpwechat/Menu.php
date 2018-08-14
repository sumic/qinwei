<?php
/**
 * =======================================================
 * @Description :mpwechat menu service
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年08月02日 17:27:59
 * @version: v1.0.0
 */

namespace core\services\mpwechat;

use core\services\Service;
use yii;
use yii\base\InvalidValueException;
use yii\helpers\ArrayHelper;

class Menu extends Service
{
    protected $_modelName = '\core\models\mysqldb\wechat\Menu';
    protected $_model;
    
    public function init()
    {
        list($this->_modelName,$this->_model) = \Yii::mapGet($this->_modelName);
    }
    
    public function actionGetAll()
    {
        $result = $this->_model->find()
        //->select(['id', 'menu_name', 'pid'])
        ->where([
            'status' => $this->_model::STATUS_ACTIVE,
        ])
        ->indexBy('id')
        ->asArray()
        ->all();
        return $result;
    }
    
    public function actionGetModelName()
    {
        return get_class($this->_model);
    }
    
    public function actionGetModel()
    {
        return $this->_model;
    }
    
    public function getPrimaryKey()
    {
        return 'id';
    }
    
    public function getByPrimaryKey($primaryKey)
    {
        if ($primaryKey) {
            $one = $this->_model->findOne($primaryKey);
            return $one;
        } else {
            return new $this->_modelName();
        }
    }
    
    public function getByMpid($mpid)
    {
        if($mpid){
            return $this->_model->find()->where(["mpid" => $mpid])->asArray()->all();
        }
    }
    public function save($param,$scenario = 'default')
    {
        $buttons = json_decode($param['newv'],TRUE);
        $mpid = $param['mpid'];
        if($mpid && $buttons['button']){
            /**
             * 判断是否有删除的菜单
             */
            //获得一级菜单
            $ids = ArrayHelper::getColumn($buttons['button'], 'id');
            //获得子菜单并移除新增的
            foreach ($buttons['button'] as $v){
                $ids = array_filter(ArrayHelper::merge($ids, ArrayHelper::getColumn($v['sub_button'], 'id')));
            }
            //获得旧的菜单id;
            $oldIds = $this->_model->find()->select('id')->where(['mpid'=>$mpid])->asArray(true)->all();
            //计算新旧菜单差值
            $needDel = array_diff(ArrayHelper::getColumn($oldIds, 'id'),$ids);
            //删除菜单
            if(!empty($needDel)){
                $doDel = $this->remove($needDel);
            }
            $innerTransaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($buttons['button'] as $v)
                {
                    //添加一级菜单
                    $data['id']   = !empty($v['id']) ? $v['id'] : '';
                    $data['mpid'] = $mpid;
                    $data['name'] = $v['name'];
                    $data['type'] = $v['type'];
                    $data['pid']  = 0;
                    $data['message'] = $v['message'];
                    //保存数据
                    $topMenu = $this->saveOne($data, $scenario);
                    if(!$topMenu){
                        throw new \UnexpectedValueException(Yii::$service->helper->errors->get()[0]);
                    }
                    //添加二级菜单
                    //更新数据时候需要指定父菜单ID
                    $pid = $topMenu->isNewRecord ? Yii::$app->db->getLastInsertID() : $data['id'];
                    if($topMenu && is_array($v['sub_button']) && !empty($v['sub_button'])){
                        foreach ($v['sub_button'] as $v1){
                            $sub['id']   = empty($v1['id']) ? '' : $v1['id'];
                            $sub['mpid'] = $mpid;
                            $sub['pid']  = $pid;
                            $sub['name'] = $v1['name'];
                            $sub['type'] = $v1['type'];
                            $sub['message'] = $v1['message'];
                            //保存submenu
                            $subMenu = $this->saveOne($sub,$scenario);
                            if(!$subMenu){
                                throw new \UnexpectedValueException(Yii::$service->helper->errors->get()[0]);
                            }
                        }
                    }
                }
                $innerTransaction->commit();
                return true;
            }catch (\Exception $e){
                Yii::$service->helper->errors->add($e->getMessage()."事务已回滚");
                $innerTransaction->rollBack();
                return false;
            }
        }else{
            Yii::$service->helper->errors->add('没有菜单添加。');
            return false;
        }
    }
    
    public function saveOne($data,$scenario)
    {
        $primaryVal = isset($data[$this->getPrimaryKey()]) ? $data[$this->getPrimaryKey()] : '';
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
        //验证场景
        $arrScenarios = $model->scenarios();
        if (isset($arrScenarios[$scenario])) {
            $model->scenario = $scenario;
        }
        //读取wechat menu表数据
        if (!$model->load($data,''))
        {
            Yii::$service->helper->errors->addByModelErrors($model->getErrors());
            return false;
        }
        //验证写入wechat menu表
        if($model->validate() && $model->save())
        {
            return $model;
        }else{
            Yii::$service->helper->errors->addByModelErrors($model->getErrors());
            return false;
        }
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
        $model = $this->_model->findOne($id);
        if (isset($model[$this->getPrimaryKey()]) && !empty($model[$this->getPrimaryKey()])) {
            try {
                $model->delete();
            } catch (\Exception $e) {
                Yii::$service->helper->errors->add($e->getMessage(). "事务已回滚");
                $innerTransaction->rollBack();
                return false;
            }
            return $model;
        } else {
            Yii::$service->helper->errors->add("ID $id 不存在.");
            return false;
        }
    }
    
    public function removeByMpid($mpid)
    {
        if(!empty($mpid)){
            $this->_model->deleteAll(['mpid'=>$mpid]);
        }
    }
}