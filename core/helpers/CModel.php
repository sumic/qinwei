<?php
/**
 * =======================================================
 * @Description :TODO
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
namespace core\helpers;
use Yii; 

class CModel 
{
	# 1.将models 的错误信息转换成字符串
	public static function getErrorStr($errors){
		$str = '';
		if(is_array($errors)){
			foreach($errors as $field=>$error_k){
				$str .= $field.':'.implode(",",$error_k)." <br/>";
			}
		}
		return $str;
	}
	
}