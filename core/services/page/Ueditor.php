<?php
/**
 * =======================================================
 * @Description :ueditor service
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月18日
 * @version: v1.0.0
 */

namespace core\services\page;

use core\services\Service;
use yii;

class Ueditor extends Service
{
    
   /**
    * UEditor的配置
    *
    * @see http://fex-team.github.io/ueditor/#start-config
    * @var array
    */
    public $config = [];
    
    /**
     * 上传地址
     *
     * @see http://fex-team.github.io/ueditor/#start-config
     * @var array
     */
    public $uploadPath;
    /**
     * 上传模块
     * @var array
     */
    public $uploader;
    
    /**
     * 是否允许内网采集
     * 如果为 false 则远程图片获取不获取内网图片，防止 SSRF。
     * 默认为 false
     *
     * @var bool
     */
    public $allowIntranet = true;
    /**
     * 列出文件/图片时需要忽略的文件夹
     * 主要用于处理缩略图管理，兼容比如elFinder之类的程序
     *
     * @var array
     */
    public $ignoreDir = [
        '.thumbnails'
    ];
    public function init()
    {
        //初始化上传助手
        $this->uploader = \Yii::$service->helper->uploader;
        
        if(!empty($this->uploadPath)){
            $this->uploader->uploadPath = $this->uploadPath;
        }
        //当客户使用低版本IE时，会使用swf上传插件，维持认证状态可以参考文档UEditor「自定义请求参数」部分。
        //http://fex.baidu.com/ueditor/#server-server_param
        if (!is_array($this->config)) {
            $this->config = [];
        }
        $default = [
            'imagePathFormat' => '/image/{yyyy}{mm}{dd}/{time}{rand:8}',
            'scrawlPathFormat' => '/image/{yyyy}{mm}{dd}/{time}{rand:8}',
            'snapscreenPathFormat' => '/image/{yyyy}{mm}{dd}/{time}{rand:8}',
            'catcherPathFormat' => '/image/{yyyy}{mm}{dd}/{time}{rand:8}',
            'videoPathFormat' => '/video/{yyyy}{mm}{dd}/{time}{rand:8}',
            'filePathFormat' => '/file/{yyyy}{mm}{dd}/{rand:8}_{filename}',
            'imageManagerListPath' => '/image/',
            'fileManagerListPath' => '/file/',
            "imageUrlPrefix" => yii::$app->params['site-img'],
        ];
        $this->uploader->config = $this->uploader->config + $this->config;
        $this->config = $default + $this->config;
        parent::init();
    }
    
    /**
     * 显示配置信息
     */
    protected function actionConfig()
    {
        return $this->show($this->config);
    }
    /**
     * 上传图片
     */
    protected function actionUploadimage()
    {
        $fieldName = $this->config['imageFieldName'];
        $result = $this->uploader->up($fieldName,'image');
        $result['fieldname'] = $fieldName;
        return $this->show($result);
    }
    
    /**
     * 上传涂鸦
     */
    public function actionUploadScrawl()
    {
        $fieldName = $this->config['scrawlFieldName'];
        $base64Data = $_POST[$fieldName];
        $result = $this->uploader->upbase64($base64Data,'image');
        $result['fieldname'] = $fieldName;
        return $this->show($result);
    }
    
    /**
     * 上传视频
     */
    public function actionUploadVideo()
    {
        $fieldName = $this->config['fileFieldName'];
        $result = $this->uploader->up($fieldName,'video');
        $result['fieldname'] = $fieldName;
        return $this->show($result);
    }
    
    /**
     * 上传文件
     */
    public function actionUploadFile()
    {
        $fieldName = $this->config['fileFieldName'];
        $result = $this->uploader->up($fieldName,'file');
        $result['fieldname'] = $fieldName;
        return $this->show($result);
    }
    
    /**
     * 文件列表
     */
    public function actionListFile()
    {
        $allowFiles = $this->config['fileManagerAllowFiles'];
        $listSize = $this->config['fileManagerListSize'];
        $path = $this->config['fileManagerListPath'];
        $result = $this->manage($allowFiles, $listSize, $path);
        return $this->show($result);
    }
    
    /**
     *  图片列表
     */
    public function actionListImage()
    {
        $allowFiles = $this->config['imageManagerAllowFiles'];
        $listSize = $this->config['imageManagerListSize'];
        $path = $this->config['imageManagerListPath'];
        $result = $this->manage($allowFiles, $listSize, $path);
        return $this->show($result);
    }
    
    /**
     * 获取远程图片
     */
    public function actionCatchImage()
    {
        $fieldName = $this->config['catcherFieldName'];
        /* 抓取远程图片 */
        $list = [];
        if (isset($_POST[$fieldName])) {
            $source = $_POST[$fieldName];
        } else {
            $source = $_GET[$fieldName];
        }
        foreach ($source as $imgUrl) {
            
            if ($this->allowIntranet) {
                $this->uploader->setAllowIntranet(true);
            }
            $list[] = $this->uploader->saveremote($imgUrl,'image');
        }
        /* 返回抓取数据 */
        return [
            'state' => count($list) ? 'SUCCESS' : 'ERROR',
            'list' => $list,
            'fieldname' => $fieldName
        ];
    }
    
    
    /**
     * 文件和图片管理action使用
     *
     * @param $allowFiles
     * @param $listSize
     * @param $path
     * @return array
     */
    protected function manage($allowFiles, $listSize, $path)
    {
        $allowFiles = substr(str_replace('.', '|', join('', $allowFiles)), 1);
        /* 获取参数 */
        $size = isset($_GET['size']) ? $_GET['size'] : $listSize;
        $start = isset($_GET['start']) ? $_GET['start'] : 0;
        $end = $start + $size;
        
        /* 获取文件列表 */
        $path = $this->uploadPath . (substr($path, 0, 1) == '/' ? '' : '/') . $path;
        $files = $this->getFiles($path, $allowFiles);
        if (empty($files)) {
            $result = [
                'state' => 'no match file',
                'list' => [],
                'start' => $start,
                'total' => 0,
            ];
            return $result;
        }
        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = []; $i < $len && $i >= 0 && $i >= $start; $i--) {
            $list[] = $files[$i];
        }
        /* 返回数据 */
        $result = [
            'state' => 'SUCCESS',
            'list' => $list,
            'start' => $start,
            'total' => count($files),
        ];
        return $result;
    }
    
    /**
     * 遍历获取目录下的指定类型的文件
     *
     * @param $path
     * @param $allowFiles
     * @param array $files
     * @return array|null
     */
    protected function getFiles($path, $allowFiles, &$files = [])
    {
        if (! is_dir($path)) {
            return null;
        }
        if (in_array(basename($path), $this->ignoreDir)) {
            return null;
        }
        if (substr($path, strlen($path) - 1) != '/') {
            $path .= '/';
        }
        $handle = opendir($path);
        //baseUrl用于兼容使用alias的二级目录部署方式
        //$baseUrl = yii::getAlias('@uploads');
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getFiles($path2, $allowFiles, $files);
                } else {
                    $pat = "/\.(" . $allowFiles . ")$/i";
                    if (preg_match($pat, $file)) {
                        $files[] = [
                            'url' => yii::$app->params['site-img']  . substr($path2, strlen(\Yii::getAlias('@uploads'))),
                            'mtime' => filemtime($path2)
                        ];
                    }
                }
            }
        }
        return $files;
    }
    /**
     * 最终显示结果，自动输出 JSONP 或者 JSON
     *
     * @param array $result
     * @return array
     */
    protected function actionShow($result)
    {
        $callback = Yii::$app->request->get('callback', null);
        
        if ($callback && is_string($callback)) {
            Yii::$app->response->format = yii\web\Response::FORMAT_JSONP;
            return [
                'callback' => $callback,
                'data' => $result
            ];
        }
        
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        return $result;
    }
}
