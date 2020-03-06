<?php
/**
 * =======================================================
 * @Description : 测试API接口
 * =======================================================
 * @copyright Copyright (c) 2018 勤为科技有限公司
 * @link: http://www.qinweigroup.cn
 * @author: sumic <sumic515@gmail.com>
 * @date: 2018年7月26日 下午2:29:51
 * @version: v1.0.0
 */
namespace appapi\modules\V1\controllers;

use appapi\modules\AppapiController;
use Yii;

class TestapiController extends AppapiController
{

    /**
     * membersync Api：同步学员
     */
    public function actionMembersync()
    {
        $userNm = Yii::$app->request->get('userNm');
        $mobileNum = Yii::$app->request->get('mobileNum');
        $etpsId = Yii::$app->request->get('etpsId');
        $type = Yii::$app->request->get('type');
        
        $operUserStaff = \Yii::$app->request->get('operUserStaffNo');
        $channelId = \Yii::$app->request->get('channelId');
        
        if(!$operUserStaff || !$channelId){
            return [
                'code'    => 400,
                'message' => 'error ,the operuserstaff and channelid must be set',
                'data'    => [],
            ];
        }
        
        return [
            'code'    => 0,
            'message' => 'member sync success',
            'data'    => array('userId'=> rand(1,99)),
        ];
    }
    /**
     * Get category Api：得到Category的api
     */
    public function actionCategory(){
        
        $clfcId = Yii::$app->request->get('clfcId');
        
        $data['0'] = ['clfcId'=>'0','clfcNm'=>'初级课程'];
        $data['1'] = ['clfcId'=>'1','clfcNm'=>'中级课程'];
        $data['2'] = ['clfcId'=>'2','clfcNm'=>'高级课程'];
        $data['3'] = ['clfcId'=>'3','clfcNm'=>'专家级课程'];
        
        $operUserStaff = \Yii::$app->request->get('operUserStaffNo');
        $channelId = \Yii::$app->request->get('channelId');
        
        if(!$operUserStaff || !$channelId){
            return [
                'code'    => 400,
                'message' => 'error ,the operuserstaff and channelid must be set',
                'data'    => [],
            ];
        }
        
        if ($clfcId) {
            return [
                'code'    => 0,
                'message' => 'fetch category success',
                'data'    => $data[$clfcId],
            ];
        } else {
            return [
                'code'    => 400,
                'message' => 'fetch category fail ,the clfcId must be set',
                'data'    => [],
            ];
        }
    }
    
    /**
     * member lesson API
     */
    public function actionMemberlesson()
    {
        //授课方式
        $teachModeCd = \Yii::$app->request->get('teachModeCd');
        //是否指派
        $asgnmtFlag = \Yii::$app->request->get('asgnmtFlag');
        //学习进度
        $studyPrgssVal = \Yii::$app->request->get('studyPrgssVal');
        //课程名称
        $courseNm = \Yii::$app->request->get('courseNm');
        
        $operUserStaff = \Yii::$app->request->get('operUserStaffNo');
        $channelId = \Yii::$app->request->get('channelId');
        
        if(!$operUserStaff || !$channelId){
            return [
                'code'    => 400,
                'message' => 'error ,the operuserstaff and channelid must be set',
                'data'    => [],
            ];
        }
        
        $data['courseNm'] = '初级课程';
        $data['courseId'] = '22';
        $data['uploadUrl'] = 'http://www.qinwei.com';
        $data['courseFrtcovrPicAddr'] = 'basic.jpg';
        $data['teachModeCd'] = '3';
        $data['signupModeCd'] = '1';
        $data['studyPrgssVal'] = '2';
        $data['orgNm'] = '贵阳移动';
        $data['courseStudyId'] = '8';
        
        return [
            'code'    => 0,
            'message' => 'fetch member lessons success',
            'data'    => $data,
        ];
    }
   
    /**
     * lesson info Api
     */
    public function actionLessons()
    {
        //课程id
        $clfcId = \Yii::$app->request->get('clfcId');
        
        $operUserStaff = \Yii::$app->request->get('operUserStaffNo');
        $channelId = \Yii::$app->request->get('channelId');
        
        if(!$operUserStaff || !$channelId){
            return [
                'code'    => 400,
                'message' => 'error ,the operuserstaff and channelid must be set',
                'data'    => [],
            ];
        }
        
        $data['courseNm'] = '中级课程';
        $data['courseId'] = '23';
        $data['uploadUrl'] = 'http://www.qinwei.com';
        $data['courseFrtcovrPicAddr'] = 'middle.jpg';
        $data['teachModeCd'] = '3';
        $data['statVal'] = '2568';
        $data['orgNm'] = '贵阳移动';
        $data['studyPrgssVal'] = '1';
        
        return [
            'code'    => 0,
            'message' => 'fetch lessons success',
            'data'    => $data,
        ];
    }
    
    public function actionExam()
    {
        //考试状态
        $examStatus = \Yii::$app->request->get('examStatus');
        //考试名称
        $qnrNm = \Yii::$app->request->get('qnrNm');
        //当前页
        $pageNum = \Yii::$app->request->get('pageNum');
        //每页显示数量
        $pageSize = \Yii::$app->request->get('pageSize');
        
        if(!$pageNum || !$pageSize){
            return [
                'code'    => 400,
                'message' => 'error ,the pagenum and pagesize must be set',
                'data'    => [],
            ];
        }
        
        $operUserStaff = \Yii::$app->request->get('operUserStaffNo');
        $channelId = \Yii::$app->request->get('channelId');
        
        if(!$operUserStaff || !$channelId){
            return [
                'code'    => 400,
                'message' => 'error ,the operuserstaff and channelid must be set',
                'data'    => [],
            ];
        }
        
        $data['qnrNm'] = '2018移动客服中级课程考试第三期';
        $data['rltQnrId'] = '165';
        $data['surplusTimes'] = '-1';
        $data['reason'] = '';
        $data['courseName'] = '中级课程';
        $data['qnrTotalScore'] = '68';
        $data['passingScore'] = '80';
        $data['correctStsCd'] = '3';
        $data['lastAnsTime'] = time();
        
        return [
            'code'    => 0,
            'message' => 'fetch exam success',
            'data'    => $data,
        ];
    }
    
    public function actionStatis()
    {
        //用户名称
        $staffName = \Yii::$app->request->get('staffName');
        //当前页
        $pageNum = \Yii::$app->request->get('pageNum');
        //每页显示数量
        $pageSize = \Yii::$app->request->get('pageSize');
        
        if(!$pageNum || !$pageSize){
            return [
                'code'    => 400,
                'message' => 'error ,the pagenum and pagesize must be set',
                'data'    => [],
            ];
        }
        $data['userId'] = '16';
        $data['staffNo'] = '188';
        $data['staffName'] = '王永成';
        $data['courseNm'] = '2018移动客服中级课程';
        $data['courseId'] = '22';
        $data['courseStatId'] = '11';
        $data['statVal'] = '1980';
        $data['courseStudyId'] = '76';
        $data['studyTmlenMinCnt'] = '35';
        $data['studyPrgssVal'] = '1';
        $data['scores'] = '68';
        $data['lastAnsTime'] = time();
        
        return [
            'code'    => 0,
            'message' => 'fetch exam success',
            'data'    => $data,
        ];
    }
}
