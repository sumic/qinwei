<?php
/**
 * =======================================================
 * @Description :menu main block
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
namespace appadmin\modules\Menu\block\main;

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
        $this->_modelName = 'AdminMenu';
    }
    
    public function setSearchFields()
    {
        $this->_searchFields = $this->searchFields();
    }
    
    public function setService()
    {
        $this->_service = Yii::$service->admin->menu;
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
                'name' => 'id',
                'columns_type' =>'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'pid',
                'columns_type' =>'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'status',
                'columns_type' =>'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'menu_name',
                'columns_type' =>'string'
            ],
            [
                'type' => 'textInput',
                'name' => 'url',
                'columns_type' =>'string'
            ],
        ];
    }
    public function getLastData()
    {
        #用户列表
        $params['users']  = ArrayHelper::map(\Yii::$service->admin->user->getActiveuser(), 'id', 'username');
        #状态
        $params['status'] = [1=>'启用',2=>'停用'];
        #查询父级分类信息
        $params['parents'] = \Yii::$service->admin->menu->getAll();
        #处理显示select
        $params['options'] = \Yii::$service->helper->tree->setParam(['data' => $params['parents'], 'parentIdName' => 'pid'])->getTree(0, '<option value="{id}" data-pid="{pid}"> {extend_space}{menu_name} </option>');
        $params['parents'] = ArrayHelper::map($params['parents'], 'id', 'menu_name');
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
