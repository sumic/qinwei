<?php
/**
 * =======================================================
 * @Description :admin uploads model
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月6日
 * @version: v1.0.0
 */
namespace core\models\mysqldb\admin;

use \yii\base\Model;

class AdminUpload extends Model
{


    // 定义字段
    public $avatar;  // 管理员个人页面上传头像
    public $face;    // 管理员信息页面上传头像

    public $url;

    // 设置应用场景
    public function scenarios()
    {
        return [
            'avatar' => ['avatar'],
            'face'   => ['face'],
            'url' => ['url']
        ];
    }

    // 验证规则
    public function rules()
    {
        return [
            [['avatar'], 'image', 'extensions' => ['png', 'jpg', 'gif', 'jpeg'], 'on' => 'avatar'],
            [['face'], 'image', 'extensions' => ['png', 'jpg', 'gif', 'jpeg'], 'on' => 'face'],
            [['url'], 'image', 'extensions' => ['png', 'jpg', 'gif', 'jpeg'], 'on' => 'url'],
        ];
    }
}
