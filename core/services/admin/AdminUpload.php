<?php
/**
 * =======================================================
 * @Description :admin log service
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
namespace core\services\admin;

use core\services\Service;
use yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class AdminUpload extends Service
{
    
    protected $_modelName = '\core\models\mysqldb\admin\AdminUpload';
    protected $_model;
    
    public function init()
    {
        list($this->_modelName,$this->_model) = \Yii::mapGet($this->_modelName);
    }
    
    public function actionGetModel()
    {
        return $this->_model;
    }
    
    public function up($strField,$path)
    {
        // 判断删除之前的文件
        $strFile = (string)\Yii::$app->request->post($strField);   
        // 旧的地址
        if (!empty($strFile) && file_exists('.' . $strFile)) unlink('.' . $strFile);
        // 删除之前的缩略图
        if ($strFile) {
            $strFace = dirname($strFile) . '/thumb_' . basename($strFile);
            if (file_exists('.' . $strFace)) @unlink('.' . $strFace);
        }
        // 初始化上次表单model对象，并定义好验证场景
        $model = $this->_model;
        $model->scenario = $strField;
        try {
            // 上传文件
            $objFile = $model->$strField = UploadedFile::getInstance($model, $strField);
            if (empty($objFile)) {
                throw new \UnexpectedValueException('没有文件上传');
            }
            
            // 验证
            if (!$model->validate()) {
                throw new \UnexpectedValueException($model->getFirstError($strField));
            }
            
            // 定义好保存文件目录，目录不存在那么创建
            $dirName = $path;
            FileHelper::createDirectory($dirName);
            if (!file_exists($dirName)) {
                throw new \UnexpectedValueException('目录创建失败:' . $dirName);
            }
            
            // 生成文件随机名
            $strFilePath = $dirName . uniqid() . '.' . $objFile->extension;
            // 执行文件上传保存，并且处理自己定义上传之后的处理
            if ($objFile->saveAs($strFilePath)) {
                $mixReturn = [
                    'sFilePath' => trim($strFilePath, '.'),
                    'sFileName' => $objFile->baseName . '.' . $objFile->extension,
                    'strFilePath' => $strFilePath,
                    'objFile' => $objFile,
                ];
                return $mixReturn;
            } else {
                Yii::$service->helper->errors->add('上传文件转移失败');
            }
        } catch (\Exception $e) {
            Yii::$service->helper->errors->add($e->getMessage(),'上传失败');
            return false;
        }
    }
}
