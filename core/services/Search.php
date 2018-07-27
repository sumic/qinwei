<?php
/**
 * =======================================================
 * @Description :search service
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月17日
 * @version: v1.0.0
 */

namespace core\services;

use Yii;

class Search extends Service
{
    //搜索使用的模型
    protected $_searchModel;
    
    public function actionGetColl($filler,$model)
    {
        $query = $model->find();
        $query = \Yii::$service->helper->ar->getCollByFilter($query, $filler);
        $coll = $query->all();
        if (YII_DEBUG) \Yii::$service->helper->json->arrJson['other'] = $query->createCommand()->getRawSql();
        return [
            'coll' => $coll,
            'total'=> $query->limit(null)->offset(null)->count(),
        ];
    }
    
    
}
