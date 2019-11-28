<?php
/**
 * =======================================================
 * @Description :cms view block
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
namespace appadmin\modules\Cms\block\main;

use Yii;
use yii\helpers\ArrayHelper;
class View{
    
    public function getLastData()
    {
        $id = (int) \Yii::$app->request->get('id');
        $data['model'] = $id ? \Yii::$service->cms->article->getbyprimarykey($id) : '';
        if ($id && !empty($data['model'])) {
            $data['model']->cid        = \Yii::$service->cms->category->getbyprimarykey($data['model']->cid)->name;
            $data['model']->created_id = \Yii::$service->admin->user->getbyprimarykey($data['model']->created_id)->username;
            $data['model']->updated_id = \Yii::$service->admin->user->getbyprimarykey($data['model']->updated_id)->username;
            $data['model']->tag        = \Yii::$service->cms->tags->gettagsbyarticle($data['model']->id, true);
            $data['model']->content    = $data['model']->articleContent->content;
            $data['model']->tag        = implode(',', ArrayHelper::getColumn($data['model']->articleTags, 'name'));
        } else {
            Yii::$service->page->message->adderror('没有文章数据或文章ID不正确');
            Yii::$service->url->redirect(['cms/main/index']);
            return;
        }

        #用户列表
        $data['users']  = ArrayHelper::map(\Yii::$service->admin->user->getActiveuser(), 'id', 'username');
        #状态
        $params['status']  = [0 => '草稿', 1 => '发布'];
        #评论
        $params['commit'] = [0 => '否', 1 => '是'];
        #可见
        $params['visable'] = [1 => '公开', 2 => '评论可见', 3 => '加密文章', 4 => '登录可见'];
        #查询父级分类信息
        $data['parents'] = \Yii::$service->cms->category->getAll();

        return $data;
    }
    
}