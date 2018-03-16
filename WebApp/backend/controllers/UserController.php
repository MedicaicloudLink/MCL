<?php

namespace backend\controllers;

use app\models\PProject;
use app\models\PProjectUser;
use app\models\QCompanyUser;
use app\models\UFeedback;
use app\models\UUser;
use app\models\UUserAchievement;
use app\models\UUserInfo;
use app\models\QCompany;
use app\models\Commonfun;
use app\models\UploadForm;
use yii\web\UploadedFile;
use Yii;
class UserController extends BaseController
{
    /**
     * 展示用户主页
     */
    public function actionHome(){
        return $this->renderPartial('home');
    }
    /**
     * 用户基本信息
     * @param userid
     * @return string
     */
    public function actionUserinfo(){
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
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = UUserInfo::userInfo($userid);
        if(!empty($result)){
            $companyuser           = QCompanyUser::userPermission($userid,$company['id']);
            //是否为企业管理员
            $result['role']        = $companyuser['permission'];
            //用户是否被批准  1.被批准 2.未处理
            $result['isauthorize'] = $companyuser['isauthorize'];
        }
        $result['companyname'] = $company['name'];
        $result['companyid']   = $company['id'];
        return $this->Rsuccess('用户信息：',$result);
    }
    /**
     * 个人主页-个人成就
     * @param userid(访问者id)
     * @param touserid(被访问者id)
     */
    public function actionUserachievement(){
        self::checkParamIsEmpty(
            [
                'userid'   => '用户id不能为空',
                'touserid' => '被访问者id不能为空'
            ]

        );
        $userid   = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid = Yii::$app->getRequest()->getBodyParam('touserid');
        $this->checkAuth($userid);
        $result   = UUserAchievement::achievementInfo($touserid);
        return $this->Rsuccess('个人成就：',$result);
    }
    /**
     * 编辑/添加个人成就
     * @param userid
     * @param achievement_type
     * @param achievement_value
     * @return string
     */
    public function actionEditachievement(){
        self::checkParamIsEmpty(
            [
                'userid'           => '用户id不能为空',
                'achievement_type' => '修改类型不能为空'
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        $type    = Yii::$app->getRequest()->getBodyParam('achievement_type');
        $value   = Yii::$app->getRequest()->getBodyParam('achievement_value');
        $this->checkAuth($userid);
        $typearr = ['s_qualification','s_honor','s_works','s_organization','s_language','s_education','s_professional'];
        if(!in_array($type,$typearr)){
            return $this->Error('参数有误');
        }
        $result  = UUserAchievement::editAchievement($userid,$type,$value);
        if($result){
            return $this->Rsuccess('编辑成功');
        }else{
            return $this->Error('编辑失败');
        }
    }
    /**
     * 企业内用户列表(全部，管理员)+搜索
     * @param userid
     * @param page
     * @param type(all,admin)
     * @param search
     * @return string
     */
    public function actionCompanyuser(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid' => '用户id不能为空',
                'page'   => '当前页不能为空',
                'type'   => '类型不能为空'
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        $page    = Yii::$app->getRequest()->getBodyParam('page');
        $type    = Yii::$app->getRequest()->getBodyParam('type');
        $search  = Yii::$app->getRequest()->getBodyParam('search');
        if(!in_array($type,['all','admin'])){
            return $this->Error('参数有误');
        }
        $this    ->checkAuth($userid);
        $result  = QCompanyUser::userList($company['id'],$page,$type,$search);
        return $this->Rsuccess('企业内用户列表：',$result);
    }
    /**
     * 删除/禁用/启用企业内用户
     * @param userid
     * @param touserid
     * @param status(4:删除；3：禁用；2：启用)
     * @return string
     */
    public function actionDocompanyuser(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'    => '用户id不能为空',
                'touserid'  => '当前页不能为空',
                'status'    => '操作状态不能为空'
            ]
        );
        $userid   = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid = Yii::$app->getRequest()->getBodyParam('touserid');
        $status   = Yii::$app->getRequest()->getBodyParam('status');
        if ($userid == $touserid){
            return $this->Error('不能对自身账户进行操作');
        }
        if(!in_array($status,[2,3,4])){
            return $this->Error('参数有误');
        }
        $this     ->checkAuth($userid);
        $result   = QCompanyUser::doCompanyUser($userid,$touserid,$status,$company['id']);
        if($result){
            if($result == 'needadmin'){
                return $this->Error('需要交接工作','',4001);
            }else{
                return $this->Rsuccess('设置成功');
            }
        }else{
            return $this->Error('设置失败','',4002);
        }
    }
    /**
     * 设置/取消企业管理员
     * @param userid
     * @param touserid
     * @param permission(1,2)
     * @return string
     */
    public function actionSetadmin(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'     => '用户id不能为空',
                'touserid'   => '搜索内容不能为空',
                'permission' => '权限不能为空'
            ]
        );
        $userid      = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid    = Yii::$app->getRequest()->getBodyParam('touserid');
        $permission  = Yii::$app->getRequest()->getBodyParam('permission');
        if(!in_array($permission,[1,2])){
            return $this->Error('参数有误');
        }
        $this ->checkAuth($userid);
        $result = QCompanyUser::setAdmin($userid,$touserid,$permission,$company['id']);
        if($result == 'nopermission'){
            return $this->Error('没有权限',"",4010);
        }elseif ($result == 'nocadmin'){
            return $this->Error('需要再指定一个企业管理员',"",4011);
        }elseif ($result == 'nopadmin'){
            return $this->Error('需要再指定一个项目管理员',"",4012);
        }elseif ($result == 'alreadyadmin'){
            return $this->Error('已经是管理员',"",4013);
        } else{
            return $this->Rsuccess('操作成功');
        }
    }
    /**
     * 搜索企业同事
     * @parma userid
     * @param search
     * @return string
     */
    public function actionSearchuser(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'  => '用户id不能为空',
                'search'  => '搜索内容不能为空'
            ]
        );
        $userid   = Yii::$app->getRequest()->getBodyParam('userid');
        $search   = Yii::$app->getRequest()->getBodyParam('search');
        $this     ->checkAuth($userid);
        $result   = UUserInfo::searchUser($search,$company['id']);
        return $this->Rsuccess('用户：',$result);
    }
    /**
     * 管理员-项目管理员管理的各个项目
     * @param userid
     * @param page
     * @return string
     */
    public function actionProjectmanager(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'  => '用户id不能为空',
                'page'    => '当前页不能为空'
            ]
        );
        $userid   = Yii::$app->getRequest()->getBodyParam('userid');
        $page     = Yii::$app->getRequest()->getBodyParam('page');
        $this     ->checkAuth($userid);
        //用户是否为企业管理员
        $isadmin  = QCompanyUser::userPermission($userid,$company['id']);
        if(empty($isadmin) || $isadmin['permission'] == 2){
            return $this->Error('你不是企业管理员');
        }
        $result   = PProjectUser::projectManager($company['id'],$page);
        return $this->Rsuccess('列表：',$result);
    }
    /**
     * 移除项目管理员
     * @param userid
     * @param touserid
     * @param projectid
     * @return string
     */
    public function actionRemoveprojectadmin(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'    => '用户id不能为空',
                'touserid'  => '被移除人不能为空',
                'projectid' => '项目id不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid  = Yii::$app->getRequest()->getBodyParam('touserid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $this     ->checkAuth($userid);
        if($userid == $touserid){
            return $this->Error('不能对自身账户进行操作');
        }
        //项目属于哪个企业
        $projectinfo = PProject::projectByid($projectid);
        if(empty($projectinfo) || $projectinfo['companyid'] != $company['id']){
            return $this->Error('这个项目不在这个企业');
        }
        //用户是否为企业的管理员
        $iscadmin   = QCompanyUser::userPermission($userid,$company['id']);
        //用户是否为项目管理员
        $ispadmin   = PProjectUser::userInfo($userid,$projectid);
        $cadmin     = isset($iscadmin['permission']) ? $iscadmin['permission']:2;
        $padmin     = isset($ispadmin['permission']) ? $ispadmin['permission']:2;
        if($cadmin != 1 && $padmin != 1){
            return $this->Error('权限不足');
        }
        $result     = PProjectUser::removeAdmin($touserid,$projectid);
        if($result){
            return $this->Rsuccess('移除成功');
        }else{
            return $this->Error('需要指定一个项目管理员',"",4008);
        }
    }
    /**
     * 项目成员
     * @param userid
     * @param projectid
     * @param page
     * @param type(admin all)
     * @return string
     */
    public function actionProjectuser(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'    => '用户id不能为空',
                'page'      => '当前页不能为空',
                'projectid' => '项目id不能为空',
                'type'      => '类型不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $page      = Yii::$app->getRequest()->getBodyParam('page');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $type      = Yii::$app->getRequest()->getBodyParam('type');
        if(!in_array($type,['all','admin'])){
            return $this->Error('参数有误');
        }
        $this      ->checkAuth($userid);
        //项目的查看成员和管理员的权限
        $projectinfo = PProject::projectByid($projectid);
        if(empty($projectinfo)){
            return $this->Error('项目不存在');
        }
        //用户是否为项目成员
        $isadmin   = PProjectUser::userInfo($userid,$projectid);
        if(empty($isadmin)){
            if($type == 'all' && $projectinfo['noproject_access_alluser'] == 2){
                return $this->Error('权限不足','','4009');
            }
            if($type == 'admin' && $projectinfo['noproject_access_admin'] == 2){
                return $this->Error('权限不足','','4010');
            }
        }
        $result    = PProjectUser::userList($projectid,$page,$company['id'],$type);
        return $this->Rsuccess('项目成员:',$result);
    }
    /**
     * 设置项目管理员
     * @param userid
     * @param touserid
     * @param projectid
     * @return string
     */
    public function actionSetprojectadmin(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户id不能为空',
                'touserid'  => '被移除人不能为空',
                'projectid' => '项目id不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid  = Yii::$app->getRequest()->getBodyParam('touserid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $this      ->checkAuth($userid);
        if($userid == $touserid){
            return $this->Error('不能自己操作自己');
        }
        //用户是否为项目管理员
        $ispadmin   = PProjectUser::userInfo($userid,$projectid);
        $padmin     = isset($ispadmin['permission']) ? $ispadmin['permission']:2;
        if($padmin != 1){
            return $this->Error('权限不足');
        }
        //设置吧
        PProjectUser::setAdmin($touserid,$projectid);
        return $this->Rsuccess('设置成功');
    }
    /**
     * 将用户移除项目
     * @param userid
     * @param touserid
     * @param projectid
     * @return string
     */
    public function actionDelprojectuser(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户id不能为空',
                'touserid'  => '被移除人不能为空',
                'projectid' => '项目id不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid  = Yii::$app->getRequest()->getBodyParam('touserid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $this      ->checkAuth($userid);
        if($userid == $touserid){
            return $this->Error('不能自己操作自己');
        }
        //用户是否为项目管理员
        $ispadmin   = PProjectUser::userInfo($userid,$projectid);
        $padmin     = isset($ispadmin['permission']) ? $ispadmin['permission']:2;
        if($padmin != 1){
            return $this->Error('权限不足');
        }
        //移除吧
        $result = PProjectUser::exitProject($touserid,$projectid);
        if($result){
            return $this->Rsuccess('移除成功');
        }else{
            return $this->Error('再设置一个管理员','',4008);
        }

    }
    /**
     * 编辑用户基本信息
     * @param userid
     * @param type_value[{"s_username":'w'}]
     * @return string
     */
    public function actionEdituserinfo(){
        self::checkParamIsEmpty(
            [
                'userid'        => '用户id不能为空',
                'type_value'    => '修改类型不能为空'
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        $typeval = Yii::$app->getRequest()->getBodyParam('type_value');
        $this->checkAuth($userid);
        $result  = UUserInfo::editUserinfo($userid,$typeval);
        if($result){
            return $this->Rsuccess('编辑成功');
        }else{
            return $this->Error('类型键不对','','4010');
        }
    }
	/**
     * 查看别人基本信息
     * @param userid
     * @param touserid
     * @return string
     */
	public function actionTouserinfo(){
		self::checkParamIsEmpty(
            [
                'userid'      => '用户id不能为空',
                'touserid'    => '被查看人不能为空'
            ]
        );
        $userid   = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid = Yii::$app->getRequest()->getBodyParam('touserid');
        $this->checkAuth($userid);
        $result   = UUserInfo::toUserinfo($userid,$touserid);
        return $this->Rsuccess('用户信息：',$result);
	}
	/**
     * 用户动态列表
     * @param userid
     * @return string
     */
	public function actionUserdynamic(){
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
        $userid   = Yii::$app->getRequest()->getBodyParam('userid');
        $this->checkAuth($userid);
        $result   = QCompanyUser::userDynamic($userid,$company['id']);
        if($result == 'nopermission'){
            return $this->Error('没有权限，查询失败');
        }
        return $this->Rsuccess('用户动态：',$result);
    }
    /**
     * 注销用户
     * @param userid
     */
    public function actionDeluser(){
        self::checkParamIsEmpty(
            [
                'userid' => '用户id不能为空'
            ]
        );
        $userid =  Yii::$app->getRequest()->getBodyParam('userid');
        $this   -> checkAuth($userid);
        UUser::delUser($userid);
        return $this->Rsuccess('删除成功');
    }
    /**
     * 重置密码
     * @param userid
     * @param oldpassword
     * @param newpassword
     * @param repassword
     * @return bool
     */
    public function actionResetpwd(){
        //validate authentication
        $this->checkAuth(Yii::$app->getRequest()->getBodyParam('userid'));
        self::checkParamIsEmpty(
            [
                'userid'      => '用户id不能为空',
                'oldpassword' => '原密码不能为空',
                'newpassword' => '新密码不能为空',
                'repassword'  => '第二次输入的密码不能为空'
            ]
        );
        $re = UUser::upPassword();
        if($re == 'olderror'){
            return $this->Error('原密码输入错误','',4111);
        }
        if($re == 'reerror'){
            return $this->Error('两次输入不一致','',4112);
        }
        if($re == 'succ'){
            return $this->Rsuccess('修改成功');
        }
    }
    /**
     * 等待批准的普通用户邀请加入的用户
     * @param userid
     * @param page
     * @return string
     */
    public function actionApplyuser(){
        self::checkParamIsEmpty(
            [
                'userid' => '用户id不能为空',
                'page'   => '当前页不能为空'
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        $page    = Yii::$app->getRequest()->getBodyParam('page');
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        $this->checkAuth($userid);
        //用户是否为企业管理员
        $isadmin = QCompanyUser::userPermission($userid,$company['id']);
        if($isadmin['permission'] != 1){
            return $this->Error('权限不足');
        }
        $result  = QCompanyUser::applyUser($company['id'],$page);
        return $this->Rsuccess('申请列表：',$result);
    }
    /**
     * 批准/拒绝申请的人
     * @param userid
     * @param touserid
     * @param type(全部：all)
     * @param status(1.批准 2.拒绝)
     * @return string
     */
    public function actionDoapplyuser(){
        self::checkParamIsEmpty(
            [
                'userid' => '用户id不能为空',
                'status' => '处理状态不能为空'
            ]
        );
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        $status = Yii::$app->getRequest()->getBodyParam('status');
        $type   = Yii::$app->getRequest()->getBodyParam('type');
        $touser = Yii::$app->getRequest()->getBodyParam('touserid');
        if($type == '' && $touser == ''){
            return $this->Error('缺少参数');
        }
        if(!in_array($status,[1,2])){
            return $this->Error('参数错误');
        }
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        $this->checkAuth($userid);
        //用户是否为企业管理员
        $isadmin = QCompanyUser::userPermission($userid,$company['id']);
        if($isadmin['permission'] != 1){
            return $this->Error('权限不足');
        }
        QCompanyUser::doApplyUser($company['id'],$status,$touser,$type);
        return $this->Rsuccess('设置成功');
    }
    /**
     * 添加反馈
     * @param userid
     * @param content
     * @param UploadForm[file]
     * @return string
     */
    public function actionAddfeedback(){
        self::checkParamIsEmpty(
            [
                'userid'  => '用户id不能为空',
                'content' => '内容不能为空'
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        $content = Yii::$app->getRequest()->getBodyParam('content');
        $this->checkAuth($userid);
        $model   = new UploadForm();
        $uploadFile = UploadedFile::getInstances($model, 'file');
        if ($uploadFile == null) {
            $url = '';
        } else {
            foreach($uploadFile as $k=>$v){
                if($v->error > 0){
                    return $this->Error('不得有文件大于2M', '',1090);
                }
            }
            $url = Commonfun::upFile($uploadFile,'feedback')[0]['url'];

        }
        UFeedback::addFeedback($userid,$content,$url);
        return $this->Rsuccess('反馈成功');
    }
    public function actionBind(){
        $uid       = $_POST['userid'];
        $client_id = $_POST['client_id'];
        Commonfun::bindUserid($uid,$client_id);
    }
    /**
     * 判断用户是否为系统或项目管理员
     * @Param userid
     * @param companyid
     * @param projectid
     * @return string
     */
    public function actionIsadmin(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户id不能为空',
                'companyid' => '企业id不能为空',
                'projectid' => '项目id不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $companyid = Yii::$app->getRequest()->getBodyParam('companyid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $admin     = Commonfun::isAdmin($userid,$projectid,$companyid);
        return $this->Rsuccess('权限',$admin);
    }


}

