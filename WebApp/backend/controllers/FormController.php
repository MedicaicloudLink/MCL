<?php

namespace backend\controllers;

use Yii;
use app\models\FForm;
class FormController extends BaseController
{
    /**
     * 创建表单
     * @param userid
     * @param projectid
     * @param sourcedata
     * @param status
     * @return string
     */
    public function actionAddform(){
        self::checkParamIsEmpty(
            [
                'userid'     => '用户id不能为空',
                'projectid'  => '项目id不能为空',
                'sourcedata' => '表单内容不能为空',
                'status'     => '状态不能为空'
            ]
        );
        $data['userid']      = Yii::$app->getRequest()->getBodyParam('userid');
        $data['projectid']   = Yii::$app->getRequest()->getBodyParam('projectid');
        $data['sourcedata']  = Yii::$app->getRequest()->getBodyParam('sourcedata');
        $data['status']      = Yii::$app->getRequest()->getBodyParam('status');
        // 权限身份验证
        $this   ->checkAuth($data['userid']);
        $result = FForm::addForm($data);
        if($result){
            return $this->Rsuccess('创建成功');
        }else{
            return $this->Error('创建失败');
        }
    }
    /**
     * 表单详情
     * @param userid
     * @param projectid
     * @return string
     */
    public function actionForminfo(){
        self::checkParamIsEmpty(
            [
                'userid'     => '用户id不能为空',
                'projectid'  => '项目id不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = FForm::lastInfo($projectid);
        return $this->Rsuccess('表单详情：',$result);
    }
    /**
     * 项目发布的表单
     * @param userid
     * @param projectid
     * @return string
     */
    public function actionPublishform(){
        self::checkParamIsEmpty(
            [
                'userid'     => '用户id不能为空',
                'projectid'  => '项目id不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = FForm::lastPublishInfo($projectid);
        return $this->Rsuccess('发布的表单：',$result);
    }
    /**
     * 表单历史版本
     * @param userid
     * @param projectid
     * @param string
     */
    public function actionFormcommits(){
        self::checkParamIsEmpty(
            [
                'userid'     => '用户id不能为空',
                'projectid'  => '项目id不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = FForm::formCommits($projectid);
        return $this->Rsuccess('表单历史版本：',$result);
    }
}
