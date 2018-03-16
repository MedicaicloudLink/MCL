<?php

namespace backend\controllers;

use app\models\Commonfun;
use app\models\PApplyToProject;
use app\models\PProjectInvite;
use app\models\PProjectUser;
use app\models\UUserInfo;
use Yii;
use app\models\QCompany;
use app\models\PProject;
use app\models\UploadForm;
use app\models\PProjectDocument;
use app\models\QCompanyUser;
use yii\web\UploadedFile;
class ProjectController extends BaseController
{
    /**
     * 创建项目
     * @param userid
     * @param name
     * @param mem
     * @param accesstype
     * @return string
     */
    public function actionCreateproject(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'      => '用户id不能为空',
                'name'        => '项目名称不能为空',
                'accesstype'  => '访问权限不能为空'
            ]
        
        );
        $userid      = Yii::$app->getRequest()->getBodyParam('userid');
        $name        = Yii::$app->getRequest()->getBodyParam('name');
        $accesstype  = Yii::$app->getRequest()->getBodyParam('accesstype');
        $mem         = Yii::$app->getRequest()->getBodyParam('mem');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = PProject::createProject($userid,$name,$accesstype,$company['id'],$mem);
        #1:创建成功。2：没资格3。创建失败
        if($result['type'] == 2){
            return $this->Error('没资格创建项目','',301);
        }else if($result['type'] == 1){
            return $this->Rsuccess('创建成功',['projectid'=>$result['projectid']],302);
        }else {
            return $this->Error('创建失败','',303);
        }
    }
    /**
     * 项目内上传文档
     * @param userid
     * @param  UploadForm[file][]
     * @return string
     */
    public function actionUpfile(){
        self::checkParamIsEmpty(
            [
                'userid'       => '用户不能为空',
            ]
        );
        $this       -> checkAuth(Yii::$app->getRequest()->getBodyParam('userid'));
        $model      = new UploadForm();
        $uploadFile = UploadedFile::getInstances($model, 'file');
        if ($uploadFile == null) {
            return $this->Error('请上传照片');
        } else {
            foreach($uploadFile as $k=>$v){
                if($v->error > 0){
                    return $this->Error('不得有文件大于2M', '',1090);
                }
            }
            $info = Commonfun::upFile($uploadFile,'project');
            return $this->Rsuccess('图片信息:', $info);
        }
    }
    /**
     * 上传文件详情
     * @param userid
     * @param projectid
     * @param data(json: [{"url":"www.sdf","name":"123.jpj","mem": "说明","size":"234543"}])
     * @return string
     */
    public function actionUpfileinfo(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空',
                'data'      => '信息不能为空',
                'projectid' => '项目id不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $data       = Yii::$app->getRequest()->getBodyParam('data');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $this       -> checkAuth($userid);
        $result     = PProjectDocument::addFileInfo($userid,$data,$projectid);
        if($result){
            return $this->Rsuccess('添加成功');
        }else{
            return $this->Error('创建失败');
        }
    }
    /**
     * 项目内附件列表
     * @param userid
     * @param projectid
     * @param page
     * @param type(all:全部；my:我的;admin:管理员的)
     * @return string
     */
    public function actionProjectfile(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空',
                'projectid' => '项目id不能为空',
                'page'      => '当前页不能为空',
                'type'      => '类型不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $page       = Yii::$app->getRequest()->getBodyParam('page');
        $type       = Yii::$app->getRequest()->getBodyParam('type');
        $this       -> checkAuth($userid);
        $result     = PProjectDocument::fileList($userid,$projectid,$page,$type);
        return $this->Rsuccess('附件列表：',$result);
    }
    /**
     * 管理员项目列表
     * @param userid
     * @param page
     * @param type(all,closed,delete)
     * @param search(搜索的项目名称)
     * @return string
     */
    public function actionAdminprojectlist(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空',
                'page'      => '当前页不能为空',
                'type'      => '类型不能为空'
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        $page    = Yii::$app->getRequest()->getBodyParam('page');
        $type    = Yii::$app->getRequest()->getBodyParam('type');
        $search  = Yii::$app->getRequest()->getBodyParam('search');
        $this    -> checkAuth($userid);
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        $projectlist = PProject::adminProjectList($userid,$company['id'],$page,$type,$search);
        return $this->Rsuccess('项目列表：',$projectlist);
    }
    /**
     * 项目详情
     * @param userid
     * @param projectid
     * @return string
     */
    public function actionProjectinfo(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空',
                'projectid' => '项目id不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $this      -> checkAuth($userid);
        $result    = PProject::projectInfo($userid,$projectid);
        return $this->Rsuccess('项目详情',$result);
    }
    /**
     * 归档/删除/彻底删除/恢复项目
     * @param userid
     * @param status(1.恢复，2.归档，3.删除，4.彻底删除)
     * @param projectid
     * @return string
     */
    public function actionDostatus(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空',
                'status'    => '状态不能为空',
                'projectid' => '项目id不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $status    = Yii::$app->getRequest()->getBodyParam('status');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $this      -> checkAuth($userid);
        $result    = PProject::doStatus($userid,$status,$projectid);
        if($result){
            return $this->Rsuccess('操作成功');
        }else{
            return $this->Error('操作失败');
        }
    }
    /**
     * 用户本人参加的项目列表
     * @param userid
     * @param type(fast; all)
     * @param search(项目名称)
     * @return string
     */
    public function actionUserprojectlist(){
        self::checkParamIsEmpty(
            [
                'userid'  => '用户不能为空',
                'type'    => '类型不能为空'
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        $type    = Yii::$app->getRequest()->getBodyParam('type');
        $search  = Yii::$app->getRequest()->getBodyParam('search');
        $this    -> checkAuth($userid);
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        $result  = PProjectUser::userProjectList($userid,$company['id'],$type,$search);
        return $this->Rsuccess('项目列表：',$result);
    }
    /**
     * 访问别人参加的项目列表
     * @param userid
     * @param touserid
     * @param search(项目名称)
     * @return string
     */
    public function actionTouserprojectlist(){
        self::checkParamIsEmpty(
            [
                'userid'   => '用户不能为空',
                'touserid' => '被访问者不能为空'
            ]
        );
        $userid   = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid = Yii::$app->getRequest()->getBodyParam('touserid');
        $search   = Yii::$app->getRequest()->getBodyParam('search');
        $this    -> checkAuth($userid);
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        $result  = PProjectUser::touserProjectList($userid,$touserid,$company['id'],$search);
        return $this->Rsuccess('项目列表：',$result);
    }
    /**
     * 项目加入快速访问列表
     * @param userid
     * @param projectid
     * @return string
     */
    public function actionAddfast(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空',
                'projectid' => '项目不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $this    -> checkAuth($userid);
        PProjectUser::addFast($userid,$projectid);
        return $this->Rsuccess('设置成功');
    }
    /**
     * 删除文件
     * @param userid
     * @param projectid
     * @param documentid
     * @return bool
     */
    public function actionDelfile(){
        self::checkParamIsEmpty(
            [
                'userid'     => '用户不能为空',
                'projectid'  => '项目不能为空',
                'documentid' => '文档id不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $documentid = Yii::$app->getRequest()->getBodyParam('documentid');
        $this       -> checkAuth($userid);
        if(PProjectDocument::delFile($userid,$projectid,$documentid)){
            return $this->Rsuccess('删除成功');
        }else{
            return $this->Error('删除失败，可能权限不够');
        }
    }
    /**
     * 下载文件
     * @param userid
     * @param documentid
     * @type type(invite_excel)
     * @return string
     */
    public function actionDownfile(){
        $userid      = Yii::$app->request->get('userid');
        $documentid  = Yii::$app->request->get('documentid');
        $type        = Yii::$app->request->get('type');
        //下载邀请excel
        if($type != '' && $type == 'invite_excel'){
            $url     = 'http://'.Yii::$app->params['inviteexcel'];
            $outfile = Yii::$app->params['inviteexcel_name'];
        }else{
            if($userid == '' || $documentid == ''){
                return $this->Error('参数不可以为空');
            }
            //文档信息
            $fileinfo   = PProjectDocument::fileInfo($documentid);
            $url        = 'http://'.$fileinfo['document_url'];
            $outfile    = $fileinfo['document_name'];
        }
        $wrstr = htmlspecialchars_decode(file_get_contents($url));
        header('Content-type: application/octet-stream; charset=utf8');
        Header("Accept-Ranges: bytes");
        header('Content-Disposition: attachment; filename='.$outfile);
        echo $wrstr;
        exit();
    }
    /**
     * 附件详情
     * @param userid
     * @param documentid
     * @return string
     */
    public function actionFileinfo(){
        self::checkParamIsEmpty(
            [
                'userid'     => '用户不能为空',
                'documentid' => '附件不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $documentid = Yii::$app->getRequest()->getBodyParam('documentid');
        $this       -> checkAuth($userid);
        $result     = PProjectDocument::fileInfo($documentid);
        return $this->Rsuccess('详情',$result);
    }
    /**
     * 邀请企业同事进项目
     * @param userid（项目管理员）
     * @param projectid
     * @param touserid(用，隔开)
     * @return string
     */
    public function actionInviteworker(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'     => '用户不能为空',
                'projectid'  => '项目id不能为空',
                'touserid'   => '同事不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $touserid   = Yii::$app->getRequest()->getBodyParam('touserid');
        $this       -> checkAuth($userid);
        $result     = PProjectInvite::addInvite($userid,$projectid,$touserid,$company['id']);
        if($result){
            return $this->Rsuccess('邀请成功');
        }else{
            return $this->Error('权限不够');
        }
    }
    /**
     * 邀请非企业用户进项目
     * @param userid
     * @param projectid
     * @param tomail(json:[{"tomail":"q"},{"tomail":"w"}])
     * @return string
     */
    public function actionInvitenoworker(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'     => '用户不能为空',
                'projectid'  => '项目id不能为空',
                'tomail'     => '邮箱不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $tomail     = Yii::$app->getRequest()->getBodyParam('tomail');
        $this       -> checkAuth($userid);
        $touserarr = json_decode($tomail,true);
        if(empty($touserarr)){
            return $this->Error('邮箱不能为空');
        }
        $result     = PProjectInvite::addNoWorker($userid,$projectid,$touserarr,$company['id']);
        if($result){
            return $this->Rsuccess('邀请成功');
        }else{
            return $this->Error('权限不够');
        }
    }
    /**
     * 退出项目
     * @param userid
     * @param projectid
     * @return string
     */
    public function actionExitproject(){
        self::checkParamIsEmpty(
            [
                'userid'     => '用户不能为空',
                'projectid'  => '项目id不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $this       -> checkAuth($userid);
        $result     = PProjectUser::exitProject($userid,$projectid);
        if($result){
            return $this->Rsuccess('退出成功');
        }else{
            return $this->Error('需要指定管理员');
        }
    }
    /**
     * 申请加入项目
     * @param userid
     * @param projectid
     * @return string
     */
    public function actionApplyproject(){
        self::checkParamIsEmpty(
            [
                'userid'     => '用户不能为空',
                'projectid'  => '项目id不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $this       -> checkAuth($userid);
        $result     = PProjectUser::applyProject($userid,$projectid);
        if($result){
            return $this->Rsuccess('申请成功');
        }else{
            return $this->Error('申请失败');
        }
    }
    /**
     * 审核加入项目请求列表
     * @param userid
     * @param projectid
     * @param page
     * @return string
     */
    public function actionApplylist(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空',
                'page'      => '页面不能为空',
                'projectid' => '项目id不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $page       = Yii::$app->getRequest()->getBodyParam('page');
        $this   -> checkAuth($userid);
        $result = PApplyToProject::applyList($userid,$projectid,$page);
        if($result){
            return $this->Rsuccess('请求列表：',$result);
        }else{
            return $this->Error('你不是这个项目的管理员');
        }
    }
    /**
     * 修改项目
     * @param userid
     * @param projectid
     * @param name
     * @param mem
     * @param accesstype
     * @param noproject_access_admin(非项目成员查看管理员，1.可以；2.不可以)
     * @param noproject_access_alluser(非项目成员查看全部成员，1.可以；2.不可以)
     * @param addfeed_access(发布动态消息的权限，1.管理员，2.全部成员)
     * @return string
     */
    public function actionUpproject(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空',
                'projectid' => '项目id不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $this   -> checkAuth($userid);
        //项目所在企业
        $company    = PProject::projectByid($projectid);
        if(empty($company)){
            return $this->Error('项目信息有误');
        }
        //用户是否为企业的管理员
        $iscadmin   = QCompanyUser::userPermission($userid,$company['companyid']);
        //用户是否为项目管理员
        $ispadmin   = PProjectUser::userInfo($userid,$projectid);
        $cadmin     = isset($iscadmin['permission']) ? $iscadmin['permission']:2;
        $padmin     = isset($ispadmin['permission']) ? $ispadmin['permission']:2;
        if($cadmin != 1 && $padmin != 1){
            return $this->Error('权限不足','','4009');
        }
        //修改吧
        PProject::upProject($projectid);
        return $this->Rsuccess('修改成功');
    }
    /**
     * 处理申请
     * @param userid
     * @param applyid
     * @param touserid
     * @param projectid
     * @param status(2,同意;3,拒绝;4,忽略)
     * @return string
     */
    public function actionDoapplyproject(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空',
                'touserid'  => '申请人不能为空',
                'projectid' => '项目id不能为空',
                'status'    => '状态不能为空',
                'applyid'   => '申请id不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid   = Yii::$app->getRequest()->getBodyParam('touserid');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $status     = Yii::$app->getRequest()->getBodyParam('status');
        $applyid    = Yii::$app->getRequest()->getBodyParam('applyid');
        if(!in_array($status,[2,3,4])){
            return $this->Error('参数有误');
        }
        $this   -> checkAuth($userid);
        //用户是否为项目管理员
        $isadmin = PProjectUser::userInfo($userid,$projectid);
        if(empty($isadmin) || $isadmin['permission'] == 2){
            return $this->Error('权限不足','',4009);
        }
        //处理
        PApplyToProject::doApply($userid,$touserid,$projectid,$status,$applyid);
        return $this->Rsuccess('处理成功');
    }
    /**
     * 项目申请（全部同意，全部拒绝）
     * @param userid
     * @param projectid
     * @param status(2:同意；3：拒绝)
     */
    public function actionDoall(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空',
                'projectid' => '项目id不能为空',
                'status'    => '状态不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $status     = Yii::$app->getRequest()->getBodyParam('status');
        if(!in_array($status,[2,3])){
            return $this->Error('参数有误');
        }
        //用户是否为项目管理员
        $isadmin = PProjectUser::userInfo($userid,$projectid);
        if(empty($isadmin) || $isadmin['permission'] == 2){
            return $this->Error('权限不足','',4009);
        }
        $this   -> checkAuth($userid);
        //处理
        PApplyToProject::doAll($userid,$projectid,$status);
        return $this->Rsuccess('处理成功');
    }
    /**
     * 关闭/开启项目动态
     * @param userid
     * @param project
     * @param status(开启1，关闭2)
     * @return string
     */
    public function actionDoprojectfeed(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空',
                'projectid' => '项目id不能为空',
                'status'    => '状态不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $status     = Yii::$app->getRequest()->getBodyParam('status');
        if(!in_array($status,[1,2])){
            return $this->Error('参数有误');
        }
        $this   -> checkAuth($userid);
        //处理
        PProjectUser::doProjectFeed($userid,$projectid,$status);
        return $this->Rsuccess('处理成功');
    }
    /**
     * 邀请进入项目的通知
     * @param userid
     * @param projectid
     * @return string
     */
    public function actionInvitenotice(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空',
                'projectid' => '项目id不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $this       -> checkAuth($userid);
        $result     = PProjectInvite::inviteInfo($userid,$projectid);
        if(!empty($result)){
            $userinfo              = UUserInfo::userInfo($result['userid']);
            $result['username']    = $userinfo['s_username'];
            $result['s_avatar']    = $userinfo['s_avatar'];
            $result['projectname'] = PProject::projectByid($projectid)['projectname'];
            unset($result['companyid']);
            unset($result['tomail']);
            unset($result['tocompany']);
            unset($result['status']);
            unset($result['show']);
        }
        return $this->Rsuccess('邀请进入项目的通知',$result);
    }
    /**
     * 接收/拒绝项目邀请
     * @param userid
     * @param type(1.同意 2.拒绝)
     * @param inviteid
     * @return string
     */
    public function actionDoinvite(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空',
                'type'      => '类型不能为空',
                'inviteid'  => '邀请id不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $type       = Yii::$app->getRequest()->getBodyParam('type');
        $inviteid   = Yii::$app->getRequest()->getBodyParam('inviteid');
        $this       -> checkAuth($userid);
        if(!in_array($type,[1,2])){
            return $this->Error('参数格式不对');
        }
        $result     = PProjectInvite::doInvite($userid,$type,$inviteid);
        if($result){
            return $this->Rsuccess('处理成功');
        }else{
            return $this->Error('处理失败');
        }
    }
    /**
     * 项目快速访问列表-1
     * @param userid
     * @return string
     */
    public function actionFastlist(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空'
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        $this    -> checkAuth($userid);
        $result  = PProjectUser::fastList($userid,'');
        return $this->Rsuccess('快速访问列表：',$result);
    }
    /**
     * 置顶/访问时间/移除项目从快速访问列表中
     * @param userid
     * @param do_data [{"projectid":"33","status":"1"},{"projectid":"22","status":"2"}]
     * @return array
     */
    public function actionDofast(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户不能为空',
                'do_data'   => '操作数据不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $do_data    = Yii::$app->getRequest()->getBodyParam('do_data');
        $this       -> checkAuth($userid);
        $result     = PProjectUser::doFast($userid,$do_data);
        if($result){
            return $this->Rsuccess('操作成功');
        }else{
            return $this->Error('操作失败');
        }

    }
}
