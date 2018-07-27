<?php

namespace appadmin\widgets;

use core\interfaces\block\BlockCache;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Menu implements BlockCache
{

    public $items = [];
    
    /**
     * @var array  定义配置选项
     *
     * - id: 定义ul 的ID
     * - class: 定义ul 的class
     */
    public $options = [];
    
    /**
     * @var string 下拉图标的显示
     */
    public $dropDownCaret = '<b class="arrow fa fa-angle-down"></b>';
    
    /**
     * @var bool 是否需要内容转义
     */
    public $encodeLabels = true;
    
    /**
     * @var string 内容标签的名称
     */
    public $labelName = 'label';
    
    /**
     * @var string 子类数组名称
     */
    public $itemsName = 'items';
    
    /**
     * @return string
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function init()
    {
        $intUserId = Yii::$app->user->id;
        // 初始化定义导航栏信息
        $menus = [];
        // 管理员登录
        if ($intUserId == \Yii::$service->admin->user::SUPER_ADMIN_ID) {
            $menus = \Yii::$service->admin->menu->getAll();
        } else {
            // 其他用户登录成功获取权限
            $permissions = Yii::$app->getAuthManager()->getPermissionsByUser($intUserId);
            if ($permissions) {
                $menus = \Yii::$service->admin->menu->getMenusByPermissions($permissions);
            }
        }
        
        $this->items = \Yii::$service->helper->tree->setParam([
            'data' => $menus,
            'childrenName' => 'child',
            'parentIdName' => 'pid'
        ])->getTreeArray(0);
    }
    
    public function getLastData($params)
    {
        #从模版赋值
        if(isset($params) && !empty($params) && is_array($params)){
            foreach ($params as $k => $v){
                $this->$k = $v;
            }
        }
       
        $this->init();
        $menu = $this->renderItems();
        return [
            'menu' => $menu,
        ];
    }
    
    /**
     * @return string
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function renderItems()
    {
        $items = [];
        foreach ($this->items as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }
            
            $items[] = $this->renderItem($item);
        }
        
        return Html::tag('ul', implode("\n", $items), $this->options);
    }
    
    /**
     * Renders a widget's item.
     * @param string|array $item the item to render.
     * @param bool $isRenderSpan
     * @return string
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function renderItem($item, $isRenderSpan = true)
    {
        if (is_string($item)) {
            return $item;
        }
        
        if (!isset($item[$this->labelName])) {
            throw new InvalidConfigException("The '{$this->labelName}' option is required.");
        }
        
        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item[$this->labelName]) : $item[$this->labelName];
        $icons = ArrayHelper::getValue($item, 'icons');
        $a = $icons ? Html::tag('i', '', ['class' => $icons]) : '';
        $a .= $isRenderSpan ? Html::tag('span', $label, ['class' => 'menu-text']) : $label;
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, $this->itemsName);
        $url = ArrayHelper::getValue($item, 'url', '#');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
        $id = ArrayHelper::getValue($item, 'id');
        if ($id) {
            $linkOptions['data-index'] = '100'.$id;
        }
        if ($url){
            Html::addCssClass($linkOptions, ['addTabs']);
        }
        
        $linkOptions['data-index'] = '100'.$id;
        if (empty($items)) {
            $items = '';
        } else {
            Html::addCssClass($linkOptions, ['dropdown-toggle']);
            if ($this->dropDownCaret !== '') {
                $a .= ' ' . $this->dropDownCaret;
            }
            
            if (is_array($items)) {
                $items = '<b class="arrow"></b>' . $this->renderDropdown($items);
            }
        }
        
        return Html::tag('li', Html::a($a, $url ? $url : '#', $linkOptions) . $items, $options);
    }
    
    /**
     * @param $items
     * @return string
     * @throws InvalidConfigException
     * @throws \Exception
     */
    protected function renderDropdown($items)
    {
        $html = '';
        foreach ($items as $item) {
            $html .= $this->renderItem($item, false);
        }
        
        return html::tag('ul', $html, ['class' => 'submenu']);
    }
    
    public function getCacheKey()
    {
        $cacheKeyName   = 'menu';
        $appName        = Yii::$service->helper->getAppName();
        $cacheUserId   = Yii::$app->user->id;
        return self::BLOCK_CACHE_PREFIX.'_'.$appName.'_'.$cacheKeyName.'_'.$cacheUserId;
    }
}
