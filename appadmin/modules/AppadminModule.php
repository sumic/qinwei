<?php
/**
 * =======================================================
 * @Description :admin模型基类
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月2日
 * @version: v1.0.0
 *
 */
namespace appadmin\modules;
use Yii;
class AppadminModule extends \yii\base\Module
{
    public $controllerNamespace ;
    public $_currentDir ;
    public $_currentNameSpace ;
    
    public function init()
    {
        parent::init();
        $this->configModuleParams();
        # 默认layout文件
        $this->layout = $this->layout ? $this->layout : "main.php";
        $this->defaultRoute = 'main';
    }
    
    public function configModuleParams(){
        # 配置config文件
        $config_file_dir = $this->_currentDir . '/etc/config.php';
        if(file_exists($config_file_dir)){
            $params_data = (require($config_file_dir));
            
        }
        # 设置参数
        $params_data['_currentDir'] 		= $this->_currentDir;
        $params_data['_currentNameSpace'] 	= $this->_currentNameSpace;
        $params = $this->params;
        if(is_array($params) && !empty($params)){
            $params_data = \yii\helpers\ArrayHelper::merge($params,$params_data);
        }
        Yii::configure($this, ['params'=> $params_data]);
        
    }
    
}
