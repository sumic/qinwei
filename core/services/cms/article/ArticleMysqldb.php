<?php
/**
 * =======================================================
 * @Description :cms article mysqldb service
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月18日
 * @version: v1.0.0
 */

namespace core\services\cms\article;

use core\services\Service;
use yii;

class ArticleMysqldb extends Service
{
    protected $_modelName = '\core\models\mysqldb\cms\Article';
    protected $_modelContentName = '\core\models\mysqldb\cms\ArticleContent';
    protected $_model;
    protected $_modelContent;
    //当前用户的缓存菜单KEY
    
    public function init()
    {
        list($this->_modelName,$this->_model) = \Yii::mapGet($this->_modelName);
        list($this->_modelContentName,$this->_modelContent) = \Yii::mapGet($this->_modelContentName);
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
            $modelContent = $this->_modelContent->findOne(['aid'=>$primaryVal]);
            if(!$modelContent){
                $modelContent = new $this->_modelContent;
            }
        } else {
            //新建数据
            $model = new $this->_model();
            $modelContent = new $this->_modelContent;
        }
        //开始事务
        $innerTransaction = Yii::$app->db->beginTransaction();
        if($model->isNewRecord)$modelContent->aid = Yii::$app->db->getLastInsertID();
        try {
            // 判断是否存在指定的验证场景，有则使用，没有默认
            $arrScenarios = $model->scenarios();
            if (isset($arrScenarios[$scenario])) {
                $model->scenario = $scenario;
            }
            //读取ARTICLE表数据
            if (!$model->load($param,''))
            {
                Yii::$service->helper->errors->addByModelErrors($model->getErrors());
                throw new \UnexpectedValueException(array_values($model->getFirstErrors())[0]);
            }
            
            //读取ARTICLE CONTENT表数据
            if (!$modelContent->load($param, ''))
            {
                Yii::$service->helper->errors->addByModelErrors($modelContent->getErrors());
                throw new \UnexpectedValueException(array_values($modelContent->getFirstErrors())[0]);
            }
            
            //验证写入article表
            if($model->validate())
            {
                //缩略图有变化则删除旧的缩略图
                if(!empty($model->getOldAttribute('thumb')) && ($model->getOldAttribute('thumb') !== $model->thumb)){
                    \Yii::$service->helper->uploader->removeByUrl($model->getOldAttribute('thumb'));
                }
                $article = $model->save();
            }else{
                Yii::$service->helper->errors->addByModelErrors($model->getErrors());
                throw new \UnexpectedValueException(array_values($model->getFirstErrors())[0]);
            }
            
            //验证写入article content表
            //新插入数据写入aid
            if($modelContent->isNewRecord)$modelContent->aid = Yii::$app->db->getLastInsertID();
            if($modelContent->validate())
            {
                $content = $modelContent->save();
            }else{
                Yii::$service->helper->errors->addByModelErrors($modelContent->getErrors());
                throw new \UnexpectedValueException(array_values($modelContent->getFirstErrors())[0]);
            }
            //插入TAGS
            \Yii::$service->cms->tags->setArticleTags($model->id,$model->tag);
            $innerTransaction->commit();
            return $model;
        } catch (\Exception $e) {
            Yii::$service->helper->errors->add($e->getMessage()."事务已回滚");
            $innerTransaction->rollBack();
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
                //删除文章
                $model->delete();
                //删除缩略图
                if(!empty($model->thumb))\Yii::$service->helper->uploader->removeByUrl($model->thumb);
                //删除tags
                \Yii::$service->cms->tags->removeByArticle($model->id);
                //删除内容
                $modelContet = $this->_modelContent->findOne(['aid'=>$model->id]);
                if (isset($modelContet[$this->getPrimaryKey()]) && !empty($modelContet[$this->getPrimaryKey()])) {
                    $modelContet->delete();
                }
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
    
}