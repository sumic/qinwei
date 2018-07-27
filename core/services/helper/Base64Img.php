<?php
/**
 * =======================================================
 * @Description :cover img to base64 encode helper service
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月20日
 * @version: v1.0.0
 */
namespace core\services\helper;

use core\services\Helper;
use Yii;


class Base64Img extends Helper
{
    public function create($imgurl)
    {
        $base64_image = '';
        $image_file = \Yii::getAlias('@uploads').$imgurl;
        if(file_exists($image_file)){
            $image_info = getimagesize($image_file);
            $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
            $base64_image = 'data:' . $image_info['mime'] . ';base64,' . base64_encode($image_data);
        }
        return $base64_image;
    }
}
