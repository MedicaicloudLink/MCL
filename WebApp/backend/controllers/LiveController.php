<?php

namespace backend\controllers;

use Yii;
use app\models\PProjectLive;
use app\models\QCompany;
class LiveController extends BaseController
{
    /**
     * 创建直播
     * @param userid
     * @param projectid
     * @param title
     * @param content
     * @return string
     */
    public function actionAddlive(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'      => '用户id不能为空',
                'projectid'   => '项目id不能为空',
                'title'       => '标题不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $content    = Yii::$app->getRequest()->getBodyParam('content');
        $title      = Yii::$app->getRequest()->getBodyParam('title');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result     = PProjectLive::addLive($userid,$projectid,$title,$content,$company['id']);
        if($result){
            return $this->Rsuccess('id',$result);
        }else{
            return $this->Error('创建失败');
        }
    }
    /**
     * 直播信息
     * @param userid
     * @param liveid
     * @return string
     */
    public function actionLiveinfo(){
        self::checkParamIsEmpty(
            [
                'userid'   => '用户id不能为空',
                'liveid'   => '直播id不能为空',
            ]
        );
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        $liveid = Yii::$app->getRequest()->getBodyParam('liveid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = PProjectLive::liveInfo($liveid);
        if($result){
            return $this->Rsuccess('直播信息',$result);
        }else{
            return $this->Error('不存在，结束了','','1404');
        }

    }
    /**
     * 结束直播
     * @param userid
     * @param liveid
     * @return string
     */
    public function actionEndlive(){
        self::checkParamIsEmpty(
            [
                'userid'   => '用户id不能为空',
                'liveid'   => '直播id不能为空',
            ]
        );
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        $liveid = Yii::$app->getRequest()->getBodyParam('liveid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = PProjectLive::endLive($liveid);
        if($result){
            return $this->Rsuccess('结束成功');
        }else{
            return $this->Error('失败了');
        }
    }
    /**
     * 项目中正在直播列表
     * @param userid
     * @param projectid
     * @return string
     */
    public function actionLivelist(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户id不能为空',
                'projectid' => '项目id不能为空',
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = PProjectLive::liveList($projectid);
        return $this->Rsuccess('列表',$result);
    }
    /**
     * 直播状态接收
     *
     */
    public function actionGetstatus(){
        $action = Yii::$app->request->get('action');
        $id     = Yii::$app->request->get('id');
        if($action == 'publish'){
            PProjectLive::startLive($id);#开始录制
        }
    }
    /**
     * 录制回调
     * 结束录制
     */
    public function actionRecordnotify(){
        $params  = Yii::$app->getRequest()->getBodyParams();
        $id      = Yii::$app->getRequest()->getBodyParam('stream');
        $content = json_encode($params);
        PProjectLive::endRecord($content,$id);
    }
    /**
     * 自己的直播状态
     * @param userid
     * @param projectid
     * @return string
     */
    public function actionMylive(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户id不能为空',
                'projectid' => '直播id不能为空',
            ]
        );
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = PProjectLive::myInfo($userid,$projectid);
        return $this->Rsuccess('我的直播',$result);
    }

}
