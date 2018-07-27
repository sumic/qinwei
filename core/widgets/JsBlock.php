<?php
/**
 * ==============================================
 * Copy right 2009-2016 http://www.qinweigroup.cn
 * ==============================================
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年3月29日
 * @version: v1.0.0
 * 用于视图文件中JS代码的渲染
 * example:
 * <?php JsBlock::begin() ?>
     <script>
        $(document).ready(function(){alert('ready')});
     </script>
   <?php JsBlock::end() ?>
 *
 */
namespace core\widgets;

use yii\web\View;
use yii\widgets\Block;
use yii\base\InvalidParamException;


class JsBlock extends Block
{
    
    /**
     * @var null
     */
    public $key = null;
    /**
     * @var int
     */
    public $pos = View::POS_END;
    
    /**
     * Ends recording a block.
     * This method stops output buffering and saves the rendering result as a named block in the view.
     */
    public function run()
    {
        $block = ob_get_clean();
        if ($this->renderInPlace) {
            throw new InvalidParamException("Not implemented yet ! ");
        }
        $block = trim($block);
        /*
         $jsBlockPattern  = '|^<script[^>]*>(.+?)</script>$|is';
         if(preg_match($jsBlockPattern,$block)){
         $block =  preg_replace ( $jsBlockPattern , '${1}'  , $block );
         }
         */
        $jsBlockPattern = '|^<script[^>]*>(?P<block_content>.+?)</script>$|is';
        if (preg_match($jsBlockPattern, $block, $matches)) {
            $block = $matches['block_content'];
        }
        
        $this->view->registerJs($block, $this->pos, $this->key);
    }
}
