<?php

/**
 * =======================================================
 * @Description :Voice main block
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2019年11月12日
 * @version: v1.0.0
 */

namespace appadmin\modules\Voice\block\main;

use Yii;
use appadmin\modules\AppadminBlock;
use appadmin\interfaces\AppadminBlockInterface;
use yii\helpers\ArrayHelper;

class Index extends AppadminBlock implements AppadminBlockInterface
{

    /**
     * @var string 定义上传文件的目录
     */
    public $uploadPath = '@uploads';

    public $uploader;

    public function init()
    {
        parent::init();
        $this->uploader = \Yii::$service->helper->uploader;
        //上传路径
        $this->uploader->uploadPath = \Yii::getAlias($this->uploadPath);
        //是不是超管
        $userole = \Yii::$app->user->identity->role;
        if($userole != 'admin'){
            $this->_param['filters']['created_id'] = \Yii::$app->user->identity->id;
        }
    }

    public function setModel()
    {
        $this->_modelName = Yii::$service->voice->playback->getModelName();
    }

    public function setSearchFields()
    {
        $this->_searchFields = $this->searchFields();
    }

    public function setService()
    {
        $this->_service = Yii::$service->voice->playback;
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
                'name' => 'status',
                'columns_type' => 'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'created_id',
                'columns_type' => 'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'cid',
                'columns_type' => 'int'
            ],
            [
                'type' => 'textInput',
                'name' => 'has_sensitive',
                'columns_type' => 'int'
            ],
        ];
    }
    public function getLastData()
    {
        #初始化过滤器
        $filler = $this->initFiller();
        #用户列表
        $params['adminUsers']  = ArrayHelper::map(\Yii::$service->admin->user->getall(), 'id', 'username');
        #查询父级分类信息
        $params['parents'] = \Yii::$service->cms->category->getAll();
        #处理显示select
        $params['options'] = \Yii::$service->helper->tree->setParam(['data' => $params['parents'], 'parentIdName' => 'pid'])->getTree(5, '<option value="{id}" data-pid="{pid}"> {extend_space}{name} </option>');
        $params['parents'] = ArrayHelper::map($params['parents'], 'id', 'name');
        #可用按钮
        $params['buttons']  = $this->_tableButton;
        #文件状态
        $params['status'] = [-1 => '未上传', 1 => '已上传', 9 => '已完成', 0 => '队列中',2=>'处理中'];
        #敏感词
        $params['sensitive'] = [0 => '正常', 1 => '高危'];

        //  var_dump($filler);exit;
        #return data
        $result = \Yii::$service->search->getColl($filler, $this->_model);
        $data['tables'] = $this->_display->handleResponse($result['coll'], $result['total'], $params);
        $data['params'] = $params;
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
        $result  = \Yii::$service->helper->uploader->up($strField, $strType);
        if ($result['state'] != 'SUCCESS') {
            Yii::$app->response->statusCode = 400;
            Yii::$app->response->data = \Yii::$service->helper->json->error(201, $result['message'], $result);
        } else {
            Yii::$app->response->data = \Yii::$service->helper->json->success($result, $result['message']);
            //上传文件成功写入voice表

            //记录日志 TYPE_CREATE TYPE_UPDATE TYPE_DELETE TYPE_OTHER TYPE_UPLOAD
            $logs = \Yii::$service->admin->logs;
            $logs->save($logs::TYPE_UPLOAD, $result, $strField);
        }
    }

    public function doUpdate($scenarios = 'update')
    {
        // 接收参数判断
        $param = Yii::$app->request->post('Playback');
        $this->_param = $param;
        $result = $this->_service->updateChecked($param['id'], $param['is_checked']);

        $errors = Yii::$service->helper->errors->get();
         if (!$errors) {
             //记录日志 TYPE_CREATE TYPE_UPDATE TYPE_DELETE TYPE_OTHER TYPE_UPLOAD
             $logs = \Yii::$service->admin->logs;
             $logs->save($scenarios == 'create' ? $logs::TYPE_CREATE : $logs::TYPE_UPDATE, $param, $this->_primaryKey . '=' . $result[$this->_primaryKey]);
             Yii::$service->url->redirect(['voice/main/view','id'=>$result->id]);
                return ;
            }else{
                $this->_model->load($param,'');
                $errors =  Yii::$service->helper->errors->get();
                //设置错误提示信息
                Yii::$service->page->message->adderror($errors[0]);
            } 
    }
    public function doTranslate()
    {
        $id = Yii::$app->request->get('id');
        $xfyunApi = \Yii::$service->voice->xfyun;
        if (!$id) {
            return \Yii::$service->helper->json->error(201, 'ID不存在');
        }
        //获取录音基础信息
        $playBack = $this->_service->getByPrimaryKey($id);
        if ($playBack) {
            switch ($playBack->status) {
                    //-1 未上传过
                case  -1:
                    //预处理
                    $prepare = $xfyunApi->prepare($playBack->fid);
                    //更新taskid
                    if ($prepare  && $prepare['ok'] == 0) {
                        $result = $this->_service->updateTask($id, $prepare);
                    }
                    //上传文件
                    if ($result) {
                        $upload =  $xfyunApi->upload($result);
                        if ($upload && $upload['ok'] == 0) {
                            //上传成功，更新状态
                            $result = $this->_service->updateStatus($id, '1');
                            //合并文件
                            $merge = $xfyunApi->megreFile($result->taskid);
                            if ($merge && $merge['ok'] == 0) {
                                //合并成功，更新状态
                                $result = $this->_service->updateStatus($id, '2');
                                //获取处理进度
                                $process = $xfyunApi->porcessFile($result->taskid);
                                if ($process && $process['ok'] == 0) {
                                    if (json_decode($process['data'])->status == 9) {
                                        //处理成功,更新状态9 
                                        $result = $this->_service->updateStatus($id, '9');
                                        //获取转换内容并保存
                                        $content = $xfyunApi->getresult($result->taskid);
                                        if ($content && $content['ok'] == 0) {
                                            //保存转换内容数据
                                            $playBack->content = $result['data'];
                                            $playBack->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
                    // 0 预处理完成，上传文件
                case 0:
                    $upload =  $xfyunApi->upload($playBack);
                    if ($upload && $upload['ok'] == 0) {
                        //上传成功，更新状态
                        $result = $this->_service->updateStatus($id, '1');
                        //合并文件
                        $merge = $xfyunApi->megreFile($playBack->taskid);
                        if ($merge && $merge['ok'] == 0) {
                            //合并成功，更新状态
                            $result = $this->_service->updateStatus($id, '2');
                            //获取处理进度
                            $process = $xfyunApi->porcessFile($playBack->taskid);
                            if ($process && $process['ok'] == 0) {
                                if (json_decode($process['data'])->status == 9) {
                                    //处理成功,更新状态9 
                                    $result = $this->_service->updateStatus($id, '9');
                                    //获取转换内容并保存
                                    $content = $xfyunApi->getresult($playBack->taskid);
                                    if ($content && $content['ok'] == 0) {
                                        //保存转换内容数据
                                        $playBack->content = $result['data'];
                                        $playBack->save();
                                    }
                                }
                            }
                        }
                    }
                    break;
                case 1:
                    //合并文件
                    $merge = $xfyunApi->megreFile($playBack->taskid);
                    if ($merge && $merge['ok'] == 0) {
                        //合并成功，更新状态 2
                        $result = $this->_service->updateStatus($id, '2');
                        //获取处理进度
                        $process = $xfyunApi->porcessFile($playBack->taskid);
                        if ($process && $process['ok'] == 0) {
                            if (json_decode($process['data'])->status == 9) {
                                //处理成功,更新状态9 
                                $result = $this->_service->updateStatus($id, '9');
                                //获取转换内容并保存
                                $content = $xfyunApi->getresult($playBack->taskid);
                                if ($content && $content['ok'] == 0) {
                                    //保存转换内容数据
                                    $playBack->content = $result['data'];
                                    $playBack->save();
                                }
                            }
                        }
                    }
                    break;
                case 2:
                    //获取处理进度
                    $process = $xfyunApi->porcessFile($playBack->taskid);
                    if ($process && $process['ok'] == 0) {
                        if (json_decode($process['data'])->status == 9) {
                            //处理成功,更新状态9 
                            $result = $this->_service->updateStatus($id, '9');
                            //获取转换内容并保存
                            $content = $xfyunApi->getresult($playBack->taskid);
                            if ($content && $content['ok'] == 0) {
                                //保存转换内容数据
                                $playBack->content = $content['data'];
                                $playBack->save();
                            }
                        }
                    }
                    break;
                case 9:
                    if (empty($playBack->content)) {
                        $result = $xfyunApi->getresult($playBack->taskid);
                        if ($result && $result['ok'] == 0) {
                            //保存转换内容数据
                            $playBack->content = $result['data'];
                            $playBack->save();
                        }
                    }
                    break;
            }
        }
    }
}
