<?php
/**
 * =======================================================
 * @Description :block 基类
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月20日
 * @version: v1.0.0
 */
namespace appadmin\modules;
use yii\base\BaseObject;
use Yii;
class AppadminBlock extends BaseObject
{
    /**
     * parameter storage front passed.
     */
    public $_param = [];
    /**
     * display service  @string datatables or jqgrid
     */
    public $_display;
    /**
     * model name
     */
    public $_modelName;
    /**
     * model
     */
    public $_model;
    /**
     * search field.
     */
    public $_searchFields;
    /**
     * service.
     */
    public $_service;
    /**
     * single data model
     */
    public $_one;
    /**
     * default offset number.
     */
    public $_offset = 0;
    /**
     * collection default number displayed.
     */
    public $_limit = 10;
    /**
     * collection primary key.
     */
    public $_primaryKey;
    
    /**
     * collection sort direction , the default value is 'desc'.
     */
    public $_orderDirection = 'desc';
    /**
     * collection sort field , the default value is primary key.
     */
    public $_orderBy;
    /**
     * search as array.
     */
    public $_asArray = true;
    /**
     * default tabledatabutton
     */
    public $_tableButton = '';
    
    /**
     * @var string 上传model
     */
    public $_uploader;
    /**
     * it will be execute during initialization ,the following object variables will be initialize.
     * $_primaryKey , $_param , $_currentUrl ,
     */
    public function init()
    {
        #设置模型
        $this->setModel();
        #设置查询字段
        $this->setSearchFields();
        #设置服务
        $this->setService();
        #设置显示类型
        $this->setDisplay();
        #设置datatables的可用按键
        $this->_tableButton = \Yii::$service->admin->role->getDataTableAuth('user');
        #获得查询的字段
        $param = $this->_display->getRequest();
        $this->_primaryKey = $this->_service->getPrimaryKey();
        //var_dump($param);exit;
        if (empty($param['offset'])) {
            $param['offset'] = $this->_offset;
        }
        if (empty($param['limit'])) {
            $param['limit'] = $this->_limit;
        }
        if (empty($param['orderBy'])) {
            $param['orderBy'] = $this->_primaryKey;
        }
        if (empty($param['orderDirection'])) {
            $param['orderDirection'] = $this->_orderDirection;
        }
        if (empty($param['asArray'])) {
            $param['asArray'] = $this->_asArray;
        }
        $this->_param = array_merge($this->_param, $param);

        $this->_model = $this->_service->getModel();
    }
    
    # 生成查询条件
    public function initWhere($searchArr){
        foreach($searchArr as $field){
            $type = $field['type'];
            $name = $field['name'];
            $columns_type = isset($field['columns_type']) ? $field['columns_type'] : '';
            if(isset($this->_param['filters'][$name])){
                if($type == 'textInput' || $type == 'select' || $type == 'chosen_select'){
                    if($columns_type == 'string'){
                       $where[] = ['like', $name, $this->_param['filters'][$name]];
                    }else if($columns_type == 'int'){
                       $where[] = [$name => (int)$this->_param['filters'][$name]];
                    }else if($columns_type == 'float'){
                       $where[] = [$name => (float)$this->_param['filters'][$name]];
                    }else if($columns_type == 'date'){
                       $where[] = [$name => $this->_param['filters'][$name]];
                    }else if($columns_type == 'inArray'){
                        $where[] = ['in',$name , $this->_param['filters'][$name]];
                    }else{
                       $where[] = [$name => $this->_param['filters'][$name]];
                    }
                }else if($type == 'inputdatefilter'){
                    $_gte 	= $this->_param[$name.'_gte'];
                    $_lt 	= $this->_param[$name.'_lt'];
                    
                    if($columns_type == 'float'){
                        $_gte 	= strtotime($_gte);
                        $_lt	= strtotime($_lt);
                    }
                    if($_gte){
                        $where[] =['>=', $name, $_gte];
                    }
                    if($_lt){
                        $where[] =['<', $name, $_lt];
                    }
                    //var_dump($query->where);
                }else if($type == 'inputfilter'){
                    $_gte 	= $this->_param[$name.'_gte'];
                    $_lt 	= $this->_param[$name.'_lt'];
                    
                    if($columns_type == 'int'){
                        $_gte 	= (int)$_gte;
                        $_lt	= (int)$_lt;
                    }else if($columns_type == 'float'){
                        $_gte 	= (float)$_gte;
                        $_lt	= (float)$_lt;
                    }
                    if($_gte){
                        $where[] =['>=', $name, $_gte];
                    }
                    if($_lt){
                        $where[] =['<', $name, $_lt];
                    }
                }else{
                    $where[]=[$name => $this->_param['params'][$name]];
                }
            }
        }
        return $where;
    }
    
    #生成查询过滤器
    public function initFiller()
    {
        return $filter = [
            'limit'          => $this->_param['limit'],
            'offset'         => $this->_param['offset'],
            'orderBy'        => $this->_param['orderBy'],
            'where'          => $this->initWhere($this->_searchFields),
            'asArray'        => $this->_asArray,
        ];
    }
    
    public function delete()
    {
        $ids = '';
        $param = Yii::$app->request->post();
        
        if ($id = $param['id']) {
            $ids = $id;
        } elseif ($ids = $param['ids']) {
            $ids = explode(',', $ids);
        }
        $result = $this->_service->remove($ids);
        $errors = Yii::$service->helper->errors->get();
        if (!$errors) {
            //记录日志 TYPE_CREATE TYPE_UPDATE TYPE_DELETE TYPE_OTHER TYPE_UPLOAD
            $logs = \Yii::$service->admin->logs;
            $logs->save($logs::TYPE_DELETE, $param, $this->_primaryKey . '=' . $result[$this->_primaryKey]);
            return \Yii::$service->helper->json->success($ids,'删除成功');
        } else {
            return \Yii::$service->helper->json->error(201, $errors[0]);
        }
    }
    
    public function update($scenarios = 'update')
    {
        // 接收参数判断
        $param = Yii::$app->request->post();
        $this->_param = $param;
        
        $result = $this->_service->save($this->_param,$scenarios); 
        $errors = Yii::$service->helper->errors->get();
         if (!$errors) {
             //记录日志 TYPE_CREATE TYPE_UPDATE TYPE_DELETE TYPE_OTHER TYPE_UPLOAD
             $logs = \Yii::$service->admin->logs;
             $logs->save($scenarios == 'create' ? $logs::TYPE_CREATE : $logs::TYPE_UPDATE, $param, $this->_primaryKey . '=' . $result[$this->_primaryKey]);
             return \Yii::$service->helper->json->success();
         } else {
             return \Yii::$service->helper->json->error(201, $errors[0]);
         }
    }
    
    public function upload()
    {
        // 接收参数
        $request = Yii::$app->request;
        $strField = $request->get('sField');    // 上传文件表单名称
        $strType = $request->get('sType');    // 上传文件验证场景
        if (empty($strField) || empty($strType)) {
            return \Yii::$service->helper->json->error(201);
        }
        $result  = \Yii::$service->helper->uploader->up($strField,$strType);
        if($result['state'] != 'SUCCESS'){
            Yii::$app->response->statusCode = 400;
            Yii::$app->response->content = \Yii::$service->helper->json->error(201,$result['message'],$result);
        }else{
            Yii::$app->response->content = \Yii::$service->helper->json->success($result,$result['message']);
            //记录日志 TYPE_CREATE TYPE_UPDATE TYPE_DELETE TYPE_OTHER TYPE_UPLOAD
            $logs = \Yii::$service->admin->logs;
            $logs->save($logs::TYPE_UPLOAD, $result, $strField);
        }
        return $result;
    }
}