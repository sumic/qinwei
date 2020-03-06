<?php
/**
 * =======================================================
 * @Description :cms main block
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
namespace appadmin\modules\Cms\block\main;

use Yii;
use appadmin\modules\AppadminBlock;
use appadmin\interfaces\AppadminBlockInterface;
use yii\helpers\ArrayHelper;

class Index extends AppadminBlock implements AppadminBlockInterface{
    
    public function init()
    {
        parent::init();
    }
    
    public function setModel()
    {
        $this->_modelName = Yii::$service->cms->article->getModelName();
    }
    
    public function setSearchFields()
    {
        $this->_searchFields = $this->searchFields();
    }
    
    public function setService()
    {
        $this->_service = Yii::$service->cms->article;
    }
    
    public function setDisplay()
    {
        $this->_display = Yii::$service->datatables;
    }
    /**
     * 定义搜索部分字段格式
     * 文本框:textInput();
     * 密码框:passwordInput();
     * 单选框:radio(),radioList();
     * 复选框:checkbox(),checkboxList();
     * 下拉框:dropDownList();
     * 隐藏域:hiddenInput();
     * 文本域:textarea(['rows'=>3]);
     * 文件上传:fileInput();
     * 提交按钮:submitButton();
     * 重置按钮:resetButtun();
     * @return array[]
     
     */
    public function searchFields()
    {
        return [
            [
                'type' => 'textInput',
                'name' => 'cid',
                'columns_type' =>'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'title',
                'columns_type' =>'string'
            ],
            [
                'type' => 'textInput',
                'name' => 'status',
                'columns_type' =>'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'flag_headline',
                'columns_type' =>'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'flag_recommend',
                'columns_type' =>'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'flag_slide_show',
                'columns_type' =>'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'flag_special_recommend',
                'columns_type' =>'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'flag_roll',
                'columns_type' =>'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'flag_bold',
                'columns_type' =>'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'flag_picture',
                'columns_type' =>'int'
            ],
        ];
    }
    public function getLastData()
    {
        #用户列表
        $params['users']  = ArrayHelper::map(\Yii::$service->admin->user->getActiveuser(), 'id', 'username');
        #状态
        $params['status']  = [1=>'发布',0=>'草稿'];
        $params['status2'] = [1=>'是',0=>'否'];
        #查询父级分类信息
        $params['parents'] = \Yii::$service->cms->category->getAll();
        #处理显示select
        $params['options'] = \Yii::$service->helper->tree->setParam(['data' => $params['parents'], 'parentIdName' => 'pid'])->getTree(0, '<option value="{id}" data-pid="{pid}"> {extend_space}{name} </option>');
        $params['parents'] = ArrayHelper::map($params['parents'], 'id', 'name');
        #可用按钮
        $params['buttons']  = $this->_tableButton;
        #初始化过滤器
        $filler = $this->initFiller();
        #return data
        $result = \Yii::$service->search->getColl($filler,$this->_model);
        $data['tables'] = $this->_display->handleResponse($result['coll'],$result['total'],$params);
        $data['params'] = $params;
        return $data;
    }
}
