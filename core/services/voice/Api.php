<?php

namespace core\services\voice;

use Yii;
use yii\base\InvalidConfigException;
use core\services\Xfyun;
use yii\authclient\signature\HmacSha1;

/**
 * 微信公众号操作Api
 * 注:部分功能因API的整体和功能性, 拆分为单独的类调用请查看compoents/mp文件夹
 */
class Api extends Xfyun
{
    /**
     * 接口基本地址
     */
    const XFYUN_BASE_URL = 'https://raasr.xfyun.cn/api';

    /**
     * 讯飞开放平台应用ID
     * @var string
     */
    public $appId;

    /**
     * 应用secretkey
     * @var string
     */
    public $secretKey;

    /**
     * 加密数字签名
     * @var string
     */
    public $signa;

    /**
     * 时间戳
     */
    public $ts;
    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->appId === null) {
            throw new InvalidConfigException('The "appId" property must be set.');
        }
        if ($this->secretKey === null) {
            throw new InvalidConfigException('The "secretKey" property must be set.');
        }
        //时间戳
        $this->ts    = time();
        //数字签名
        $this->signa = $this->createSigna();
    }

    /**
     * 基本链接
     * @inheritdoc
     */
    protected function httpBuildQuery($url, array $options)
    {
        if (stripos($url, 'http://') === false && stripos($url, 'https://') === false) {
            $url = self::XFYUN_BASE_URL . $url;
        }
        return parent::httpBuildQuery($url, $options);
    }

    /**
     * @inheritdoc
     * @param bool $force 是否强制获取access_token, 该设置会在access_token使用错误时, 是否再获取一次access_token并再重新提交请求
     */
    public function parseHttpRequest(callable $callable, $url, $postOptions = null, $header = null, $force = true)
    {
        $result = call_user_func_array($callable, [$url, $postOptions, $header]);
        if (isset($result['err_no']) && $result['err_no']) {
            $this->lastError = $result;
            Yii::warning([
                'url' => $url,
                'result' => $result,
                'postOptions' => $postOptions
            ], __METHOD__);
            switch ($result['err_no']) {
                case 40001: //access_token 失效,强制更新access_token, 并更新地址重新执行请求
                    if ($force) {
                        $url = preg_replace_callback("/access_token=([^&]*)/i", function () {
                            return 'access_token=' . $this->getAccessToken(true);
                        }, $url);
                        $result = $this->parseHttpRequest($callable, $url, $postOptions, false); // 仅重新获取一次,否则容易死循环
                    }
                    break;
            }
        }
        return $result;
    }   

    /**
     * 创建加密数字签名
     * HmacSHA1(MD5(appid + ts)，secretkey)
     * @return object
     */
    protected function actionCreateSigna()
    {
        $baseString = \md5($this->appId . $this->ts);
        $hmca = new HmacSha1();
        return $hmca->generateSignature($baseString, $this->secretKey);
    }

    /* =================== 基础接口 =================== */

    /**
     * 预处理接口
     */
    const PREPARE_PREFIX = '/prepare';
    /**
     * 请求服务器并获得taskid
     *        参数	              类型	必须	说明	示例
     * @param app_id	        string	是	讯飞开放平台应用ID	595f23df
     * @param signa	            string	是	加密数字签名（基于HMACSHA1算法，可参考实时转写生成方式或页面下方demo）	BFQEcN3SgZNC4eECvq0LFUPVHvI=
     * @param ts	            string	是	当前时间戳，从1970年1月1日0点0分0秒开始到现在的秒数	1512041814
     * @param file_len	        string	是	文件大小（单位：字节）	160044
     * @param file_name	        string	是	文件名称（带后缀）	lfasr_audio.wav
     * @param slice_num	          int	是	文件分片数目（建议分片大小为10M，若文件<10M，则slice_num=1）	1
     * @param lfasr_type	    string	否	转写类型，默认 0 0: (标准版，格式: wav,flac,opus,mp3,m4a) 2: (电话版，已取消)	0
     * @param has_participle	string	否	转写结果是否包含分词信息	false或true， 默认false
     * @param max_alternatives	string	否	转写结果中最大的候选词个数	默认：0，最大不超过5
     * @param speaker_number	string	否	发音人个数，可选值：0-10，0表示盲分 注：发音人分离目前还是测试效果达不到商用标准，如测试无法满足您的需求，请慎用该功能。	默认：2（适用通话时两个人对话的场景）
     * @param has_seperate	    string	否	转写结果中是否包含发音人分离信息	false或true，默认为false
     * @param has_sensitive	    string	否	是否需要对转写结果进行敏感词检测	false或true， 默认：false
     * @param sensitive_type	string	否	敏感词检测类型	需要进行敏感词检测(has_sensitive为true)时必传，0(默认词库)或1(自定义敏感词)
     * @param keywords	        string	否	自定义的敏感词	敏感词检测类型为1时必传，格式：科大讯飞,语音转写（每个词用英文逗号分割，整个字符串长度不超过256）
     * @param language	        string	否	语种cn:中文（默认）en:英文（英文不支持热词）	cn     
     * @param pd                string  否  垂直领域个性化参数:法院: court 教育: edu 金融: finance 医疗: medical 科技: tech
     * @return array|bool 
     **/
    public function prepare($fileId)
    {
        //获取文件详情
        $fileInfo = \Yii::$service->helper->uploader->getByPrimaryKey($fileId);
        if ($fileInfo && file_exists(\Yii::getAlias('@uploads') . $fileInfo->url)) {
            $result = $this->httpPost(self::PREPARE_PREFIX, [], [
                'app_id'    => (string) $this->appId,
                'signa'     => (string) $this->signa,
                'ts'        => (string) $this->ts,
                'file_len'  => (string) $fileInfo->size,
                'file_name' => (string) $fileInfo->savename,
                'slice_num' => '1',
                'has_seperate' => 'true',
                'has_sensitive' => 'true',
                'sensitive_type' => '1',
                'keywords' => '你好,信用卡',
                'pd' => 'finance'

            ], [
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
            ]);
            if ($result['ok'] == 0) {
                return $result;
            } else {
                //获取失败抛出异常
                Yii::$app->response->statusCode = 400;
                Yii::$app->response->data = \Yii::$service->helper->json->error($result['err_no'], $result['failed']);
                return false;
            }
        }
    }

    /**
     * 上传文件
     */
    const UPLOAD_PREFIX = '/upload';
    /**
     * 上传文件到讯飞云
     * @return array|bool
     * @throws \yii\web\HttpException
     */
    public function upload($voiceModel)
    {
        //获取文件详情
        $fileInfo = \Yii::$service->helper->uploader->getByPrimaryKey($voiceModel->fid);
        $filePath = \Yii::getAlias('@uploads') . $fileInfo->url;

        if ($fileInfo && file_exists($filePath)) {
            $result = $this->httpPost(self::UPLOAD_PREFIX,   [
                'app_id'    => (string) $this->appId,
                'signa'     => (string) $this->signa,
                'ts'        => (string) $this->ts,
                'task_id'  => (string) $voiceModel->taskid,
                'slice_id' => 'aaaaaaaaaa',
                'content' => $this->uploadFile($filePath),
            ], [], [
                'Content-Type: multipart/form-data'
            ]);
        }
        return isset($result['ok']) ? $result : false;
    }


    /**
     * 获取结果接口
     */
    const GETRESULT_PREFIX = '/getResult';
    /**
     * 发送客服消息
     * @param array $data
     * @return bool
     * @throws \yii\web\HttpException
     */
    public function getResult($taskid)
    {
        $result = $this->httpPost(self::GETRESULT_PREFIX, [], [
            'app_id'    => (string) $this->appId,
            'signa'     => (string) $this->signa,
            'ts'        => (string) $this->ts,
            'task_id'  => (string) $taskid
        ], [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
        ]);
        return isset($result['ok']) ? $result : false;
    }

    /**
     * 合并文件
     */
    const MERGE_PREFIX = '/merge';

    public function megreFile($taskid)
    {
        $result = $this->httpPost(self::MERGE_PREFIX, [], [
            'app_id'    => (string) $this->appId,
            'signa'     => (string) $this->signa,
            'ts'        => (string) $this->ts,
            'task_id'  => (string) $taskid
        ], [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
        ]);
        return isset($result['ok']) ? $result : false;
    }

    /**
     * 查询处理进度
     */
    const PROGRESS_PREFIX = '/getProgress';

    public function porcessFile($taskid)
    {
        $result = $this->httpPost(self::PROGRESS_PREFIX, [], [
            'app_id'    => (string) $this->appId,
            'signa'     => (string) $this->signa,
            'ts'        => (string) $this->ts,
            'task_id'  => (string) $taskid
        ], [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
        ]);
        return isset($result['ok']) ? $result : false;
    }
}
