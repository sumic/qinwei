<?php
/**
 * =======================================================
 * @Description :role main block
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
namespace appadmin\modules\Admin\block\main;

use Yii;
use appadmin\modules\AppadminBlock;
use appadmin\interfaces\AppadminBlockInterface;
use yii\helpers\ArrayHelper;
use yii\image\drivers\Image;

class Index extends AppadminBlock implements AppadminBlockInterface{
    
    #上传路径
    public $uploadPath = '@uploads/avatars';
    
    /**
     * 缩略图设置
     * 默认不开启
     * ['height' => 200, 'width' => 200]表示生成200*200的缩略图，如果设置为空数组则不生成缩略图
     * @var array
     */
    public $thumbnail = ['height' => 180, 'width' => 180];
    
    /**
     * 图片缩放设置
     * 默认不缩放。
     * 配置如 ['height'=>200,'width'=>200]
     *
     * @var array
     */
    public $zoom = ['height'=>48,'width'=>48];
    
    
    public function init(){
        parent::init();
        #设置上传路径
        $this->_uploader = yii::$service->helper->uploader;
        $this->_uploader->uploadPath = \Yii::getAlias($this->uploadPath);
        //缩略图
        $this->_uploader->thumbnail = $this->thumbnail;
        //缩放
        $this->_uploader->zoom = $this->zoom;
    }
    
    public function setModel()
    {
        $this->_modelName = 'AdminUser';
    }
    
    public function setSearchFields()
    {
        $this->_searchFields = $this->searchFields();
    }
    
    public function setService()
    {
        $this->_service = Yii::$service->admin->user;
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
        $data['tables'] = $this->_display->handleResponse($result['coll'],$result['total'],$params);
        $data['params'] = $params;
        return $data;
    }
    
    public function afterUpload($objFile, &$strFilePath, $strField)
    {
        // 上传头像信息
        if ($strField === 'avatar' || $strField === 'face') {
            // 删除之前的缩略图
            $strFace = Yii::$app->request->post('face');
            if ($strFace) {
                $strFace = dirname($strFace) . '/thumb_' . basename($strFace);
                if (file_exists('.' . $strFace)) @unlink('.' . $strFace);
            }
            
            // 处理图片
            $strTmpPath = dirname($strFilePath) . '/thumb_' . basename($strFilePath);
            
            /* @var $image yii\image\ImageDriver */
            $imageComponent = Yii::$app->get('image');
            if ($imageComponent) {
                /* @var $image yii\image\drivers\Kohana_Image_GD */
                $image = $imageComponent->load($strFilePath);
                $image->resize(180, 180, Image::CROP)->save($strTmpPath);
                $image->resize(48, 48, Image::CROP)->save();
                
                // 管理员页面修改头像
                if($strField === 'avatar'){
                    $avatar = \Yii::$service->admin->user->setUserAvatar($strFilePath);
                    if($avatar)$strFilePath = $strTmpPath;
                }
            }
        }
        return true;
    }
}
