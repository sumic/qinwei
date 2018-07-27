<?php

namespace appadmin\widgets;

use core\interfaces\block\BlockCache;
use Yii;

class DataTables implements BlockCache
{
    /**
     * @var array 按钮的配置
     */
    public $buttons = [];
    
    /**
     * @var array 表格的配置
     */
    public $table = [];
    
    /**
     * @var string 按钮容器目标
     */
    public $buttonsTemplate = '<p {options}></p>';
    
    /**
     * @var string 表格目标
     */
    public $tableTemplate = '<table {options}></table>';
    
    /**
     * @var array 定义表格默认的配置信息
     */
    private $defaultOptions = [
        'class' => 'table table-striped table-bordered table-hover',
        'id' => 'show-table'
    ];
    
    /**
     * @var array 默认按钮的配置
     */
    private $defaultButtons = [
        'id' => 'me-table-buttons',
    ];
    
    public function init()
    {
        if ($this->table) {
            $this->defaultOptions = array_merge($this->defaultOptions, $this->table);
        }
        // 默认按钮配置覆盖
        if ($this->buttons) {
            $this->defaultButtons = array_merge($this->defaultButtons, $this->buttons);
        }
    }
    
    public function getLastData()
    {
        $this->init();
        $options ['defaultOptions']= $this->defaultOptions;
        $options ['defaultButtons']= $this->defaultButtons;
        return $options;
    }

    public function getCacheKey()
    {
        $cacheKeyName   = 'datatables';
        $appName        = Yii::$service->helper->getAppName();
        $cacheUserId   = Yii::$app->user->id;
        return self::BLOCK_CACHE_PREFIX.'_'.$appName.'_'.$cacheKeyName.'_'.$cacheUserId;
    }
}
