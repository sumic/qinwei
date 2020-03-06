<?php
/**
 * =======================================================
 * @Description :roleedit block
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年4月15日
 * @version: v1.0.0
 */
namespace appadmin\modules\RoleItem\block\main;

use Yii;
use appadmin\modules\AppadminBlock;

class Edit extends AppadminBlock{
    
    public $modelName = 'AdminRoleItem';
    
    public function init()
    {
        $this->_modelName = $this->modelName;
        \Yii::$service->page->theme->layoutFile = "main-single.php";
        parent::init();
    }
    
    public function getLastData()
    {
        $this->_one->scenario = 'edit';
        $data['model'] = $this->_one;
        return $data;
    }
    
    public function setService()
    {
        $this->_service = Yii::$service->admin->role;
    }
    
    public function delete()
    {
        $ids = '';
        if ($id = \Yii::$app->request->get('id')) {
            $ids = $id;
        } elseif ($ids = \Yii::$app->request->post('id')) {
            $ids = explode(',', $ids);
        }
        $this->_service->remove($ids);
        $errors = Yii::$service->helper->errors->get();
        if (!$errors) {
            echo  json_encode([
                'statusCode'=>'200',
                'message'=>'删除成功',
            ]);
            exit;
        } else {
            echo  json_encode([
                'statusCode'=>'300',
                'message'=>$errors,
            ]);
            exit;
        }
    }
    
}
