<?php
/**
 * =======================================================
 * @Description :Interface BlockCache
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月17日
 * @version: v1.0.0
 */

namespace core\interfaces\block;

interface BlockCache
{
    const BLOCK_CACHE_PREFIX = 'block_cache';

    public function getCacheKey();
}
