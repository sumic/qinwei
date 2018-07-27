<?php
/**
 * =======================================================
 * @Description :cms 接口
 * =======================================================
 * @copyright Copyright (c) 2018 成都勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年5月18日
 * @version: v1.0.0
 */

namespace core\services\cms\article;

interface ArticleInterface
{
    public function getByPrimaryKey($primaryKey);
    
    public function save($one, $originUrlKey);
    
    public function remove($ids);
}
