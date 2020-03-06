<?php
/**
 * =======================================================
 * @Description :TODO
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
namespace core\helpers;
use Yii; 

class CModule //extends CModule
{
	# 1.得到模块内部的配置，模块的配置在模块的etc/config.php内
	public static function param($param,$moduleName=''){
		if($moduleName){
			//echo $moduleName;exit;
			return Yii::$app->getModule($moduleName)->params[$param];
		}else{
			return Yii::$app->controller->module->params[$param];
		
		}
	}
	# \core\helpers\CModule::getToken();
	# 得到模块的 验证token
	public static function getToken(){
		
		$module_token = self::param('module_token');
		
		if($module_token){
			return $module_token;
		}else{
			return  CConfig::getDefaultModuleToken();
		}
	}
}