<?php
/**
 * =======================================================
 * @Description :console控制器基类
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2020年3月3日
 * @version: v1.0.0
 *
 */
?>
<?php
namespace console\modules;

use core\controllers\BaseController;
use Yii;

class ConsoleController extends BaseController
{
    public $blockNamespace;

    /**
     * get current block
     * you can change $this->blockNamespace.
     */
    public function getBlock($blockName = '')
    {
        if (!$blockName) {
            $blockName = $this->action->id;
        }
        if (!$this->blockNamespace) {
            $this->blockNamespace = Yii::$app->controller->module->blockNamespace;
        }
        if (!$this->blockNamespace) {
            throw new \yii\web\HttpException(406, 'blockNamespace is empty , you should config it in module->blockNamespace or controller blockNamespace ');
        }

        $relativeFile = '\\'.$this->blockNamespace;
        $relativeFile .= '\\'.$this->id.'\\'.ucfirst($blockName);
        //查找是否在rewriteMap中存在重写
        $relativeFile = Yii::mapGetName($relativeFile);
        
        return new $relativeFile();
    }
}
