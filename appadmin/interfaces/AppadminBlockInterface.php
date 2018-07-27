<?php
/**
 * =======================================================
 * @Description :接口定义
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月28日
 * @version: v1.0.0
 */

namespace appadmin\interfaces;


interface AppadminBlockInterface
{
    #初始化
    public function init();
    #设置模型
    public function setModel();
    #设置查询字段
    public function setSearchFields();
    #设置服务
    public function setService();
    #设置显示类型 datatables jqgrid
    public function setDisplay();
    #设置查询的字段
    public function searchFields();
}
