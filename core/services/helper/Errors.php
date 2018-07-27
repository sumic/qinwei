<?php
/**
 * =======================================================
 * @Description :Helper Errors services.
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月18日
 * @version: v1.0.0
 */

namespace core\services\helper;

use core\services\Service;
use Yii;


class Errors extends Service
{
    protected $_errors = false;
    public $status = true;

    /**
     * @property $errros | String , 错误信息
     * @property $arr | Array 变量替换对应的数组
     * Yii::$service->helper->errors->add('Hello, {username}!', ['username' => $username])
     */
    public function add($errros, $arr = [])
    {
        if ($errros) {
            $this->_errors[] = $errros;
        }
    }
    /**
     * @property $model_errors | Array
     * Yii2的model在使用rules验证数据格式的时候，报错保存在errors中
     * 本函数将errors的内容添加到errors services中。
     */
    public function addByModelErrors($model_errors)
    {
        $error_arr = [];
        if (is_array($model_errors)) {
            foreach ($model_errors as $name => $errors) {
                $arr = [];

                foreach ($errors as $s) {
                    $arr[] = $s;
                }
                $error_arr[] = implode(',', $arr);
            }
            if (!empty($error_arr)) {
                $this->_errors[] = implode(' ', $error_arr);
            }
        }
    }
    
    public function getModelErrorsStrFormat($model_errors){
        $error_arr = [];
        if (is_array($model_errors)) {
            foreach ($model_errors as $errors) {
                $arr = [];

                foreach ($errors as $s) {
                    $arr[] = $s;
                }
                $error_arr[] = implode(',', $arr);
            }
            if (!empty($error_arr)) {
                return implode(',', $error_arr);
            }
        }
    }

    /**
     * @property $separator 如果是false，则返回数组，
     *						如果是true则返回用| 分隔的字符串
     *						如果是传递的分隔符的值，譬如“,”，则返回用这个分隔符分隔的字符串
     */
    public function get($separator = false)
    {
        if ($errors = $this->_errors) {
            $this->_errors = false;
            if(is_array($errors) && !empty($errors)){
                if ($separator) {
                    if ($separator === true) {
                        $separator = '|';
                    }

                    return implode($separator, $errors);
                } else {
                    return $errors;
                }
            }
        }

        return false;
    }
}
