<?php
/**
 * =======================================================
 * @Description :servicelog 组建
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
namespace core\components;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Component;
use core\helpers\CRequest;


class ServiceLog extends Component
{
    public $log_config;
    protected $_serviceContent;
    protected $_serviceUid;
    protected $_isServiceLog;
    protected $_isServiceLogDbPrint;
    protected $_isServiceLogHtmlPrint;
    protected $_isServiceLogDbPrintByParam;
    
    protected $_logModelName = '\core\models\mongodb\ServiceLog';
    protected $_logModel;
    
    public function init(){
	parent::init();
        list($this->_logModelName,$this->_logModel) = Yii::mapGet($this->_logModelName);  
    }
    /**
     * Log��get log uuid .
     */
    public function getLogUid()
    {
        if (!$this->_serviceUid) {
            $this->_serviceUid = $this->guid();
        }

        return $this->_serviceUid;
    }

    /**
     * ServiceLog���Ƿ���service log.
     */
    public function isServiceLogEnable()
    {
        if ($this->_isServiceLog === null) {
            if (
                isset($this->log_config['services']['enable'])
            && $this->log_config['services']['enable']
            ) {
                $this->_isServiceLog = true;
            } else {
                $this->_isServiceLog = false;
            }
        }

        return $this->_isServiceLog;
    }

    /**
     * ServiceLog������serviceLog.
     */
    public function printServiceLog($log_info)
    {
        if ($this->isServiceLogDbPrint()) {
            $this->_logModel->getCollection()->save($log_info);
        }
        if ($this->isServiceLogHtmlPrint() || $this->isServiceLogDbPrintByParam()) {
            $str = '<br>#################################<br><table>';
            foreach ($log_info as $k=>$v) {
                if (is_array($v)) {
                    $v = implode('<br>', $v);
                    $str .= "<tr>
					<td>$k</td><td>$v</td>
					</tr>";
                } else {
                    $str .= "<tr>
					<td>$k</td><td>$v</td>
					</tr>";
                }
            }
            $str .= '</table><br>#################################<br><br>';
            echo $str;
        }
    }

    /**
     * ServiceLog��if service log db print is enable.
     */
    protected function isServiceLogDbPrint()
    {
        if ($this->_isServiceLogDbPrint === null) {
            if (
                isset($this->log_config['services']['enable'])
            && $this->log_config['services']['enable']
            && isset($this->log_config['services']['dbprint'])
            && $this->log_config['services']['dbprint']
            ) {
                $this->_isServiceLogDbPrint = true;
            } else {
                $this->_isServiceLogDbPrint = false;
            }
        }

        return $this->_isServiceLogDbPrint;
    }

    /**
     * ServiceLog����ǰ̨��ӡservicelog�Ƿ���.
     */
    protected function isServiceLogHtmlPrint()
    {
        if ($this->_isServiceLogHtmlPrint === null) {
            if (
                isset($this->log_config['services']['enable'])
            && $this->log_config['services']['enable']
            && isset($this->log_config['services']['htmlprint'])
            && $this->log_config['services']['htmlprint']
            ) {
                $this->_isServiceLogHtmlPrint = true;
            } else {
                $this->_isServiceLogHtmlPrint = false;
            }
        }

        return $this->_isServiceLogHtmlPrint;
    }

    /**
     * ServiceLog��ͨ����������ǰ̨��ӡservicelog�Ƿ���.
     */
    protected function isServiceLogDbPrintByParam()
    {
        if ($this->_isServiceLogDbPrintByParam === null) {
            $this->_isServiceLogDbPrintByParam = false;
            if (
                isset($this->log_config['services']['enable'])
            && $this->log_config['services']['enable']
            && isset($this->log_config['services']['htmlprintbyparam']['enable'])
            && $this->log_config['services']['htmlprintbyparam']['enable']
            && isset($this->log_config['services']['htmlprintbyparam']['paramVal'])
            && ($paramVal = $this->log_config['services']['htmlprintbyparam']['paramVal'])
            && isset($this->log_config['services']['htmlprintbyparam']['paramKey'])
            && ($paramKey = $this->log_config['services']['htmlprintbyparam']['paramKey'])

            ) {
                if (CRequest::param($paramKey) == $paramVal) {
                    $this->_isServiceLogDbPrintByParam = true;
                }
            }
        }

        return $this->_isServiceLogDbPrintByParam;
    }

    /**
     * generate  uuid .
     */
    protected function guid()
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((float) microtime() * 10000); //optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $uuid = //chr(123)// "{"
                 substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid, 12, 4).$hyphen
                .substr($charid, 16, 4).$hyphen
                .substr($charid, 20, 12)
                //.chr(125)// "}"
                ;
            return $uuid;
        }
    }
}
