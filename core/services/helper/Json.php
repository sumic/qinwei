<?php
/**
 * =======================================================
 * @Description :Helper Json services.
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月18日
 * @version: v1.0.0
 */

namespace core\services\helper;

use core\services\Service;
use Yii;
use \yii\web\Response;

class Json extends Service
{
    /**
     * 定义返回json的数据
     * @var array
     */
    public $arrJson = [
        'code' => 201,
        'msg'  => '',
        'data'    => [],
    ];
    
    /**
     * 响应ajax 返回
     * @param string $array    其他返回参数(默认null)
     * @return mixed|string
     */
    protected function actionReturnJson($array = null)
    {
        // 判断是否覆盖之前的值
        if ($array) $this->arrJson = array_merge($this->arrJson, $array);
        
        // 没有错误信息使用code 确定错误信息
        if (empty($this->arrJson['msg'])) {
            $errCode = Yii::t('error', 'error_code');
            $this->arrJson['msg'] = $errCode[$this->arrJson['code']];
        }
        
        // 设置JSON返回
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->arrJson;
    }
    
    /**
     * handleJson() 处理返回数据
     * @param mixed $data     返回数据
     * @param integer   $errCode  返回状态码
     * @param string  $errMsg   提示信息
     */
    protected function actionHandleJson($data, $code = 0, $msg = '')
    {
        $this->arrJson['code'] = $code;
        $this->arrJson['data']    = $data;
        $this->arrJson['msg'] = $msg;
    }
    
    /**
     * 处理成功返回
     *
     * @param mixed $data 返回结果信息
     * @param string $message
     * @return mixed|string
     */
    protected function actionSuccess($data = [], $msg = '操作成功')
    {
        $code = 0;
        return $this->returnJson(compact('code', 'msg', 'data'));
    }
    
    /**
     * 处理错误返回
     *
     * @param integer $code 错误码
     * @param string $message
     * @return mixed|string
     */
    protected function actionError($code = 201, $msg = '',$data=[])
    {
        return $this->returnJson(compact('code', 'msg', 'data'));

    }
    
    /**
     * 设置错误码
     *
     * @param int $errCode
     */
    public function setCode($code = 201)
    {
        $this->arrJson['code'] = $code;
    }
    
    /**
     * 设置错误信息
     *
     * @param string $message
     */
    public function setMessage($msg = '')
    {
        $this->arrJson['msg'] = $msg;
    }
}
