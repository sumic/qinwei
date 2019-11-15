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
namespace appadmin\modules\RoleItem\block\main;

use Yii;
use appadmin\modules\AppadminBlock;
use appadmin\interfaces\AppadminBlockInterface;

class Index extends AppadminBlock implements AppadminBlockInterface{
    
    public function init()
    {
        parent::init();
    }
    
    public function setModel()
    {
        $this->_modelName = 'AdminRoleItem';
    }
    
    public function setSearchFields()
    {
        $this->_searchFields = $this->searchFields();
    }
    
    public function setService()
    {
        $this->_service = Yii::$service->admin->role;
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
                'name' => 'name',
                'columns_type' =>'string'
            ],
            [
                'type' => 'textInput',
                'name' => 'description',
                'columns_type' =>'string'
            ],
            [
                'type' => 'textInput',
                'name' => 'type',
                'columns_type' =>'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'rule_name',
                'columns_type' =>'string'
            ],
        ];
    }
    public function getLastData()
    {
        #初始化过滤器
        $params['type'] = $this->_param['filers']['type'] = $this->_model::TYPE_PERMISSION;
        $filler = $this->initFiller();
        #查出所有规则
        $rules = \Yii::$service->admin->rule->getAll();
        #封装规则数据
        $params['rules'] = ['' => '请选择'];
        if ($rules) {
            foreach ($rules as $value) {
                if ($value['data']) {
                    $tmp = unserialize($value['data']);
                    if ($tmp) {
                        $value['data'] = get_class($tmp);
                    }
                }
                
                $params['rules'][$value['name']] = $value['name'] . ' - ' . $value['data'];
            }
        }
        #可用按钮
        $params['buttons']  = $this->_tableButton;
        #返回数据
        $result = \Yii::$service->search->getColl($filler,$this->_model);
        $data['tables'] = $this->_display->handleResponse($result['coll'],$result['total'],$params);
        $data['params'] = $params;
        return $data;
    }
}
