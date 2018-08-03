<?php
/**
 * =======================================================
 * @Description :wechat main block
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年07月31日 14:23:21
 * @version: v1.0.0
 */
namespace appadmin\modules\Wechat\block\main;

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
        $this->_modelName = 'Base';
    }
    
    public function setSearchFields()
    {
        $this->_searchFields = $this->searchFields();
    }
    
    public function setService()
    {
        $this->_service = Yii::$service->mpwechat->base;
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
                'name' => 'mpname',
                'columns_type' =>'string'
            ],
            [
                'type' => 'textInput',
                'name' => 'mptype',
                'columns_type' =>'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'appid',
                'columns_type' =>'string'
            ],
        ];
    }
    public function getLastData()
    {
        #状态
        $params['status'] = [2=>'停用',1=>'启用'];
        #公众号类型
        $params['mptype'] = [0=>'未认证订阅号',1=>'认证订阅号',2=>'未认证服务号',3=>'认证服务号'];
        #公众号类型菜单
        foreach ($params['mptype'] as $k =>$v){
            $params['options'] .= '<option value="'.$k.'">'.$v.'</option>';
        }
        #可用按钮
        $params['buttons']  = $this->_tableButton;
        #初始化过滤器
        $filler = $this->initFiller();
        #return data
        $result = \Yii::$service->search->getColl($filler,$this->_model);
        $data = $this->_display->handleResponse($result['coll'],$result['total'],$params);
        return $data;
    }
}
