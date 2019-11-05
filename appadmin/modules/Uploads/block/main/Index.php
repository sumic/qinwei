<?php
/**
 * =======================================================
 * @Description :uploads main block
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
namespace appadmin\modules\Uploads\block\main;

use Yii;
use appadmin\modules\AppadminBlock;
use appadmin\interfaces\AppadminBlockInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Index extends AppadminBlock implements AppadminBlockInterface{
    
    /**
     * @var string 定义上传文件的目录
     */
    public $uploadPath = '@uploads';
    /**
     * 缩略图设置
     * 默认不开启
     * ['height' => 200, 'width' => 200]表示生成200*200的缩略图，如果设置为空数组则不生成缩略图
     * @var array
     */
    public $thumbnail = ['height' => 200, 'width' => 200];
    
    /**
     * 图片缩放设置
     * 默认不缩放。
     * 配置如 ['height'=>200,'width'=>200]
     *
     * @var array
     */
    public $zoom = ['height'=>200,'width'=>200];
    
    /**
     * 水印设置
     * 参考配置如下：
     * ['path'=>'水印图片位置','position'=>0]
     * 默认位置为 9，可不配置
     * position in [1 ,9]，表示从左上到右下的9个位置。
     *
     * @var array
     */
    public $watermark = ['path'=>'@uploads/water.gif','position'=>0];
    /**
     * @var string 上传model
     */
    public $uploader;
    
    public function init()
    {
        parent::init();
        $this->uploader = \Yii::$service->helper->uploader;
        //上传路径
        $this->uploader->uploadPath = \Yii::getAlias($this->uploadPath);
        //缩略图
        //$this->uploader->thumbnail = $this->thumbnail;
        //缩放
        //$this->uploader->zoom = $this->zoom;
        //水印
        //$this->uploader->watermark = $this->watermark;
        
    }
    
    public function setModel()
    {
        $this->_modelName = Yii::$service->helper->uploader->getModelName();
    }
    
    public function setSearchFields()
    {
        $this->_searchFields = $this->searchFields();
    }
    
    public function setService()
    {
        $this->_service = Yii::$service->helper->uploader;
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
                'name' => 'username',
                'columns_type' =>'string'
            ],
            [
                'type' => 'textInput',
                'name' => 'email',
                'columns_type' =>'string'
            ],
            [
                'type' => 'textInput',
                'name' => 'id',
                'columns_type' =>'int'
            ],
        ];
    }
    public function getLastData()
    {
        #初始化过滤器
        $filler = $this->initFiller();
        #用户列表
        $params['adminUsers']  = ArrayHelper::map(\Yii::$service->admin->user->getall(), 'id', 'username');
        #用户状态
        $params['status'] = \Yii::$service->admin->user->getarraystatus();
        #用户状态颜色
        $params['statusColor'] = \Yii::$service->admin->user->getstatuscolor();
        #可用角色
        $params['roles'] = \Yii::$service->admin->role->getallrolearray();
        #可用按钮
        $params['buttons']  = $this->_tableButton;
        #return data
        $result = \Yii::$service->search->getColl($filler,$this->_model);
        $data ['tables']= $this->_display->handleResponse($result['coll'],$result['total'],$params);
        $data ['params'] = $params;
        return $data;
    }
    
    public function doUpload()
    {
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
