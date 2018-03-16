<?php

namespace backend\controllers;

use app\models\PProjectUser;
use Yii;
use app\models\QCompany;
use app\models\PProject;
use app\models\NNewsfeed;
use app\models\NNewsfeedComment;
class FeedController extends BaseController
{
    /**
     * 创建feed
     * @param userid
     * @param content
     * @param upload_url
     * @param projectid(选填)
     * @return string
     */
    public function actionAddfeed(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'      => '用户id不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $content    = Yii::$app->getRequest()->getBodyParam('content');
        $upload_url = Yii::$app->getRequest()->getBodyParam('upload_url');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        if($content == '' && $upload_url == '[]'){
            return $this->Error('内容和附件必须传一个');
        }
        // 权限身份验证
        $this   ->checkAuth($userid);
        if($projectid != ''){
            #项目是否存在
            $projectinfo = PProject::projectByid($projectid);
            if(empty($projectinfo)){
                return $this->Error('项目不存在');
            }
            //用户是否为项目管理员
            $isadmin = PProjectUser::userInfo($userid,$projectid);
            if(empty($isadmin)){
                return $this->Error('发布失败,不属于项目成员','','5002');
            }
            if($isadmin['permission'] == 2){
                #项目是否允许发消息
                if($projectinfo['addfeed_access'] == 1){
                    return $this->Error('发布失败,仅允许管理员发布','','5001');
                }
            }
        }
        $result = NNewsfeed::addFeed($userid,$content,$upload_url,$company['id'],$projectid);
        if($result){
            return $this->Rsuccess('发布成功');
        }else{
            return $this->Error('发布失败');
        }
    }
    /**
     * 添加评论
     * @param userid
     * @param content
     * @param feedid
     * @return string
     */
    public function actionAddcomment(){
        self::checkParamIsEmpty(
            [
                'userid'     => '用户id不能为空',
                'feedid'     => 'feedid不能为空',
                'content'    => '内容不能为空'
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        $feedid  = Yii::$app->getRequest()->getBodyParam('feedid');
        $content = Yii::$app->getRequest()->getBodyParam('content');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = NNewsfeedComment::addComment($userid,$feedid,$content);
        if($result){
            return $this->Rsuccess('评论成功');
        }else{
            return $this->Error('评论失败');
        }
    }
    /**
     * 删除feed
     * @param userid
     * @param feedid
     * @param projectid(选填)
     * @return string
     */
    public function actionDelfeed(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'     => '用户id不能为空',
                'feedid'     => 'feedid不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $feedid    = Yii::$app->getRequest()->getBodyParam('feedid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        // 权限身份验证
        $this->checkAuth($userid);
        $result    = NNewsfeed::delFeed($userid,$feedid,$company['id'],$projectid);
        if($result){
            return $this->Rsuccess('删除成功');
        }else{
            return $this->Error('删除失败，权限不够');
        }
    }
    /**
     * 消息列表
     * @param userid
     * @param sort(before,after)
     * @param count(显示数量)
     * @param since_id(基准,feedid)
     * @param projectid(选填)
     * @return string
     */
    public function actionFeedlist(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'   => '用户id不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $sort      = Yii::$app->getRequest()->getBodyParam('sort');
        $count     = Yii::$app->getRequest()->getBodyParam('count');
        $since_id  = Yii::$app->getRequest()->getBodyParam('since_id');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        if($since_id == ''){
            $count = 15;
            $sort  = 'before';
        }else{
            if($count == "" ){
                $count = 15;
            }
            if($sort == ""){
                $sort = 'after';
            }
            if(!in_array($sort,['before','after']) || $count>50){
                return $this->Error('参数有误');
            }
            $since_id = NNewsfeed::feedInfo($since_id)['id'];
        }
        $this->checkAuth($userid);
        $result    = NNewsfeed::feedList($userid,$sort,$count,$since_id,$company['id'],$projectid);
        if($result){
            return $this->Rsuccess('消息列表：',$result);
        }else{
            return $this->Error('项目不在进行中');
        }

    }
    /**
     * 评论列表
     * @param userid
     * @param feedid
     * @param sort(before,after)
     * @param count(显示数量)
     * @param since_id(基准,commentid)
     */
    public function actionCommentlist(){
        self::checkParamIsEmpty(
            [
                'userid'   => '用户id不能为空',
                'feedid'   => 'feedid不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $feedid    = Yii::$app->getRequest()->getBodyParam('feedid');
        $sort      = Yii::$app->getRequest()->getBodyParam('sort');
        $count     = Yii::$app->getRequest()->getBodyParam('count');
        $since_id  = Yii::$app->getRequest()->getBodyParam('since_id');
        $this->checkAuth($userid);
        if($since_id == ''){
            $count = 15;
            $sort  = 'before';
        }else{
            if($count == "" ){
                $count = 15;
            }
            if($sort == ""){
                $sort = 'after';
            }
            if(!in_array($sort,['before','after']) || $count>50){
                return $this->Error('参数有误');
            }
            $since_id = NNewsfeedComment::commentInfo($since_id)['id'];
        }
        $result = NNewsfeedComment::commentList($userid,$sort,$count,$since_id,$feedid);
        return $this->Rsuccess('评论列表：',$result);
    }
    /**
     * 关闭/开启评论
     * @param userid
     * @param feedid
     * @param status(1：允许；2：关闭)
     * @param projectid(选填)
     * @return string
     */
    public function actionDofeedcomment(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'     => '用户id不能为空',
                'feedid'     => 'feedid不能为空',
                'status'     => '操作状态不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $feedid    = Yii::$app->getRequest()->getBodyParam('feedid');
        $status    = Yii::$app->getRequest()->getBodyParam('status');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        if($status == 1 || $status == 2){
            // 权限身份验证
            $this->checkAuth($userid);
            $result    = NNewsfeed::doFeedComment($userid,$feedid,$company['id'],$status,$projectid);
            if($result){
                return $this->Rsuccess('设置成功');
            }else{
                return $this->Error('设置失败，权限不够');
            }
        }else{
            return $this->Error('参数有误');
        }
    }
    /**
     * 个人主页-动态消息日志
     * @param userid
     * @param touserid
     * @param sort(before,after)
     * @param count(显示数量)
     * @param since_id(基准,feedid)
     * @return string
     */
    public function actionUserfeedlist(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'     => '用户id不能为空',
                'touserid'   => '被访问者不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid  = Yii::$app->getRequest()->getBodyParam('touserid');
        $sort      = Yii::$app->getRequest()->getBodyParam('sort');
        $count     = Yii::$app->getRequest()->getBodyParam('count');
        $since_id  = Yii::$app->getRequest()->getBodyParam('since_id');
        if($since_id == ''){
            $count = 15;
            $sort  = 'before';
        }else{
            if($count == "" ){
                $count = 15;
            }
            if($sort == ""){
                $sort = 'after';
            }
            if(!in_array($sort,['before','after']) || $count>50){
                return $this->Error('参数有误');
            }
            $since_id = NNewsfeed::feedInfo($since_id)['id'];
        }
        $this->checkAuth($userid);
        $result    = NNewsfeed::userFeedlist($userid,$touserid,$sort,$count,$since_id,$company['id']);
        return $this->Rsuccess('消息日志：',$result);
    }
    /**
     * feed详情
     * @param userid
     * @param feedid
     * @return string
     */
    public function actionFeedinfo(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'     => '用户id不能为空',
                'feedid'     => '被访问者不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $feedid    = Yii::$app->getRequest()->getBodyParam('feedid');
        $this->checkAuth($userid);
        $result    = NNewsfeed::feedById($feedid,$company['id']);
        return $this->Rsuccess('feed详情',$result);
    }
}
