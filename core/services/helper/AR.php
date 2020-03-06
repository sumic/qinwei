<?php
/**
 * =======================================================
 * @Description :ar helper service
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月20日
 * @version: v1.0.0
 */
namespace core\services\helper;

use core\services\Service;
use Yii;


class AR extends Service
{
    public $limit = 20;
    public $offset = 0;

    /*
     * example filter:
    * [
     * 		'numPerPage' 	=> 20,
     * 		'pageNum'		=> 1,
     * 		'orderBy'	=> ['_id' => SORT_DESC, 'sku' => SORT_ASC ],
     * 		'where'			=> [
                ['>','price',1],
                ['<=','price',10]
     * 			['sku' => 'uk10001'],
     * 		],
     * 	'asArray' => true,
     * ]
     * 查询方面使用的函数，根据传递的参数，进行query
     */
    public function getCollByFilter($query, $filter)
    {
        $select     = isset($filter['select']) ? $filter['select'] : '';
        $asArray    = isset($filter['asArray']) ? $filter['asArray'] : true;
        $limit = isset($filter['limit']) ? $filter['limit'] : $this->numPerPage;
        $offset     = isset($filter['offset']) ? $filter['offset'] : $this->offset;
        $orderBy    = isset($filter['orderBy']) ? $filter['orderBy'] : '';
        $where      = isset($filter['where']) ? $filter['where'] : '';
        if ($asArray) {
            $query->asArray();
        }
        if (is_array($select) && !empty($select)) {
            $query->select($select);
        }
        if ($where) {
            if (is_array($where)) {
                $i = 0;
                foreach ($where as $w) {
                    $i++;
                    if ($i == 1) {
                        $query->where($w);
                    } else {
                        $query->andWhere($w);
                    }
                }
            }
        }
        $query->limit($limit)->offset($offset);
        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        return $query;
    }
    /**
     * @property $model | Object , 数据库model
     * @property $one | Array ， 数据数组，对model进行赋值
     * 通过循环的方式，对$model对象的属性进行赋值。
     * 并保存，保存成功后，返回保存后的model对象
     */
    public function save($model, $data, $serialize = false)
    {
        if (!$model || !$model->load($data,'')) {
            Yii::$service->helper->errors->addByModelErrors($model->getErrors());
            return false;
        }
        if ($model->save()) {
            return $model;
        } else {
            Yii::$service->helper->errors->addByModelErrors($model->getErrors());
            return false;
        }
    }
}
