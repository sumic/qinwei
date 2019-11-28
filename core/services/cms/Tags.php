<?php
/**
 * =======================================================
 * @Description :tags service
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @version: v1.0.0
 */

namespace core\services\cms;

use core\services\Service;
use yii;
use yii\base\InvalidValueException;
use yii\helpers\ArrayHelper;

class Tags extends Service
{
    protected $_modelName = '\core\models\mysqldb\cms\ArticleTags';
    protected $_model;
    //当前用户的缓存菜单KEY
    public $keyName = 'tag';
    
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
    
    public function removeByArticle($aid)
    {
        if(!empty($aid)) return $this->_model->deleteAll(['aid'=>(int)$aid]);
    }
    public function getTagsByArticle($aid, $isString=false)
    {
        $result = $this->_model->find()->where(['key'=>$this->keyName])->andWhere(['aid'=>$aid])->asArray()->all();
        if( $result === null ){
            if( $isString ){
                return '';
            }else{
                return [];
            }
        }
        $result = ArrayHelper::getColumn($result, 'name');
        if( $isString ){
            return implode(',', $result);
        }
        return $result;
    }
    
    public function setArticleTags($aid, $tags)
    {
        if( is_string($tags) ){
            if( empty($tags) ){
                $tags = [];
            }else {
                $tags = str_replace('，', ',', $tags);
                $tags = str_replace(' ', '', $tags);
                $tags = explode(',', $tags);
            }
        }
        $oldTags = $this->getTagsByArticle($aid);
        $needAdds = array_diff($tags, $oldTags);
        $needRemoves = array_diff($oldTags, $tags);
        foreach ($needAdds as $tag){
            $model = new $this->_modelName([
                'aid' => $aid,
                'key' => $this->keyName,
                'name' => $tag
            ]);
            $model->save();
        }
        
        foreach ($needRemoves as $tag){
            $this->_model->find()->where(['key'=>$this->keyName])->andwhere(['name'=>$tag])->andwhere(['aid'=>$aid])->one()->delete();
        }
    }
    
    public function getHotestTags($limit=12)
    {
        $tags = $this->_model->findBySql("select value,COUNT(value) as times from {$this->_model->tableName()} where `key`='{$this->keyName}' GROUP BY value order by times desc limit {$limit}")->asArray()->all();
        return ArrayHelper::map($tags, 'value', 'times');
    }
    
    public function getAidsByTag($tag)
    {
        $result = $this->_model->find()->where(['key'=>$this->keyName])->where(['name'=>$tag])->asArray()->all();
        if( $result === null ) return [];
        return ArrayHelper::getColumn($result, 'aid');
    }
}