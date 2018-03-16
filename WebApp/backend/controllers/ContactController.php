<?php

namespace backend\controllers;

use app\models\ContactPerson;
use app\models\QCompanyUser;
use Yii;
use app\models\ContactTag;
use app\models\ContactPersonLog;
class ContactController extends BaseController
{
    /**
     * 创建/编辑标签
     * @param userid
     * @param tagname
     * @param tagid(编辑是才需要)
     * @return string
     */
    public function actionEdittag(){
        self::checkParamIsEmpty(
            [
                'userid'   => '用户id不能为空',
                'tagname'  => '标签名不能为空',
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        $tagname = Yii::$app->getRequest()->getBodyParam('tagname');
        $tagid   = Yii::$app->getRequest()->getBodyParam('tagid');
        // 权限身份验证
        $this    ->checkAuth($userid);
        $result  = ContactTag::addTag($userid,$tagname,$tagid);
        if($result){
            return $this->Rsuccess('创建成功');
        }else{
            return $this->Error('创建失败');
        }
    }
    /**
     * 标签列表
     * @param userid
     * @return string
     */
    public function actionTaglist(){
        self::checkParamIsEmpty(
            [
                'userid'   => '用户id不能为空'
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        // 权限身份验证
        $this    ->checkAuth($userid);
        $result  = ContactTag::tagList($userid);
        return $this->Rsuccess('列表',$result);
    }
    /**
     * 申请加联系人
     * @param userid
     * @param touserid
     * @param tagid(可为空)
     * @return string
     */
    public function actionAddcontact(){
        self::checkParamIsEmpty(
            [
                'userid'   => '用户id不能为空',
                'touserid' => '联系人不能为空',
            ]
        );
        $userid   = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid = Yii::$app->getRequest()->getBodyParam('touserid');
        $tagid    = Yii::$app->getRequest()->getBodyParam('tagid');
        // 权限身份验证
        $this     ->checkAuth($userid);
        //是否已经是联系人
        $contact  = ContactPerson::contactInfo($userid,$touserid);
        if(!empty($contact)){
            return $this->Error('已经是联系人','','1401');
        }
        $result   = ContactPersonLog::addLog($userid,$touserid,$tagid);
        if($result){
            return $this->Rsuccess('创建成功');
        }else{
            return $this->Error('创建失败');
        }
    }
    /**
     * 同意/拒绝申请
     * @param userid
     * @param touserid
     * @param status(2同意，3拒绝)
     * @return string
     */
    public function actionDoapply(){
        self::checkParamIsEmpty(
            [
                'userid'   => '用户id不能为空',
                'touserid' => '对方不能为空',
                'status'   => '处理状态不能为空'
            ]
        );
        $userid   = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid = Yii::$app->getRequest()->getBodyParam('touserid');
        $status   = Yii::$app->getRequest()->getBodyParam('status');
        if(!in_array($status,[2,3])){
            return $this->Error('参数有误');
        }
        // 权限身份验证
        $this     ->checkAuth($userid);
        $result   = ContactPersonLog::doApply($userid,$touserid,$status);
        if($result){
            return $this->Rsuccess('成功');
        }else{
            return $this->Error('失败');
        }
    }
    /**
     * 联系人列表
     * @param userid
     * @param page
     * @param type(all=全部；always=常用；tagid)
     * @return string
     */
    public function actionContactlist(){
        self::checkParamIsEmpty(
            [
                'userid' => '用户id不能为空',
                'page'   => '当前页不能为空'
            ]
        );
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        $page   = Yii::$app->getRequest()->getBodyParam('page');
        $type   = Yii::$app->getRequest()->getBodyParam('type');
        if($type == ''){
            $type = 'all';
        }
        // 权限身份验证
        $this     ->checkAuth($userid);
        $result = ContactPerson::contactList($userid,$page,$type);
        return $this->Rsuccess('列表',$result);
    }
    /**
     * 联系人每个标签下的人数
     * @param userid
     * @return string
     */
    public function actionTagperson(){
        self::checkParamIsEmpty(
            [
                'userid' => '用户id不能为空'
            ]
        );
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        // 权限身份验证
        $this     ->checkAuth($userid);
        $result = ContactTag::tagPersonNum($userid);
        return $this->Rsuccess('各个标签下联系人人数',$result);
    }
    /**
     * 移除联系人
     * @param userid
     * @param touserid
     * @return string
     */
    public function actionDelcontact(){
        self::checkParamIsEmpty(
            [
                'userid'   => '用户id不能为空',
                'touserid' => '对方id不能为空'
            ]
        );
        $userid   = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid = Yii::$app->getRequest()->getBodyParam('touserid');
        // 权限身份验证
        $this     ->checkAuth($userid);
        ContactPerson::delContact($userid,$touserid);
        return $this->Rsuccess('成功');
    }
    /**
     * 内部通讯录
     * @param userid
     * @param page
     * @return string
     */
    public function actionCompanycontact(){
        self::checkParamIsEmpty(
            [
                'userid' => '用户id不能为空',
                'page'   => '当前页不能为空'
            ]
        );
        $userid   = Yii::$app->getRequest()->getBodyParam('userid');
        $page     = Yii::$app->getRequest()->getBodyParam('page');
        // 权限身份验证
        $this     ->checkAuth($userid);
        $result   = QCompanyUser::allContact($userid,$page);
        return $this->Rsuccess('内部通讯录',$result);
    }
    /**
     * 导出全部我的联系人
     * @param userid
     * @return string
     */
    public function actionExportcontact(){
        $userid = Yii::$app->request->get('userid');
        ContactPerson::exportContact($userid);
        return $this->Rsuccess('导出成功');
    }
    /**
     * 把联系人放到不一样的标签下
     * @param userid
     * @param touserid
     * @param tagid
     * @return string
     */
    public function actionEdittagperson(){
        self::checkParamIsEmpty(
            [
                'userid'   => '用户id不能为空',
                'touserid' => '对方id不能为空'
            ]
        );
        $userid   = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid = Yii::$app->getRequest()->getBodyParam('touserid');
        $tagid    = Yii::$app->getRequest()->getBodyParam('tagid');
        // 权限身份验证
        $this     ->checkAuth($userid);
        ContactPerson::editTagPerson($userid,$touserid,$tagid);
        return $this->Rsuccess('成功');
    }
    /**
     * 删除标签
     * @param userid
     * @param tagid
     * @return string
     */
    public function actionDeltag(){
        self::checkParamIsEmpty(
            [
                'userid' => '用户id不能为空',
                'tagid'  => '标签id不能为空'
            ]
        );
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        $tagid  = Yii::$app->getRequest()->getBodyParam('tagid');
        // 权限身份验证
        $this     ->checkAuth($userid);
        ContactTag::delTag($userid,$tagid);
        return $this->Rsuccess('删除成功');
    }
    /**
     * 申请加用户为联系人的信息
     * @param userid
     * @return string
     */
    public function actionApplymyinfo(){
        self::checkParamIsEmpty(
            [
                'userid' => '用户id不能为空'
            ]
        );
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        // 权限身份验证
        $this     ->checkAuth($userid);
        $result = ContactPersonLog::applyMyInfo($userid);
        return $this->Rsuccess('别人申请加我的信息',$result);
    }


}
