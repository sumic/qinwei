<?php
/**
 * =======================================================
 * @Description :Helper services.
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月17日
 * @version: v1.0.0
 */
namespace core\services;

use Yii;

class Helper extends Service
{
    protected $_app_name;
    protected $_param;
    /**
     * 得到当前的app入口的名字，譬如 appfront apphtml5  appserver等.
     */
    public function getAppName()
    {
        return   Yii::$app->params['appName'];
    }

    /**
     * @property $var | String Or Array 需要进行Html::encode()操作的变量。
     * @return $var | String Or Array 去除xss攻击字符后的变量
     */
    public function htmlEncode($var)
    {
        if (is_array($var) && !empty($var)) {
            foreach ($var as $k=>$v) {
                if (is_array($v) && !empty($v)) {
                    $var[$k] = $this->htmlEncode($v);
                } elseif (empty($v)) {
                    $var[$k] = $v;
                } else {
                    if (is_string($var)) {
                        $var[$k] = \yii\helpers\Html::encode($v);
                    }
                }
            }
        } elseif (empty($var)) {
        } else {
            if (is_string($var)) {
                $var = \yii\helpers\Html::encode($var);
            }
        }

        return $var;
    }
    
    /**
     * @property $domain | String vue类型的appserver传递的domain
     * 这个是appservice发送邮件，在邮件里面的url链接地址，在这里保存
     */
    public function setAppServiceDomain($domain){
        $this->_param['appServiceDomain'] = $domain; 
        return true;
    }
    
    public function getAppServiceDomain(){
        return isset($this->_param['appServiceDomain']) ? $this->_param['appServiceDomain'] : false;
    }
}
