<?php
/**
 * =======================================================
 * @Description :serssion 接口
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月17日
 * @version: v1.0.0
 */
namespace core\services\session;

interface SessionInterface
{
    public function set($key,$val,$timeout);

    public function get($key,$reflush);

    public function remove($key);

    public function setFlash($key,$val,$timeout);
    
    public function getFlash($key);
    
    public function destroy();
}
