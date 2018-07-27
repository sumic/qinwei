<?php
/**
 * =======================================================
 * @Description :role main block
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
namespace appadmin\modules\Admin\block\role;

use Yii;
use appadmin\modules\AppadminBlock;
use appadmin\interfaces\AppadminBlockInterface;

class Main extends AppadminBlock implements AppadminBlockInterface{
    
    public $modelName = 'AdminRoleItem';
    
    public function init()
    {
        $this->_modelName = $this->modelName;
        parent::init();
    }
    
    public function setService()
    {
        $this->_service = Yii::$service->admin->role;
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
                'name' => 'route',
                'columns_type' =>'string'
            ],
            [
                'type' => 'textInput',
                'name' => 'description',
                'columns_type' =>'string'
            ],
        ];
    }
    public function getLastData()
    {
        $search = $this->initWhere($this->searchFields());
        var_dump($search);exit;
    }
}