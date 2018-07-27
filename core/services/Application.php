<?php
/**
 * =======================================================
 * @Description :service服务注册类
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */

namespace core\services;

use Yii;
use yii\base\InvalidConfigException;

/**
 * 此对象就是Yii::$service,通过魔术方法__get ， 得到服务对象，服务对象是单例模式。
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Application
{
    public $childService;
    public $_childService;
    
    /**
     * @property $config | Array 注入的配置数组
     * 在@app/web/index.php 入口文件处。会调用 new core\services\Application($config['services']); 
     * Yii::$service 就是该类实例化的对象，注入的配置保存到 $this->childService 中
     */
    public function __construct($config = [])
    {
        Yii::$service = $this;
        $this->childService = $config;
    }

    /**
     * 得到services 里面配置的子服务childService的实例.
     * 单例模式，懒加载，使用的时候才会被实例化。类似于Yii2的component原理。
     */
    public function getChildService($childServiceName)
    {
        
        if (!$this->_childService[$childServiceName]) {
            $childService = $this->childService;
            if (isset($childService[$childServiceName])) {
                $service = $childService[$childServiceName];
                if(!isset($service['enableService']) || $service['enableService']){
                    $this->_childService[$childServiceName] = Yii::createObject($service);
                }else{
                    throw new InvalidConfigException('Child Service ['.$childServiceName.'] is disable in '.get_called_class().', you must config it! ');
                }
            } else {
                throw new InvalidConfigException('Child Service ['.$childServiceName.'] is not find in '.get_called_class().', you must config it! ');
            }
        }

        return $this->_childService[$childServiceName];
    }
    /**
     * @property $attr | String ， service的name。
     * 魔术方法，当调用一个属性，对象不存在的时候就会执行该方法，然后
     * 根据构造方法注入的配置，实例化service对象。
     */
    public function __get($attr)
    {
        return $this->getChildService($attr);
    }
}
