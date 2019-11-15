<?php
/**
 * =======================================================
 * @Description :upload helpers
 * 使用时需要定义上传的类型$senction，通过类型验证场景 
 * 类型有 image file video
 * up($filename ,$senction)
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月28日
 * @version: v1.0.0
 */
namespace core\services\helper;

use core\services\Service;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
//use yii\imagine\Image;
use yii\image\drivers\Image;
use yii;
use phpDocumentor\Reflection\Types\This;

class Uploader extends Service
{
    /**
     * 上传目录
     * @var string
     */
    public $uploadPath = "@uploads";
    /**
     * 配置信息
     * @var array
     */
    public  $config = []; 
    
    /**
     * 缩略图设置
     * 默认不开启
     * ['height' => 200, 'width' => 200]表示生成200*200的缩略图，如果设置为空数组则不生成缩略图
     *
     * @var array
     */
    public $thumbnail = [];
    
    /**
     * 图片缩放设置
     * 默认不缩放。
     * 配置如 ['height'=>200,'width'=>200]
     *
     * @var array
     */
    public $zoom = [];
    
    /**
     * 水印设置
     * 参考配置如下：
     * ['path'=>'水印图片位置','offset_x'=>0,'offset_y'=>0]
     * @param   integer       $offset_x   offset from the left
     * @param   integer       $offset_y   offset from the top
     * 默认位置为 右下角，可不配置
     *
     * @var array
     */
    public $watermark = [];
    
    /**
     * 是否允许内网采集
     * 如果为 false 则远程图片获取不获取内网图片，防止 SSRF。
     * 默认为 false
     *
     * @var bool
     */
    private $allowIntranet = false;
    
    private $fileField; //文件域名
    private $file; //文件上传对象
    private $oriName; //原始文件名
    private $fileName; //新文件名
    private $fullName; //完整文件名,即从当前配置目录开始的URL
    private $filePath; //完整文件名,即从当前配置目录开始的URL
    private $fileSize; //文件大小
    private $fileType; //文件类型
    private $stateInfo; //上传状态信息,
    private $stateMap = array( //上传状态映射表，国际化用户需考虑此处数据的国际化
        "SUCCESS", //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        "文件大小超出 upload_max_filesize 限制",
        "文件大小超出 MAX_FILE_SIZE 限制",
        "文件未被完整上传",
        "没有文件被上传",
        "上传文件为空",
        "ERROR_TMP_FILE" => "临时文件错误",
        "ERROR_TMP_FILE_NOT_FOUND" => "找不到临时文件",
        "ERROR_SIZE_EXCEED" => "文件大小超出网站限制",
        "ERROR_TYPE_NOT_ALLOWED" => "文件类型不允许",
        "ERROR_CREATE_DIR" => "目录创建失败",
        "ERROR_DIR_NOT_WRITEABLE" => "目录没有写权限",
        "ERROR_FILE_MOVE" => "文件保存时出错",
        "ERROR_FILE_NOT_FOUND" => "找不到上传文件",
        "ERROR_WRITE_CONTENT" => "写入文件内容错误",
        "ERROR_UNKNOWN" => "未知错误",
        "ERROR_DEAD_LINK" => "链接不可用",
        "ERROR_HTTP_LINK" => "链接不是http链接",
        "ERROR_HTTP_CONTENTTYPE" => "链接contentType不正确",
        "INVALID_URL" => "非法 URL",
        "INVALID_IP" => "非法 IP"
    );
    
    private $_fileType = array(
        'image' => 1,
        'voice' => 2,
        'video' => 3,
        'thumb' => 4,
        'playback' => 5,
        'file' => 9
    );

    protected $_modelName = '\core\models\mysqldb\uploads\Uploads';
    protected $_model;
    
    public function init()
    {
        list($this->_modelName,$this->_model) = \Yii::mapGet($this->_modelName);
        //默认配置信息
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
        //默认上传地址
        $this->uploadPath = \Yii::getAlias($this->uploadPath);
        $this->config = $this->_model->config = $this->config + $default ;
        if (! is_array($this->thumbnail)) {
            $this->thumbnail = false;
        }
    }
    
    public function actionGetModel()
    {
        return $this->_model;
    }
    
    protected function actionGetModelName()
    {
        return get_class($this->_model);
    }
    
    protected function actionGetPrimaryKey()
    {
        return 'id';
    }
    
    /**
     * $strField 上传表单名称
     * $scenario 模型验证场景$image = $imageComponent->load($rootPath . $file);
     * @param string $strField
     * @param string $scenario
     * @throws \UnexpectedValueException
     * @return array
     */
    public function up($strField,$scenario)
    {
        // 初始化上次表单model对象，并定义好验证场景
        $model = $this->_model;
        $model->scenario = $scenario;
        
        //确保文件上传后写入数据库，使用了事务
        //开始事务
        $objFile = UploadedFile::getInstanceByName($strField);
        $innerTransaction = Yii::$app->db->beginTransaction();
        try {
            //文件类型
            $model->type = $this->_fileType[$scenario];
            // 上传文件
            $objFile = UploadedFile::getInstanceByName($strField);
            $this->oriName = $objFile->baseName;
            $model->ext  = $this->fileType = $objFile->extension;
            $model->name = $objFile->baseName.'.'.$objFile->extension;
            $model->size = $this->fileSize = $objFile->size;
            $model->mime = $objFile->type;
            $model->md5 = md5_file($objFile->tempName);
            $model->sha1 = sha1_file($objFile->tempName);
            //文件MD5已经存在
            if(!$model->validate('md5')){
                $file = $model->find()->where(['md5'=>$model->md5])->one();
                //检测文件是否在本地存在，存在返回数据库信息
                $truePath = \Yii::getAlias('@uploads').$file->url;
                if(file_exists($truePath)){
                    $result = [
                        "state" => 'SUCCESS',
                        "message"=> '文件存在，已经取回',
                        "url" =>  $file->url,
                        "savefile" => $file->savename,
                        "original" => $file->name,
                        "type" => $file->ext,
                        "size" => $file->size
                    ];
                    //结束事务返回数据
                    $innerTransaction->commit();
                    return $result;
                }else{
                    //数据库存在，文件不存在删除垃圾文件信息，重新上传。
                    $file->delete();
                }
            }
            if(!$model->validate()){
                throw new \UnexpectedValueException($model->getFirstError($scenario));
            }
            
            //文件保存路径格式
            $pathFormat = $this->config[$scenario.'PathFormat'];
            //生成文件名称
            $fullname = $this->getFullName($pathFormat);
            //文件保存地址带名称
            $filepath = $this->getFilePath($fullname);
            //文件保存名
            $filename = $this->getFileName($filepath);
            //文件保存URL
            $model->url = $this->getFileUrl($filepath);
            $model->savename = $filename;
            //文件保存相对目录
            $model->savepath = $this->getSavePath($filepath);
            
            //文件保存绝对目录，目录不存在那么创建
            $dirname = dirname($filepath);
            
            FileHelper::createDirectory($dirname);
            if (!file_exists($dirname)) {
                throw new \UnexpectedValueException('目录创建失败:' . $dirname);
            }
            if ($model->save()){
                if($objFile->saveAs($filepath)){
                    $result = [
                        "state" => 'SUCCESS',
                        "message" => '上传成功',
                        "url" =>  $model->url,
                        "savefile" => $model->savename,
                        "original" => $model->name,
                        "type" => $model->ext,
                        "size" => $model->size
                    ];
                    //上传成功提交事务
                    $innerTransaction->commit();
                    //如果上传的是图片，后续缩略图、缩放、水印操作
                    if (($this->thumbnail || $this->zoom || $this->watermark)  && in_array($result['type'], ['png','jpg','bmp','gif'])
                        ) {
                            $result['thumbnail'] = $this->imageHandle($result['url']);
                        }
                    return $result;
                }else {
                    throw new \UnexpectedValueException('上传文件转移失败');
                }
            } 
        } catch (\Exception $e) {
            //上传失败事务回滚
            $innerTransaction->rollBack();
            Yii::$service->helper->errors->add($e->getMessage(),'上传失败');
            $result = [
                "state" => $e->getMessage(),
                "message" => $e->getMessage(),
                "url" =>  $model->url,
                "savefile" => $model->savename,
                "original" => $model->name,
                "type" => $model->ext,
                "mime" => $model->mime,
                "size" => $model->size
            ];
            return $result;
        }
    }
    /**
     * 设置是否允许获取内网图片
     *
     * @param boolean $allow
     */
    public function setAllowIntranet($allow)
    {
        $this->allowIntranet = $allow ? true : false;
    }
    
    /**
     * 处理base64编码的图片上传
     *
     * @return mixed
     */
    protected function actionUpbase64($base64Data,$scenario)
    {
        $imgfile = base64_decode($base64Data);
        //确保文件上传后写入数据库，使用了事务
        $innerTransaction = Yii::$app->db->beginTransaction();
        try {
            //实例化模型
            $model = $this->_model;
            $model->scenario = $scenario;
            $model->name = $this->oriName = 'scrawl.png';
            $model->size = $this->fileSize = strlen($imgfile);
            $model->mime = 'image/png';
            //这里用文件URL来生成MD5
            $model->md5 = md5($base64Data);
            $model->sha1 = sha1($base64Data);
            $model->ext = $this->fileType = $this->getFileExt($model->name);
            
            //检查文件大小是否超出限制
            if ($this->fileSize >= $this->config['scrawlMaxSize']) {
                throw new \UnexpectedValueException($this->getStateInfo("ERROR_SIZE_EXCEED"));
            }
            //文件MD5已经存在
            if(!$model->validate('md5')){
                $file = $model->find()->where(['md5'=>$model->md5])->one();
                //检测文件是否在本地存在，存在返回数据库信息
                $truePath = \Yii::getAlias('@uploads').$file->url;
                if(file_exists($truePath)){
                    $result = [
                        "state" => 'SUCCESS',
                        "message"=> '文件存在，已经取回',
                        "url" =>  $file->url,
                        "savefile" => $file->savename,
                        "original" => $file->name,
                        "type" => $file->ext,
                        "size" => $file->size
                    ];
                    return $result;
                }else{
                    //数据库存在，文件不存在删除垃圾文件信息，重新上传。
                    $file->delete();
                }
            }
            //文件保存路径格式
            $pathFormat = $this->config[$scenario.'PathFormat'];
            //生成文件名称
            $fullname = $this->getFullName($pathFormat);
            //文件保存地址带名称
            $filepath = $this->getFilePath($fullname);
            //文件保存名
            $filename = $this->getFileName($filepath);
            //文件保存URL
            $model->url = $this->getFileUrl($filepath);
            $model->savename = $filename;
            //文件保存相对目录
            $model->savepath = $this->getSavePath($filepath);
            
            if(!$model->validate()){
                throw new \UnexpectedValueException($model->getFirstError($scenario));
            }
            
            //文件保存绝对目录，目录不存在那么创建
            $dirname = dirname($filepath);
            FileHelper::createDirectory($dirname);
            if (!file_exists($dirname)) {
                throw new \UnexpectedValueException('目录创建失败:' . $dirname);
            }
            
            if ($model->save()){
                //移动文件
                if (! (file_put_contents($filepath, $imgfile) && file_exists($filepath))) { //移动失败
                    throw new \UnexpectedValueException($this->getStateInfo("ERROR_WRITE_CONTENT"));
                } else { //移动成功
                    $result = [
                        "state" => 'SUCCESS',
                        "message" => '上传成功',
                        "url" =>  $this->config['imageUrlPrefix'].$model->url,
                        "savefile" => $model->savename,
                        "original" => $model->name,
                        "type" => $model->ext,
                        "size" => $model->size,
                        "source" => $imgUrl
                    ];
                    //上传成功提交事务
                    $innerTransaction->commit();
                    //如果上传的是图片，后续缩略图、缩放、水印操作
                    if (($this->thumbnail || $this->zoom || $this->watermark)  && in_array($result['type'], ['png','jpg','bmp','gif','jpeg'])
                        ) {
                            $result['thumbnail'] = $this->imageHandle($result['url']);
                        }
                        return $result;
                }
            }else{
                throw new \UnexpectedValueException($model->getFirstErrors());
            }
        }catch (\Exception $e) {
            //上传失败事务回滚
            $innerTransaction->rollBack();
            Yii::$service->helper->errors->add($e->getMessage(),'上传失败');
            $result = [
                "state" => $e->getMessage(),
                "message" => $e->getMessage(),
                "url" =>  $model->url,
                "savefile" => $model->savename,
                "original" => $model->name,
                "type" => $model->ext,
                "size" => $model->size
            ];
            return $result;
        }
        
    }
    
    /**
     * 拉取远程图片
     *
     * @return mixed
     */
    protected function actionSaveremote($imgUrl,$scenario)
    {
        $imgUrl = htmlspecialchars($imgUrl);
        $imgUrl = str_replace("&amp;", "&", $imgUrl);
        //确保文件上传后写入数据库，使用了事务
        $innerTransaction = Yii::$app->db->beginTransaction();
        try{
            //http开头验证
            if (strpos($imgUrl, "http") !== 0) {
                throw new \UnexpectedValueException($this->getStateInfo("ERROR_HTTP_LINK"));
            }
            
            preg_match('/(^https?:\/\/[^:\/]+)/', $imgUrl, $matches);
            $host_with_protocol = count($matches) > 1 ? $matches[1] : '';
            
            // 判断是否是合法 url
            if (! filter_var($host_with_protocol, FILTER_VALIDATE_URL)) {
                throw new \UnexpectedValueException($this->getStateInfo("INVALID_URL"));
            }
            
            preg_match('/^https?:\/\/(.+)/', $host_with_protocol, $matches);
            $host_without_protocol = count($matches) > 1 ? $matches[1] : '';
            
            // 此时提取出来的可能是 IP 也有可能是域名，先获取 IP
            $ip = gethostbyname($host_without_protocol);
            
            // 判断是否允许私有 IP
            if (! $this->allowIntranet && ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
                throw new \UnexpectedValueException($this->getStateInfo("INVALID_IP"));
            }
            
            //获取请求头并检测死链
            $heads = get_headers($imgUrl, 1);
            if (! (stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
                $error = $this->getStateInfo("ERROR_DEAD_LINK");
            }
            
            //格式验证(扩展名验证和Content-Type验证)
            $fileType = strtolower(strrchr($imgUrl, '.'));
            
            if (! in_array($fileType, $this->config['catcherAllowFiles']) || ! isset($heads['Content-Type']) || ! stristr($heads['Content-Type'], "image")) {
                throw new \UnexpectedValueException($this->getStateInfo("ERROR_HTTP_CONTENTTYPE"));
            }
            //打开输出缓冲区并获取远程图片
            ob_start();
            $context = stream_context_create([
                'http' => [
                    'follow_location' => false // don't follow redirects
                ]
            ]);
            readfile($imgUrl, false, $context);
            $imgfile = ob_get_contents();
            ob_end_clean();
            preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);
            
            
            $fileSize = strlen($imgfile);
            //检查文件大小是否超出限制
            if ($fileSize >= $this->config['catcherMaxSize']) {
                throw new \UnexpectedValueException($this->getStateInfo("ERROR_SIZE_EXCEED"));
            }
            //实例化模型
            $model = $this->_model;
            $model->scenario = $scenario;
            $model->name = $this->oriName = $m ? $m[1] : "";
            $model->size = $this->fileSize = $fileSize;
            $model->mime = $heads['Content-Type'];
            //这里用文件URL来生成MD5
            $model->md5 = md5_file($imgUrl);
            $model->sha1 = sha1_file($imgUrl);
            $model->ext = $this->fileType = $this->getFileExt($model->name);
            //文件MD5已经存在
            if(!$model->validate('md5')){
                $file = $model->find()->where(['md5'=>$model->md5])->one();
                //检测文件是否在本地存在，存在返回数据库信息
                $truePath = \Yii::getAlias('@uploads').$file->url;
                if(file_exists($truePath)){
                    $result = [
                        "state" => 'SUCCESS',
                        "message"=> '文件存在，已经取回',
                        "url" =>  $file->url,
                        "savefile" => $file->savename,
                        "original" => $file->name,
                        "mime" => $model->mime,
                        "type" => $file->ext,
                        "size" => $file->size
                    ];
                    //结束事务返回数据
                    $innerTransaction->commit();
                    return $result;
                }else{
                    //数据库存在，文件不存在删除垃圾文件信息，重新上传。
                    $file->delete();
                }
            }
            //文件保存路径格式
            $pathFormat = $this->config[$scenario.'PathFormat'];
            //生成文件名称
            $fullname = $this->getFullName($pathFormat);
            //文件保存地址带名称
            $filepath = $this->getFilePath($fullname);
            //文件保存名
            $filename = $this->getFileName($filepath);
            //文件保存URL
            $model->url = $this->getFileUrl($filepath);
            $model->savename = $filename;
            //文件保存相对目录
            $model->savepath = $this->getSavePath($filepath);
            
            if(!$model->validate()){
                throw new \UnexpectedValueException($model->getFirstError($scenario));
            }
            
            //文件保存绝对目录，目录不存在那么创建
            $dirname = dirname($filepath);
            FileHelper::createDirectory($dirname);
            if (!file_exists($dirname)) {
                throw new \UnexpectedValueException('目录创建失败:' . $dirname);
            }
            
            if ($model->save()){
                //移动文件
                if (! (file_put_contents($filepath, $imgfile) && file_exists($filepath))) { //移动失败
                    throw new \UnexpectedValueException($this->getStateInfo("ERROR_WRITE_CONTENT"));
                } else { //移动成功
                    $result = [
                        "state" => 'SUCCESS',
                        "message" => '上传成功',
                        "url" =>  $this->config['imageUrlPrefix'].$model->url,
                        "savefile" => $model->savename,
                        "original" => $model->name,
                        "type" => $model->ext,
                        "mime" => $model->mime,
                        "size" => $model->size,
                        "source" => $imgUrl
                    ];
                    //上传成功提交事务
                    $innerTransaction->commit();
                    //如果上传的是图片，后续缩略图、缩放、水印操作
                    if (($this->thumbnail || $this->zoom || $this->watermark)  && in_array($result['type'], ['png','jpg','bmp','gif'])
                        ) {
                            $result['thumbnail'] = $this->imageHandle($result['url']);
                        }
                        return $result;
                }
            }else{
                throw new \UnexpectedValueException($model->getFirstErrors());
            }
        } catch (\Exception $e) {
            //上传失败事务回滚
            $innerTransaction->rollBack();
            Yii::$service->helper->errors->add($e->getMessage(),'上传失败');
            $result = [
                "state" => $e->getMessage(),
                "message" => $e->getMessage(),
                "url" =>  $model->url,
                "savefile" => $model->savename,
                "original" => $model->name,
                "type" => $model->ext,
                "mime" => $model->mime,
                "size" => $model->size
            ];
            return $result;
        }
    }
    
    /**
     * 上传错误检查
     *
     * @param $errCode
     * @return string
     */
    private function getStateInfo($errCode)
    {
        return ! $this->stateMap[$errCode] ? $this->stateMap["ERROR_UNKNOWN"] : $this->stateMap[$errCode];
    }
    
    /**
     * 获取文件扩展名
     *
     * @todo .tar.gz 扩展名
     * @return string
     */
    private function getFileExt($filename)
    {
        return substr($filename,strripos($filename,'.') + 1);
    }
    
    /**
     * 重命名文件
     *
     * @return string
     */
    private function getFullName($pathFormat)
    {
        //替换日期时间
        $t = time();
        $d = explode('-', date("Y-y-m-d-H-i-s"));
        $format = $pathFormat;
        $format = str_replace("{yyyy}", $d[0], $format);
        $format = str_replace("{yy}", $d[1], $format);
        $format = str_replace("{mm}", $d[2], $format);
        $format = str_replace("{dd}", $d[3], $format);
        $format = str_replace("{hh}", $d[4], $format);
        $format = str_replace("{ii}", $d[5], $format);
        $format = str_replace("{ss}", $d[6], $format);
        $format = str_replace("{time}", $t, $format);
        
        //过滤文件名的非法自负,并替换文件名
        $oriName = preg_replace("/[\|\?\"\<\>\/\*\\\\]+/", '', $this->oriName);
        $format = str_replace("{filename}", $oriName, $format);
        
        //替换随机字符串
        $randNum = rand(1, 10000000000) . rand(1, 10000000000);
        if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, $matches[1]), $format);
        }
        
        $ext = $this->fileType;
        return $format . '.'.$ext;
    }
    
    /**
     * 获取文件名
     *
     * @return string
     */
    private function getFileName($filePath)
    {
        return substr($filePath, strrpos($filePath, '/') + 1);
    }
    
    /**
     * 获取文件名没有后缀
     *
     * @return string
     */
    private function getFileNameNoExt($filename)
    {
        if (substr($filename, 0, 1) != '/') {
            $filename = '/' . $filename;
        }
        return substr($filename, 0,strrpos($filename, '.') );
    }
    
    /**
     * 获取文件完整路径
     * @return string
     */
    private function getFilePath($fullname)
    {
        $rootPath = $this->uploadPath;
        
        if (substr($fullname, 0, 1) != '/') {
            $fullname = '/' . $fullname;
        }
        
        return $rootPath . $fullname;
    }
    
    /**
     * 获取文件完整URL
     * @return string
     */
    private function getFileUrl($filepath)
    {
        return str_replace(\Yii::getAlias("@uploads"),'',$filepath);
    }
    
    /**
     * 获取文件保存目录
     * 替换默认目录@uploads
     * @return string
     */
    private function getSavePath($filepath)
    {
        if (substr($filepath, 0, 1) != '/') {
            $filepath = '/' . $filepath;
        }
        return str_replace(\Yii::getAlias("@uploads"),'',substr($filepath, 0, strripos($filepath, '/')));
    }
    
    /**
     * 文件类型检测
     *
     * @return bool
     */
    private function checkType()
    {
        return in_array($this->getFileExt(), $this->config["allowFiles"]);
    }
    
    /**
     * 文件大小检测
     *
     * @return bool
     */
    private function checkSize($filesize)
    {
        return $this->fileSize <= ($this->config["maxSize"]);
    }
    
    /**
     * 获取当前上传成功文件的各项信息
     *
     * @return array
     */
    public function getFileInfo($model)
    {
        //$prefix = str_replace(yii::getAlias(yii::getAlias('@appadmin/web')), '', yii::getAlias('@ueditor'));
        return array(
            "state" => $this->stateInfo,
            "url" =>  $this->fullName,
            "title" => $this->fileName,
            "original" => $this->oriName,
            "type" => $this->fileType,
            "size" => $this->fileSize
        );
    }
    /**
     * 自动处理图片
     *
     * @param $file
     * @return mixed|string
     */
    protected function actionImageHandle($file)
    {
        if (substr($file, 0, 1) != '/') {
            $file = '/' . $file;
        }
        $rootPath = \Yii::getAlias("@uploads");
        //先处理缩略图
        /* @var $image yii\image\drivers\Kohana_Image_GD */
        $imageComponent = Yii::$app->get('image');
        
        if ($imageComponent && $this->thumbnail && ! empty($this->thumbnail['height']) && ! empty($this->thumbnail['width'])) {
            $fileInfo = pathinfo($file);
            $fileThumb = $fileInfo['dirname'] . '/' . $fileInfo['filename'] . '.thumb.' . $fileInfo['extension'];
            $image = $imageComponent->load($rootPath . $file);
            /* Image::thumbnail($rootPath . $file, intval($this->thumbnail['width']), intval($this->thumbnail['height']))
            ->save($rootPath . $fileThumb); */
            $image->resize(intval($this->thumbnail['width']), intval($this->thumbnail['height']), Image::CROP)->save($rootPath . $fileThumb);
        }
        //再处理缩放，默认不缩放
        if ($imageComponent && isset($this->zoom['height']) && isset($this->zoom['width'])) {
            $image = $imageComponent->load($rootPath . $file);
            $image->resize(intval($this->zoom['width']), intval($this->zoom['height']), Image::CROP)->save($rootPath . $file);
        }
        //最后生成水印
        if (isset($this->watermark['path']) && file_exists(\Yii::getAlias($this->watermark['path']))) {
            if (! isset($this->watermark['offset_x']) or ! isset($this->watermark['offset_y'])) {
                $this->watermark['offset_x'] = $this->watermark['offset_y'] = TRUE;
            }
            $image = $imageComponent->load($rootPath . $file);
            $mark  = $imageComponent->load(\Yii::getAlias($this->watermark['path']));
            $image->watermark($mark, $this->watermark['offset_x'], $this->watermark['offset_y'])->save($rootPath . $file);;
        }
        
        return $fileThumb;
    }
    
    /**
     * 获取图片的大小
     * 主要用于获取图片大小并
     *
     * @param $file
     * @return array
     */
    protected function getSize($file)
    {
        if (! file_exists($file)) {
            return [];
        }
        
        $info = pathinfo($file);
        $image = null;
        switch (strtolower($info['extension'])) {
            case 'gif':
                $image = imagecreatefromgif($file);
                break;
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($file);
                break;
            case 'png':
                $image = imagecreatefrompng($file);
                break;
            default:
                break;
        }
        if ($image == null) {
            return [];
        } else {
            return [imagesx($image), imagesy($image)];
        }
    }
    
    //根据ID删除数据，使用了事务
    public function remove($ids)
    {
        if (!$ids) {
            Yii::$service->helper->errors->add('没有选中删除项。');
            return false;
        }
        $innerTransaction = Yii::$app->db->beginTransaction();
        if (is_array($ids) && !empty($ids)) {
            foreach ($ids as $id) {
                if(!$result = $this->removeOne($id, $innerTransaction))return false;
            }
        } else {
            $id = $ids;
            $result = $this->removeOne($id, $innerTransaction);
        }
        if($result){
            $innerTransaction->commit();
            return $result;
        }else{
            return false;
        }
    }
    
    public function removeOne($id,$innerTransaction){
        $model = $this->_model->findOne($id);
        // 旧的地址
        if (!empty($model['url']) && file_exists($this->uploadPath. $model['url'])) unlink($this->uploadPath. $model['url']);
        // 删除之前的缩略图
        if (!empty($model['savename']) && !empty($model['savepath'])) {
            //得到缩略图路径
            $thumbPath = $this->uploadPath. $model['savepath'].$this->getFileNameNoExt($model['savename']).'.thumb.'.$model['ext'];
            if (file_exists($thumbPath)) @unlink($thumbPath);
        }
        if (isset($model[$this->getPrimaryKey()]) && !empty($model[$this->getPrimaryKey()])) {
            try {
                $model->delete();
            } catch (\Exception $e) {
                Yii::$service->helper->errors->add($e->getMessage(). "事务已回滚");
                $innerTransaction->rollBack();
                return false;
            }
            return $model;
        } else {
            Yii::$service->helper->errors->add("ID $id 不存在.");
            return false;
        }
    }
    
    public function removeByUrl($url)
    {
        if($url)
        {
            $result = $this->_model->findOne(['url'=>$url]);
            return $result ? $this->remove($result->id) : false;
        }
    }
}