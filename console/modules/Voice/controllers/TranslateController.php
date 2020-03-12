<?php

namespace console\modules\Voice\controllers;

use Yii;
use yii\console\Controller;

/**
 * @author Sumic 2020年03月09日
 * @since 1.0
 */

class TranslateController extends Controller
{
    public $_service ;
    /**
     * 自动上传录音文件，并检查转码状态 
     * */
    public function actionIndex()
    {
        $xfyunApi = \Yii::$service->voice->xfyun;
        $this->_service = Yii::$service->voice->console;
        $data = $this->_service->getAllTranslate(); 
        if($data){
            foreach ($data as $playBack){
                if ($playBack) {
                    switch ($playBack->status) {
                            //-1 未上传过
                        case  -1:
                            //预处理
                            $prepare = $xfyunApi->prepare($playBack->fid);
                            
                            //更新taskid
                            if ($prepare  && $prepare['ok'] == 0) {
                                $result = $this->_service->updateTask($playBack->id, $prepare);
                            }                           
                            //上传文件
                            if ($result) {
                                $upload =  $xfyunApi->upload($result);
                                if ($upload && $upload['ok'] == 0) {
                                    //上传成功，更新状态
                                    $result = $this->_service->updateStatus($playBack->id, '1');
                                    //合并文件
                                    $merge = $xfyunApi->megreFile($result->taskid);
                                    if ($merge && $merge['ok'] == 0) {
                                        //合并成功，更新状态
                                        $result = $this->_service->updateStatus($playBack->id, '2');
                                        //获取处理进度
                                        $process = $xfyunApi->porcessFile($result->taskid);
                                        if ($process && $process['ok'] == 0) {
                                            if (json_decode($process['data'])->status == 9) {
                                                //处理成功,更新状态9 
                                                $result = $this->_service->updateStatus($playBack->id, '9');
                                                //获取转换内容并保存
                                                $content = $xfyunApi->getresult($result->taskid);
                                                if ($content && $content['ok'] == 0) {
                                                    //保存转换内容数据
                                                    $playBack->content = $result['data'];
                                                    //是否包含敏感词
                                                    if(!empty(json_decode((json_decode($result['data']))->sensitive_result))){
                                                        $playBack->has_sensitive = 1;
                                                    }
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
                                $result = $this->_service->updateStatus($playBack->id, '1');
                                //合并文件
                                $merge = $xfyunApi->megreFile($playBack->taskid);
                                if ($merge && $merge['ok'] == 0) {
                                    //合并成功，更新状态
                                    $result = $this->_service->updateStatus($playBack->id, '2');
                                    //获取处理进度
                                    $process = $xfyunApi->porcessFile($playBack->taskid);
                                    if ($process && $process['ok'] == 0) {
                                        if (json_decode($process['data'])->status == 9) {
                                            //处理成功,更新状态9 
                                            $result = $this->_service->updateStatus($playBack->id, '9');
                                            //获取转换内容并保存
                                            $content = $xfyunApi->getresult($playBack->taskid);
                                            if ($content && $content['ok'] == 0) {
                                                //保存转换内容数据
                                                $playBack->content = $content['data'];
                                                 //是否包含敏感词
                                                 if(!empty(json_decode((json_decode($content['data']))->sensitive_result))){
                                                    $playBack->has_sensitive = 1;
                                                }
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
                                $result = $this->_service->updateStatus($playBack->id, '2');
                                //获取处理进度
                                $process = $xfyunApi->porcessFile($playBack->taskid);
                                if ($process && $process['ok'] == 0) {
                                    if (json_decode($process['data'])->status == 9) {
                                        //处理成功,更新状态9 
                                        $result = $this->_service->updateStatus($playBack->id, '9');
                                        //获取转换内容并保存
                                        $content = $xfyunApi->getresult($playBack->taskid);
                                        if ($content && $content['ok'] == 0) {
                                            //保存转换内容数据
                                            $playBack->content = $content['data'];
                                             //是否包含敏感词
                                             if(!empty(json_decode((json_decode($content['data']))->sensitive_result))){
                                                $playBack->has_sensitive = 1;
                                            }
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
                                    $result = $this->_service->updateStatus($playBack->id, '9');
                                    
                                    //获取转换内容并保存
                                    $content = $xfyunApi->getresult($playBack->taskid);

                                    if ($content && $content['ok'] == 0) {
                                        //保存转换内容数据
                                        $playBack->content = $content['data'];
                                         //是否包含敏感词
                                         if(!empty(json_decode((json_decode($content['data']))->sensitive_result))){
                                            $playBack->has_sensitive = 1;
                                        }
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
                                     //是否包含敏感词
                                     if(!empty(json_decode((json_decode($result['data']))->sensitive_result))){
                                        $playBack->has_sensitive = 1;
                                    }
                                    $playBack->save();
                                }
                            }
                            break;
                    }
                }
            }
        }
        
    }
}