<?php

/**
 * =======================================================
 * @Description :base service
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @version: v1.0.0
 */

namespace core\services\voice;

use core\services\Service;
use yii;
use yii\base\InvalidValueException;
use yii\helpers\ArrayHelper;

class Playback extends Service
{
    protected $_modelName = '\core\models\mysqldb\voice\Playback';
    protected $_model;

    public function init()
    {
        list($this->_modelName, $this->_model) = \Yii::mapGet($this->_modelName);
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

    public function getAllTranslate(){
        $result = $this->_model->find()
            //->select(['id', 'menu_name', 'pid'])
            ->where([
                '<>' ,'status','9'
            ])
            ->orwhere(['IS','content',new \yii\db\Expression('NULL')])
            ->indexBy('id')
            ->all();
        return $result;
    }
    public function save($param, $scenario = 'default')
    {
        $primaryVal = isset($param[$this->getPrimaryKey()]) ? $param[$this->getPrimaryKey()] : '';
        if ($primaryVal) {
            //更新数据
            $model = $this->getByPrimaryKey($primaryVal);

            if (!$model) {
                Yii::$service->helper->errors->add($this->getPrimaryKey() . ' 不存在');
                return false;
            }
        } else {
            //开始批量添加
            $innerTransaction = Yii::$app->db->beginTransaction();
        }

        try {
            //验证数据并添加
            foreach ($param['fid'] as $k => $v) {
                //新建数据
                $model = new $this->_model();
                // 判断是否存在指定的验证场景，有则使用，没有默认
                $arrScenarios = $model->scenarios();
                if (isset($arrScenarios[$scenario])) {
                    $model->scenario = $scenario;
                }
                $model->fid = $v;
                $model->name = $param['name'][$k];
                $model->cid = $param['cid'];
                if ($model->validate()) {
                    $model->save();
                } else {
                    Yii::$service->helper->errors->addByModelErrors($model->getErrors());
                    throw new \UnexpectedValueException(array_values($model->getFirstErrors())[0]);
                    return false;
                }
            }
            $innerTransaction->commit();
            return $model;
        } catch (\Exception $e) {
            Yii::$service->helper->errors->add($e->getMessage() . "事务已回滚");
            $innerTransaction->rollBack();
            //删除已经上传的文件
            Yii::$service->helper->uploader->remove($param['fid']);
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
        $ids = explode(',', $ids);
        if (is_array($ids) && !empty($ids)) {
            foreach ($ids as $id) {
                if (!$result = $this->removeOne($id, $innerTransaction)) return false;
            }
        }
        if ($result) {
            $innerTransaction->commit();
            return $result;
        } else {
            return false;
        }
    }

    public function removeOne($id, $innerTransaction)
    {
        $model = $this->_model->findOne($id);
        if (isset($model[$this->getPrimaryKey()]) && !empty($model[$this->getPrimaryKey()])) {
            try {
                $model->delete();
            } catch (\Exception $e) {
                Yii::$service->helper->errors->add($e->getMessage() . "事务已回滚");
                $innerTransaction->rollBack();
                return false;
            }
            //删除文件
            if (\Yii::$service->helper->uploader->getByPrimaryKey($model->fid)) {
                \Yii::$service->helper->uploader->remove($model->fid);
            }
            return $model;
        } else {
            Yii::$service->helper->errors->add("ID $id 不存在.");
            return false;
        }
    }


    public function updateTask($id, $param)
    {
        if ($id) {
            //更新数据
            
            $model = $this->getByPrimaryKey($id);
            if (!$model) {
                Yii::$service->helper->errors->add($this->getPrimaryKey() . ' 不存在');
                return false;
            } else {
                //更新taskid
                $model->taskid = $param['data'];
                //状态0 任务创建成功
                $model->status = 0;
                $model->scenario = 'update';
                if ($model->validate()) {
                    $model->save();
                } else {
                    Yii::$service->helper->errors->addByModelErrors($model->getErrors());
                    return false;
                }
            }
            return $model;
        }
    }

    public function updateStatus($id,$param){
        if ($id) {
            //更新数据
            $model = $this->getByPrimaryKey($id);
            if (!$model) {
                Yii::$service->helper->errors->add($this->getPrimaryKey() . ' 不存在');
                return false;
            } else {
                //状态1 上传成功
                $model->status = $param;

                if ($model->validate()) {
                    $model->save();
                } else {
                    Yii::$service->helper->errors->addByModelErrors($model->getErrors());
                    return false;
                }
            }
            return $model;
        }
    }

    public function updateChecked($id,$param){
        if ($id) {
            //更新数据
            $model = $this->getByPrimaryKey($id);
            if (!$model) {
                Yii::$service->helper->errors->add($this->getPrimaryKey() . ' 不存在');
                return false;
            } else {
                //状态1 上传成功
                $model->is_checked = $param;

                if ($model->validate()) {
                    $model->save();
                } else {
                    Yii::$service->helper->errors->addByModelErrors($model->getErrors());
                    return false;
                }
            }
            return $model;
        }
    }
}
