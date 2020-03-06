<?php

/**
 * =======================================================
 * @Description :cms article mysqldb model
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月18日
 * @version: v1.0.0
 */

namespace core\models\mysqldb\cms;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use core\behaviors\UpdateBehavior;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property int $id 文章自增id
 * @property int $cid 文章分类id
 * @property int $type 类型.0文章,1单页
 * @property string $title 文章标题
 * @property string $sub_title 用户名
 * @property string $summary 文章概要
 * @property string $thumb 缩略图
 * @property string $seo_title seo标题
 * @property string $seo_keywords seo关键字
 * @property string $seo_description seo描述
 * @property int $status 状态.0草稿,1发布
 * @property int $sort 排序
 * @property int $author_id 发布文章管理员id
 * @property string $author_name 发布文章管理员用户名
 * @property int $scan_count 浏览次数
 * @property int $comment_count 浏览次数
 * @property int $can_comment 是否可评论.0否,1是
 * @property int $visibility 文章可见性.1.公开,2.评论可见,3.加密文章,4.登陆可见
 * @property string $password 文章明文密码
 * @property int $flag_headline 头条.0否,1.是
 * @property int $flag_recommend 推荐.0否,1.是
 * @property int $flag_slide_show 幻灯.0否,1.是
 * @property int $flag_special_recommend 特别推荐.0否,1.是
 * @property int $flag_roll 滚动.0否,1.是
 * @property int $flag_bold 加粗.0否,1.是
 * @property int $flag_picture 图片.0否,1.是
 * @property int $created_at 创建时间
 * @property int $updated_at 最后修改时间
 */
class Article extends ActiveRecord
{
    /**
     * @var string
     */
    const ARTICLE = 1;
    const SINGLE_PAGE = 2;
    /**
     * @var string
     */
    public $tag = '';

    /**
     * @var null|string
     */
    public $content = null;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            UpdateBehavior::className()
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'type', 'status', 'sort', 'can_comment', 'visibility'], 'integer'],
            [['cid', 'sort'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['title', 'status', 'cid'], 'required'],
            [['can_comment', 'visibility'], 'default', 'value' => 1],
            [['sort'], 'default', 'value' => 0],
            [['content'], 'string'],
            [['created_at', 'updated_at', 'created_id', 'updated_id'], 'safe'],
            [
                [
                    'title',
                    'sub_title',
                    'summary',
                    'thumb',
                    'seo_title',
                    'seo_keywords',
                    'seo_description',
                    'author_name',
                    'tag'
                ],
                'string',
                'max' => 255
            ],
            [
                [
                    'flag_headline',
                    'flag_recommend',
                    'flag_slide_show',
                    'flag_special_recommend',
                    'flag_roll',
                    'flag_bold',
                    'flag_picture',
                    'status',
                    'can_comment'
                ],
                'in',
                'range' => [0, 1]
            ],
            [['visibility'], 'in', 'range' => [1, 2, 3, 4]],
            [['type'], 'default', 'value' => self::ARTICLE, 'on' => 'article'],
            [['type'], 'default', 'value' => self::SINGLE_PAGE, 'on' => 'page'],
            [['password'], 'string', 'max' => 20],
        ];
    }

    public function scenarios()
    {
        return [
            'default' => [
                'cid',
                'type',
                'title',
                'sub_title',
                'summary',
                'content',
                'thumb',
                'seo_title',
                'seo_keywords',
                'seo_description',
                'status',
                'sort',
                'author_name',
                'created_at',
                'created_id',
                'updated_at',
                'updated_id',
                'scan_count',
                'comment_count',
                'can_comment',
                'visibility',
                'tag',
                'flag_headline',
                'flag_recommend',
                'flag_slide_show',
                'flag_special_recommend',
                'flag_roll',
                'flag_bold',
                'flag_picture',
                'password'
            ],
            'page' => [
                'type',
                'title',
                'sub_title',
                'summary',
                'seo_title',
                'content',
                'seo_keywords',
                'seo_description',
                'status',
                'can_comment',
                'visibility',
                'tag',
                'sort',
                'created_at',
                'created_id',
                'updated_at',
                'updated_id',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '文章自增id',
            'cid' => '文章分类',
            'type' => '类型.0文章,1单页',
            'title' => '文章标题',
            'sub_title' => '副标题',
            'summary' => '文章概要',
            'thumb' => '缩略图',
            'seo_title' => 'SEO标题',
            'seo_keywords' => 'SEO关键字',
            'seo_description' => 'SEO描述',
            'content' => '内容正文',
            'status' => '状态',
            'sort' => '排序',
            'author_name' => '来源/作者',
            'scan_count' => '浏览数',
            'comment_count' => '评论数',
            'can_comment' => '是否可评论',
            'visibility' => '可见性',
            'password' => '文章密码',
            'flag_headline' => '头条',
            'flag_recommend' => '推荐',
            'flag_slide_show' => '幻灯',
            'flag_special_recommend' => '特荐',
            'flag_roll' => '滚动',
            'flag_bold' => '加粗',
            'flag_picture' => '图片',
            'created_at' => '创建时间',
            'created_id' => '创建者',
            'updated_at' => '修改时间',
            'updated_id' => '修改者',
        ];
    }

    //关联文章内容
    public function getArticleContent()
    {
        return $this->hasOne(ArticleContent::className(), ['aid' => 'id'])->select('content');
    }
    //关联文章TAGS
    public function getArticleTags()
    {
        return $this->hasMany(ArticleTags::className(), ['aid' => 'id'])->select('name')->asArray(true);
    }
}
