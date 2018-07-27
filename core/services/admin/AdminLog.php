<?php
/**
 * =======================================================
 * @Description :admin log service
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
use yii\db\Expression;
use yii\helpers\Json;

class AdminLog extends Service
{
    /**
     * 类型
     */
    const TYPE_CREATE = 1; // 创建
    const TYPE_UPDATE = 2; // 修改
    const TYPE_DELETE = 3; // 删除
    const TYPE_OTHER  = 4;  // 其他
    const TYPE_UPLOAD = 5;  // 上传
    
    protected $_modelName = '\core\models\mysqldb\admin\AdminLog';
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
        return $query->all();
    }
    
    /**
     * 创建日志
     * @param integer $type 类型
     * @param array $params 请求参数
     * @param string $index 数据唯一标识
     * @return bool
     */
    public function save($type, $params = [], $index = '')
    {
        $log = new $this->_modelName;
        $log->type = $type;
        $log->params = Json::encode($params);
        $log->controller = Yii::$app->controller->module->id.'/'.Yii::$app->controller->id;
        $log->action = Yii::$app->controller->action->id;
        $log->url = Yii::$app->request->url;
        $log->index = $index;
        $log->created_id = Yii::$app->user->id;
        $log->created_at = new Expression('UNIX_TIMESTAMP()');
        if(!$log->save()){
            Yii::$service->helper->errors->addByModelErrors($log->getErrors());
            return false;
        }
        return $log;
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
            $innerTransaction->commit();
            return $result;
        } else {
            $id = $ids;
            $result = $this->removeOne($id, $innerTransaction);
            if($result){
                $innerTransaction->commit();
                return $result;
            }
            return false;
        }
    }
    
    public function removeOne($id,$innerTransaction){
        $model = $this->_model->findOne($id);
        if (isset($model[$this->getPrimaryKey()]) && !empty($model[$this->getPrimaryKey()])) {
            try {
                if(!$model->delete())throw new \Exception("删除失败.");
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
    
    /**
     * 获取类型说明
     * @param null $type
     * @return array|mixed|null
     */
    public function getTypeDescription($type = null)
    {
        $mixReturn = [
            self::TYPE_CREATE => '创建',
            self::TYPE_CREATE => '创建',
            self::TYPE_UPDATE => '修改',
            self::TYPE_DELETE => '删除',
            self::TYPE_OTHER => '其他',
            self::TYPE_UPLOAD => '上传',
        ];
        
        if ($type !== null) {
            $mixReturn = isset($mixReturn[$type]) ? $mixReturn[$type] : null;
        }
        
        return $mixReturn;
    }
    
}
