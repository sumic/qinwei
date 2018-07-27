<?php
/**
 * =======================================================
 * @Description :重写YII注册service
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月2日
 * @version: v1.0.0
 *
 */
$dir = __DIR__ . '/../../vendor/yiisoft/yii2';
require $dir.'/BaseYii.php';

class Yii extends \yii\BaseYii
{
    public static $service;
    /**
     * rewriteMap , like:
     * [
     *    '\admin\models\mongodb\Category'  => '\appadmin\models\mongodb\Category'
     * ]
     */
    public static $rewriteMap;
    /**
     * @property $absoluteClassName | String , like: '\app\appfront\modules\Cms\block\home\Index'
     * @property $arguments | Array ,数组，里面的每一个子项就是用于实例化的一个参数，多少个子项，就代表有多个参数，用于对象的实例化。
     * 通过$rewriteMap，查找是否存在重写，如果存在，则得到重写的className
     * 然后返回 类名 和 对象
     */
    public static function mapGet($absoluteClassName,$arguments = []){
        $absoluteClassName = self::mapGetName($absoluteClassName);
        if (!empty($arguments) && is_array($arguments)) {
            $class = new ReflectionClass($absoluteClassName);
            $absoluteOb = $class->newInstanceArgs($arguments);
        } else {
            $absoluteOb = new $absoluteClassName;
        }
        
        return [$absoluteClassName,$absoluteOb];
    }
    /**
     * @property $absoluteClassName | String , like: '\app\appfront\modules\Cms\block\home\Index'
     * 通过$rewriteMap，查找是否存在重写，如果存在，则返回重写的className
     */
    public static function mapGetName($absoluteClassName){
        if(isset(self::$rewriteMap[$absoluteClassName]) && self::$rewriteMap[$absoluteClassName]){
            $absoluteClassName = self::$rewriteMap[$absoluteClassName];
        }
        return $absoluteClassName;
    }
}

spl_autoload_register(['Yii', 'autoload'], true, true);
Yii::$classMap = require $dir.'/classes.php';
Yii::$container = new yii\di\Container();
