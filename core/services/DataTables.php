<?php
namespace core\services;
use core\services\Service;
/**
 * =======================================================
 * @Description :datatables service 
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
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
        $request = \Yii::$app->request;
        
        // 接收参数
        $params = $request->post('params');  // 查询参数
        $intStart   = (int)$request->post('iDisplayStart',  0);   // 开始位置
        $intLength  = (int)$request->post('iDisplayLength', 10);  // 查询长度
        
        // 接收处理排序信息
        $sort  = $request->post('sSortDir_0', 'desc'); // 排序类型
        if (isset($params['orderBy']) && !empty($params['orderBy'])) {
            $field = $params['orderBy'];
            unset($params['orderBy']);
        } else {
            $field = null;
        }
        
        $this->arrRequest = [
            'orderDirection' => $sort,    // 排序方式
            'orderField' => $field, // 排序字段
            'offset' => $intStart, // 查询开始位置
            'numPerPage' => $intLength, // 查询数据条数
            'params' => $params, // 查询参数
            'sEcho' => (int)$request->post('sEcho')
        ];
        
        return $this->arrRequest;
    }
    
    public function handleResponse($data, $total, $params = null)
    {
        return [
            'sEcho' => $this->arrRequest['sEcho'],  // 请求次数
            'iTotalRecords' => count($data),        // 当前页条数
            'iTotalDisplayRecords' => (int)$total,  // 数据总条数
            'aaData' => $data,                      // 数据信息
            'params' => $params,                      // 数据信息
        ];
    }
}