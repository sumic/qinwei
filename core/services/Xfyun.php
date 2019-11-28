<?php

namespace core\services;

use DOMDocument;
use DOMElement;
use DOMText;
use Yii;
use yii\web\HttpException;
use yii\base\InvalidParamException;
use core\services\Service;

/**
 * Xfyun API 操作基类
 */
class Xfyun extends Service
{
    /**
     * 返回错误码
     * @var array
     */
    public $lastError;

    /**
     * Api url 组装
     * @param $url
     * @param array $options
     * @return string
     */
    protected function actionHttpBuildQuery($url, array $options)
    {
        if (!empty($options)) {
            $url .= (stripos($url, '?') === null ? '&' : '?') . http_build_query($options);
        }
        return $url;
    }

    /**
     * Http Get 请求
     * @param $url
     * @param array $options
     * @return mixed
     */
    public function httpGet($url, array $options = [])
    {
        Yii::info([
            'url' => $url,
            'options' => $options
        ], __METHOD__);
        return $this->parseHttpRequest(function ($url) {
            return $this->http($url);
        }, $this->httpBuildQuery($url, $options));
    }

    /**
     * Http Post 请求
     * @param $url
     * @param array $postOptions
     * @param array $options
     * @return mixed
     */
    public function httpPost($url, array $postOptions, array $options = [], array $header = [])
    {
        Yii::info([
            'url' => $url,
            'postOptions' => $postOptions,
            'options' => $options,
            'header' => $header
        ], __METHOD__);
        return $this->parseHttpRequest(function ($url, $postOptions, $header) {
            return $this->http($url, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postOptions,
                CURLOPT_HTTPHEADER => $header
            ]);
        }, $this->httpBuildQuery($url, $options), $postOptions, $header);
    }

    /**
     * Http Raw数据 Post 请求
     * @param $url
     * @param $postOptions
     * @param array $options
     * @return mixed
     */
    public function httpRaw($url, $postOptions, array $options = [], array $header = [])
    {
        Yii::info([
            'url' => $url,
            'postOptions' => $postOptions,
            'options' => $options,
            'header' => $header
        ], __METHOD__);
        return $this->parseHttpRequest(function ($url, $postOptions, $header) {
            return $this->http($url, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => is_array($postOptions) ? json_encode($postOptions, JSON_UNESCAPED_UNICODE) : $postOptions,
                CURLOPT_HTTPHEADER => $header
            ]);
        }, $this->httpBuildQuery($url, $options), $postOptions);
    }

    /**
     * Http基础库 使用该库请求服务器
     * @param $url
     * @param array $options
     * @return bool|mixed
     */
    protected function http($url, $options = [])
    {
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
        ] + (stripos($url, "https://") !== false ? [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ] : [])
            + (class_exists('\CURLFile') ? [CURLOPT_SAFE_UPLOAD => true] : [CURLOPT_SAFE_UPLOAD => false])
            + $options;

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $content = curl_exec($curl);
        $status = curl_getinfo($curl);
        $errors = curl_error($curl);
        curl_close($curl);
        if (isset($status['http_code']) && $status['http_code'] == 200) {
            return json_decode($content, true) ?: false; // 正常加载应该是只返回json字符串
        }
        Yii::error([
            'result' => $content,
            'status' => $status,
            'errors' => $errors
        ],  __METHOD__);
        return false;
    }

    /**
     * 上传文件请使用该类来解决curl版本兼容问题
     * @param $filePath
     * @return \CURLFile|string
     */
    protected function uploadFile($filePath)
    {
        // php 5.5将抛弃@写法,引用CURLFile类来实现 @see http://segmentfault.com/a/1190000000725185
        return class_exists('\CURLFile') ? new \CURLFile($filePath) : '@' . $filePath;
    }
}
