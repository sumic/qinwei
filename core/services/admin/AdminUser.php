<?php
/**
 * =======================================================
 * @Description :logs service
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
use core\helpers\CFunc;

class AdminUser extends Service
{
    /**
     * @var integer 超级管理员ID
     */
    const SUPER_ADMIN_ID = 1;
    
    protected $_userModelName = '\core\models\mysqldb\admin\AdminUser';
    protected $_userModel;
    
    
    public function init()
    {
        parent::init();
        list($this->_userModelName,$this->_userModel) = \Yii::mapGet($this->_userModelName);
    }
    //返回主键
    public function getPrimaryKey()
    {
        return 'id';
    }
    //返回模型
    public function actionGetModel()
    {
        return $this->_userModel;
    }
    //根据主键查找数据
    public function getByPrimaryKey($primaryKey)
    {
        if ($primaryKey) {
            $one = $this->_userModel->findOne($primaryKey);
            return $one;
        } else {
            return $this->_userModel;
        }
    }
    //查询所有数据
    public function getAll()
    {
        $query = $this->_userModel->find();
        return $query->all();
    }
    //查询当前启用的用户
    public function actionGetactiveuser(){
        $result = $this->_userModel->find()
        ->select('id,username')
        ->where(" status = :status ",[':status'=>$this->_userModel::STATUS_ACTIVE])
        ->asArray()->all();
        return $result;
    }
    
    public function getByCreateId($id,$createId)
    {
        if($id && $createId){
            return $this->_userModel->find()->where(['id' => $id, 'created_id' => $createId])->one();
        }else{
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
        if (is_array($ids) && !empty($ids)) {
            $innerTransaction = Yii::$app->db->beginTransaction();
            try {
                foreach ($ids as $id) {
                    $model = $this->_userModel->findOne($id);
                    if (isset($model[$this->getPrimaryKey()]) && !empty($model[$this->getPrimaryKey()])) {
                        $model->delete();
                    } else {
                        Yii::$service->helper->errors->add("删除失败:ID $id 不存在.");
                        $innerTransaction->rollBack();
                        return false;
                    }
                }
                $innerTransaction->commit();
            } catch (Exception $e) {
                Yii::$service->helper->errors->add('删除失败：事务已回滚');
                $innerTransaction->rollBack();
                return false;
            }
        } else {
            $id = $ids;
            $model = $this->_roleItemModel->findOne($id);
            if (isset($model[$this->getPrimaryKey()]) && !empty($model[$this->getPrimaryKey()])) {
                $innerTransaction = Yii::$app->db->beginTransaction();
                try {
                    $model->delete();
                    $innerTransaction->commit();
                } catch (Exception $e) {
                    Yii::$service->helper->errors->add('删除失败：事务已回滚');
                    $innerTransaction->rollBack();
                }
            } else {
                Yii::$service->helper->errors->add("删除失败:ID $id 不存在.");
                return false;
            }
        }
        return true;
    }
    
    public function save($param,$scenario = 'default')
    {
        $primaryVal = isset($param[$this->getPrimaryKey()]) ? $param[$this->getPrimaryKey()] : '';
        if ($primaryVal) {
            //更新数据
            $model = $this->getByPrimaryKey($primaryVal);
            if (!$model) {
                Yii::$service->helper->errors->add($this->getPrimaryKey().' 不存在');
                return;
            }
        } else {
            //新建数据
            $model = new $this->_userModel();
            if(isset($param['rule_name']) && empty($param['rule_name']))unset($param['rule_name']);
        }
        
        // 判断是否存在指定的验证场景，有则使用，没有默认
        $arrScenarios = $model->scenarios();
        if (isset($arrScenarios[$scenario])) {
            $model->scenario = $scenario;
        }
        //缩略图有变化则删除旧的缩略图
        if(!empty($model->getOldAttribute('face')) && ($model->getOldAttribute('face') !== $param['face'])){
            \Yii::$service->helper->uploader->removeByUrl($model->getOldAttribute('face'));
        }
        return Yii::$service->helper->ar->save($model, $param);
    }
    
    /**
     * getArrayStatus() 获取状态说明信息
     * @param integer|null $intStatus
     * @return array|string
     */
    public function getArrayStatus($intStatus = null)
    {
        $array = [
            $this->_userModel::STATUS_ACTIVE => '启用',
            $this->_userModel::STATUS_INACTIVE => '禁用',
            $this->_userModel::STATUS_DELETED => '删除',
        ];
        
        if ($intStatus !== null && isset($array[$intStatus])) {
            $array = $array[$intStatus];
        }
        
        return $array;
    }
    
    /**
     * getStatusColor() 获取状态值对应颜色信息
     * @param null $intStatus
     * @return array|mixed
     */
    public function getStatusColor($intStatus = null)
    {
        $array = [
            $this->_userModel::STATUS_ACTIVE => 'label-success',
            $this->_userModel::STATUS_INACTIVE => 'label-warning',
            $this->_userModel::STATUS_DELETED => 'label-danger',
        ];
        
        if ($intStatus !== null && isset($array[$intStatus])) {
            $array = $array[$intStatus];
        }
        
        return $array;
    }
    
    public function setUserAvatar($avatar)
    {
        $uid = \Yii::$app->user->id;
        $model = $this->getByPrimaryKey($uid);
        if($model){
            // 删除之前的图像信息
            if ($model->face && file_exists('.' . $model->face)) {
                @unlink('.' . $model->face);
                @unlink('.' . dirname($model->face) . '/thumb_' . basename($model->face));
            }
            $model->face = ltrim($avatar, '.');
            return $model->save();
        }else{
            return false;
        }
    }
    
    public function logout()
    {
        Yii::$app->user->logout();
    }
    
    public function logip()
    {
        // 用户登陆成功修改登录时间
        $uid = \Yii::$app->user->id;
        $admin = $this->getByPrimaryKey($uid);
        if ($admin) {
            $admin->last_time = time();
            $admin->last_ip = CFunc::get_real_ip();
            $admin->save();
        }
    }
}