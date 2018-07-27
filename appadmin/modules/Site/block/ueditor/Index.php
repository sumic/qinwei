<?php
/**
 * =======================================================
 * @Description :ueditor block
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月28日
 * @version: v1.0.0
 */
namespace appadmin\modules\Site\block\ueditor;
use yii;
use appadmin\modules\AppadminBlock;

class Index extends AppadminBlock
{
    /**
     * @var string 定义上传文件的目录
     */
    public $uploadPath = '@uploads/ueditor';
    
    public $ueditor;
    /**
     * 缩略图设置
     * 默认不开启
     * ['height' => 200, 'width' => 200]表示生成200*200的缩略图，如果设置为空数组则不生成缩略图
     * @var array
     */
    public $thumbnail = ['height' => 200, 'width' => 200];
    
    public function init()
    {
        //初始化编辑器
        $this->ueditor = \Yii::$service->page->ueditor;
        $this->ueditor->uploadPath = \Yii::getAlias($this->uploadPath);
        //上传相关参数配置
        $this->ueditor->uploader->uploadPath = \Yii::getAlias($this->uploadPath);
        //缩略图开启
        $this->ueditor->uploader->thumbnail = $this->thumbnail;
        
    }
    
    public function getLastData()
    {
        $action = strtolower(Yii::$app->request->get('action', 'config'));
        $actions = [
            'uploadimage','uploadscrawl','uploadvideo','uploadfile','listimage','listfile','catchimage','config','listinfo'
        ];
        if (in_array($action,$actions)) {
            yii::$app->getResponse()->format = yii\web\Response::FORMAT_JSON;
            $result = $this->ueditor->{$action}();
            //手动上传图片成功后写入日志，排除远程拉取数据
            if(in_array($action,['uploadimage','uploadscrawl','uploadvideo','uploadfile']) && $result['state'] == 'SUCCESS'){
                //记录日志 TYPE_CREATE TYPE_UPDATE TYPE_DELETE TYPE_OTHER TYPE_UPLOAD
                $logs = \Yii::$service->admin->logs;
                $logs->save($logs::TYPE_UPLOAD, $result, $result['fieldname']);
            }
            return $result;
        } else {
            return $this->ueditor->show(['state' => 'Unknown action.']);
        }
    }
}