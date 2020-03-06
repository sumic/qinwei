<?php
/**
 * =======================================================
 * @Description :cms article service
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月18日
 * @version: v1.0.0
 */
namespace core\services\cms;

use core\services\cms\article\ArticleMongodb;
use core\services\cms\article\ArticleMysqldb;
use core\services\Service;
use Yii;


class Article extends Service
{
    /**
     * $storagePrex , $storage , $storagePath 为找到当前的storage而设置的配置参数
     * 可以在配置中更改，更改后，就会通过容器注入的方式修改相应的配置值
     */
    public $storage     = 'ArticleMongodb';   // 当前的storage，如果在config中配置，那么在初始化的时候会被注入修改
    /**
     * 设置storage的path路径，
     * 如果不设置，则系统使用默认路径
     * 如果设置了路径，则使用自定义的路径
     */
    public $storagePath = ''; 
    protected $_article;

    public function init()
    {
        parent::init();
        $currentService = $this->getStorageService($this);
         
        $this->_article = new $currentService();
       
    }

    /**
     * get artile's primary key.
     */
    protected function actionGetPrimaryKey()
    {
        return $this->_article->getPrimaryKey();
    }

    /**
     * get artile model by primary key.
     */
    protected function actionGetByPrimaryKey($primaryKey)
    {
        return $this->_article->getByPrimaryKey($primaryKey);
    }
    /**
     * @property $urlKey | String ,  对应表的url_key字段
     * 根据url_key 查询得到article model
     */
    protected function actionGetByUrlKey($urlKey)
    {
        return $this->_article->getByUrlKey($urlKey);
    }

    /**
     * 得到category model的全名.
     */
    protected function actionGetModelName()
    {
        return get_class($this->_article);
    }
    
    /**
     * 返回model.
     */
    protected function actionGetModel()
    {
        return $this->_article->getmodel();
    }

    
    /**
     * @property $one|array , save one data .
     * @property $originUrlKey|string , article origin url key.
     * save $data to cms model,then,add url rewrite info to system service urlrewrite.
     */
    protected function actionSave($one, $originUrlKey)
    {
        return $this->_article->save($one, $originUrlKey);
    }

    protected function actionRemove($ids)
    {
        return $this->_article->remove($ids);
    }
}
