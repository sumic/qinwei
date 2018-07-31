<?php 
/**
 * =======================================================
 * @Description : wechat interfaces
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年7月31日 下午4:24:12
 * @version: v1.0.0
 */
namespace core\interfaces;

interface WechatInterface{
    /**
     * 请求微信服务器获取AccessToken
     * 必须返回以下格式内容
     * [
     *     'access_token => 'xxx',
     *     'expirs_in' => 7200
     * ]
     * @return array|bool
     */
    public function requestAccessToken();
    
    /**
     * 请求微信服务器获取JsApiTicket
     * 必须返回以下格式内容
     * [
     *     'ticket => 'xxx',
     *     'expirs_in' => 7200
     * ]
     * @return array|bool
     */
    public function requestJsApiTicket();
    
    /**
     * 生成js 必要的config
     */
    public function jsApiConfig(array $config = []);
    
    /**
     * 创建消息加密类
     * @return mixed
     */
    public function createMessageCrypt();
    /**
     * 微信数据缓存基本键值
     * @param $name
     * @return string
     */
    public function getCacheKey($name);
    
    /**
     * 解析微信请求响应内容
     * @param callable $callable Http请求主体函数
     * @param string $url Api地址
     * @param array|string|null $postOptions Api地址一般所需要的post参数
     * @return array|bool
     */
    public function parseHttpRequest(callable $callable, $url, $postOptions = null);
    
}
