<?php
/**
 * =======================================================
 * @Description :cms article content mysqldb model
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月18日
 * @version: v1.0.0
 */

namespace core\models\mysqldb\cms;

use yii\db\ActiveRecord;
use yii;

/**
 * This is the model class for table "{{%article_content}}".
 *
 * @property int $id 自增id
 * @property int $aid 文章id
 * @property string $content 文章详细内容
 */
class ArticleContent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article_content}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aid'], 'integer'],
            [['content'], 'required'],
            [['content'], 'string'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'aid' => '文章id',
            'content' => '文章详细内容',
        ];
    }
    
    /**
     * @保存前替换网址
     */
    public function beforeSave($insert)
    {
        $this->content = str_replace(yii::$app->params['site-img'], yii::$app->params['site-sign'], $this->content);
        return true;
    }
    
    /**
     * @查找后替换网址
     */
    public function afterFind()
    {
        $this->content = str_replace(yii::$app->params['site-sign'], yii::$app->params['site-img'], $this->content);
    }
}
