<?php
/**
 * =======================================================
 * @Description :cms main block
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
namespace appadmin\modules\Cms\block\main;

use Yii;
use yii\helpers\ArrayHelper;

class Edit extends Index
{
    
    public function init(){
        parent::init();
    }
    
    public function getLastData()
    {
        $id = (int)\Yii::$app->request->get('id');
        #model
        $params['model']  = (!empty($id) && $this->_service->getbyprimarykey($id)) ? $this->_service->getbyprimarykey($id) : $this->_model;
       
        if(!empty($id) && $this->_service->getbyprimarykey($id)){
            #tag content
            $isNew = false;
            $params['model']->tag        = \Yii::$service->cms->tags->gettagsbyarticle($params['model']->id,true);
            $params['model']->content    = $params['model']->articleContent->content;
            $params['model']->tag        = implode(',', ArrayHelper::getColumn($params['model']->articleTags, 'name'));
            //缩略图转BASE64前端回显
            if(!empty($params['model']->thumb))$params['base64_thumb'] = \Yii::$service->helper->base64img->create($params['model']->thumb);
        }
        if(\Yii::$app->getRequest()->isPost)
        {
            // 接收参数判断
            $param = Yii::$app->request->post('Article');
            $result = $this->_service->save($param,'default');
            if($result){
                //记录日志 TYPE_CREATE TYPE_UPDATE TYPE_DELETE TYPE_OTHER TYPE_UPLOAD
                $logs = \Yii::$service->admin->logs;
                $logs->save($isNew ? $logs::TYPE_CREATE : $logs::TYPE_UPDATE, $param, $this->_primaryKey . '=' . $result[$this->_primaryKey]);
                Yii::$service->url->redirect(['cms/main/view','id'=>$result->id]);
                return ;
            }else{
                $this->_model->load($param,'');
                $errors =  Yii::$service->helper->errors->get();
                //设置错误提示信息
                Yii::$service->page->message->adderror($errors[0]);
            } 
        }
        #状态
        $params['status']  = [0=>'草稿',1=>'发布'];
        #评论
        $params['commit'] = [0=>'否',1=>'是'];
        #可见
        $params['visable'] = [1=>'公开',2=>'评论可见',3=>'加密文章',4=>'登录可见'];
        #查询父级分类信息
        $params['parents'] = \Yii::$service->cms->category->getAll();
        #处理显示select
        $params['options'] = \Yii::$service->helper->tree->setParam(['data' => $params['parents'], 'parentIdName' => 'pid'])->getTreeArraySpec(0,'name');
        
        $params['parents'] = ArrayHelper::map($params['parents'], 'id', 'name');
        return $params;
    }
    
}
