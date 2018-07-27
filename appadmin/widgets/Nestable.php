<?php

namespace appadmin\widgets;

use yii\helpers\Html;
use core\interfaces\block\BlockCache;
use yii\helpers\ArrayHelper;
use Yii;

class Nestable implements BlockCache
{
    /**
     * @var array 定义数据来源
     */
    public $items = [];

    /**
     * @var array  定义配置选项
     * - class: 定义ul 的class
     */
    public $options = [
        'class' => 'dd-list'
    ];

    /**
     * @var string 子类数组名称
     */
    public $itemsName = 'items';

    /**
     * @var string 定义名称字段
     */
    public $labelName = 'name';

    public function getLastData($params)
    {
        #从模版赋值
        if(isset($params) && !empty($params) && is_array($params)){
            foreach ($params as $k => $v){
                if($v)$this->$k = $v;
            }
        }
        return [
            'nestable' => $this->renderItems($this->items),
        ];
    }

    /**
     * @param array $items 数据信息
     * @return string
     */
    private function renderItems($items)
    {
        $arrItems = [];
        foreach ($items as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }

            $arrItems[] = $this->renderItem($item);
        }

        return Html::tag('ol', implode("\n", $arrItems), $this->options);
    }

    /**
     * Renders a widget's item.
     * @param string|array $item the item to render.
     * @return string
     */
    private function renderItem($item)
    {
        $html = '<div class="dd-handle">' . ArrayHelper::getValue($item, $this->labelName) . '</div>';
        $items = ArrayHelper::getValue($item, $this->itemsName);
        $options = ArrayHelper::getValue($item, 'options', []);
        Html::addCssClass($options, 'dd-item');
        if (!empty($items)) {
            Html::addCssClass($options, 'item-red');
            $html .= $this->renderItems($items);
        }

        return Html::tag('li', $html, $options);
    }
    
    public function getCacheKey()
    {
        $cacheKeyName   = 'nestable';
        $appName        = Yii::$service->helper->getAppName();
        $cacheUserId   = Yii::$app->user->id;
        return self::BLOCK_CACHE_PREFIX.'_'.$appName.'_'.$cacheKeyName.'_'.$cacheUserId;
    }
}