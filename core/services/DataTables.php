<?php
namespace core\services;
use core\services\Service;
/**
 * =======================================================
 * @Description :datatables service 
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月28日
 * @version: v1.0.0
 */


class DataTables extends Service
{
    /**
     * 请求参数信息
     * @var array
     */
    public $arrRequest = [];
    
    public function getRequest()
    {
        // 接收参数
        $request          = \Yii::$app->request;
        $this->arrRequest = [
            'draw'    => (int)$request->get('draw'),         // 请求次数
            'orderBy' => trim($request->get('orderBy', '')), // 排序条件
            'offset'  => intval($request->get('offset', 0)), // 查询开始位置
            'limit'   => intval($request->get('limit', 10)), // 查询数据条数
            'filters' => $request->get('filters'),           // 查询过滤条件
        ];

        return $this->arrRequest;
    }
    
    public function handleResponse($data, $total, $params = null)
    {
        return [
            'draw'            => $this->arrRequest['draw'], // 请求次数
            'recordsTotal'    => (int)$total,                    // 数据总条数
            'recordsFiltered' => (int)$total,                    // 数据总条数
            'data'            => $data,                     // 数据信息
            'params'          => $params
        ];
    }
}