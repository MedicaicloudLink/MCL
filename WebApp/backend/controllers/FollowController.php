<?php
/**
 * @todo 随访相关
 * Date: 2017/5/31 
 */

namespace backend\controllers;

use app\models\UFollowPatient;
use app\models\UProjectTask;
use app\models\UUserProject;
use app\models\FRemark;
use app\models\UploadForm;
use Yii;
use yii\web\UploadedFile;
use app\models\FFile;
use app\models\UPatientbase;
use app\models\ULockpatient;

class FollowController extends BaseController {

    /**
     * @todo 创建项目随访任务           follow/createfollow
     * @param projectid
     * @param userid
     * @param taskname
     * @param taskformid
     * @param taskmonth
     * @param taskcontent
     */
    public function actionCreatefollow(){
        $this->checkAuth(Yii::$app->getRequest()->getBodyParam('userid'));
        self::checkParamIsEmpty(
                [
                    'userid'      => '用户id不能为空',
                    'projectid'   => '项目id不能为空',
                    'taskname'    => '任务名称不能为空',
                    'taskformid'  => '表单id不能为空',
                    'taskmonth'   => '任务条件不能为空'
                ]
        );
        $projectid             = Yii::$app->getRequest()->getBodyParam('projectid');
        $admin                 = Yii::$app->getRequest()->getBodyParam('userid');
        $follow['taskname']    = Yii::$app->getRequest()->getBodyParam('taskname');
        $follow['taskformid']  = Yii::$app->getRequest()->getBodyParam('taskformid');
        $follow['taskmonth']   = Yii::$app->getRequest()->getBodyParam('taskmonth');
        $follow['taskcontent'] = Yii::$app->getRequest()->getBodyParam('taskcontent');
        if(UProjectTask::createProjectTask(2, $projectid, $admin,$follow)){
            return $this->Rsuccess('创建成功');
        }
        return $this->Error('创建失败');
    }
   
    /**
     * @todo 展示用户所参与的随访计划       follow/showfollows
     * @param userid
     * @param projectid
     */
    public function actionShowfollows() {
        self::checkParamIsEmpty(
                [
                    'userid'      => '用户id不能为空',
                    'projectid'   => '项目id不能为空'
                ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam("userid");
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $this      ->checkAuth($userid);
        $follows   = UProjectTask::showFollow($userid,$projectid);
        if ($follows) {
            return $this->Rsuccess("随访列表", $follows);
        }
        return $this->Error("未查询到结果");
    }
    /**
     * @todo 删除随访                follow/deletefollow
     * @param userid
     * @param projectid
     * @param taskid
     */
    public function actionDeletefollow(){
        self::checkParamIsEmpty(
                [
                    'userid'      => '用户id不能为空',
                    'projectid'   => '项目id不能为空',
                    'taskid'      => '任务id不能为空'
                ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam("userid");
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $taskid    = Yii::$app->getRequest()->getBodyParam('taskid');
        //$this      ->checkAuth($userid);
        //有无权限
        $admin     = UUserProject::getPermitInProject($userid,$projectid);
        if($admin != 1){
            return $this->Error('权限不足');
        }
        if(UProjectTask::deleteFollow($userid,$projectid,$taskid)){
            return $this->Rsuccess('删除成功');
        }else{
            return $this->Error('删除失败');
        }
        
    }
    /**
     * @todo 编辑随访                    follow/editfollow
     * @param projectid
     * @param userid
     * @param taskid
     * @param taskname
     * @param taskformid 
     * @param taskmonth
     * @param taskcontent
     * @param formname
     * @param formdata
     */
    public function actionEditfollow(){
        self::checkParamIsEmpty(
                [
                    'userid'      => '用户id不能为空',
                    'projectid'   => '项目id不能为空',
                    'taskid'      => '任务名称不能为空',
                ]
        );
        $this->checkAuth(Yii::$app->getRequest()->getBodyParam('userid'));
        //有无权限
        $admin     = UUserProject::getPermitInProject(Yii::$app->getRequest()->getBodyParam('userid'),Yii::$app->getRequest()->getBodyParam('projectid'));
        if($admin != 1){
            return $this->Error('权限不足');
        }
        //修改
        if(UProjectTask::editFollow()){
            return $this->Rsuccess('编辑成功');
        }else{
            return $this->Error('编辑失败');
        }
    }
    /**
     * @todo 统计随访       follow/followstatistics
     * @param userid
     * @param projectid
     */
    public function actionFollowstatistics(){
        self::checkParamIsEmpty(
                [
                    'projectid'   => '项目id不能为空',
                    'userid'      => '用户不能为空'
                ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $this->checkAuth($userid);
        $result    = UFollowPatient::followSta($projectid,$userid);
        return $this->Rsuccess('随访数据：',$result);
    }
    /**
     * @todo 患者随访-添加/编辑患者随访      follow/feditfollow
     * @param userid
     * @param projectid
     * @param mdid
     * @param taskid
     * @param sourcedata
     * @param patientdata
     * @param status 
     * @param other_reason(若状态是其他原因，则填写)
     */
    public function actionFeditfollow(){
        self::checkParamIsEmpty(
                [
                    'userid'       => '用户不能为空',
                    'projectid'    => '项目id不能为空',
                    'mdid'         => '患者id不能为空',
                    'taskid'       => '随访id不能为空',
                    'patientdata'  => '用户数据不能为空',
                    'sourcedata'   => '源数据不能为空',
                    'status'       => '状态不能为空'
                ]
        );
        $userid    =  Yii::$app->getRequest()->getBodyParam('userid');
        $mdid      =  Yii::$app->getRequest()->getBodyParam('mdid');
        $taskid    =  Yii::$app->getRequest()->getBodyParam('taskid');
        //$projectid =  Yii::$app->getRequest()->getBodyParam('projectid');
        $this      -> checkAuth($userid);
        //查看上一次随访是否随访过
        /*$taskarr   = UProjectTask::showFollow($userid,$projectid);
        if(!empty($taskarr)){
            foreach($taskarr as $k=>$v){
                if($v['taskid'] == $taskid){
                    $flag = $k;#值为此次任务id的键
                }
            }
            //不是第一次随访
            if($flag != 0){
                //上一次随访的任务id
                $beforetaskid  = $taskarr[$flag-1]['taskid'];
                //患者是否已经完成上一次的随访
                $patientbefore = UFollowPatient::followPatient($mdid, $beforetaskid);
                if(empty($patientbefore) || $patientbefore['u_follow_status']!=1){
                    return $this->Error('患者的上一次随访还没处理完');
                }
            }
        }*/
        //患者是否被提交过
        $iscommit  = UFollowPatient::commitPatientData($mdid, $taskid);
        if(!empty($iscommit)){
            return $this->Error('此病例已提交');
        }
        $result    =  UFollowPatient::createFollow();
        if($result){
            //解除锁定
            ULockpatient::unLock($userid,$mdid,$taskid);
            return $this->Rsuccess('编辑成功,记录id',$result);
        }else{
            return $this->Error('编辑失败');
        }
    }
    /**
     * @todo 患者随访-创建备注    follow/fcreateremark
     * @param recordid
     * @param userid
     * @param remark
     */
    public function actionFcreateremark(){
        self::checkParamIsEmpty(
                [
                    'userid'       => '用户不能为空',
                    'recordid'     => '记录id不能为空',
                    'remark'       => '备注不能为空'
                ]
        );
        $this   -> checkAuth(Yii::$app->getRequest()->getBodyParam('userid'));
        $result =  FRemark::createRemark();
        if($result){
            return $this->Rsuccess('创建成功');
        }else{
            return $this->Error('创建失败');
        }
    }
    /**
     * @todo 患者随访-备注列表 follow/fremarklist
     * @param recordid
     */
    public function actionFremarklist(){
        self::checkParamIsEmpty(
                [
                    'recordid'         => '记录id不能为空',
                ]
        );
        $recordid   = Yii::$app->getRequest()->getBodyParam('recordid');
        $result     = FRemark::remarkList($recordid);
        return $this->Rsuccess('备注列表：',$result);
    }
    /**
     * @todo 患者随访-删除备注 follow/fdeleteremark
     * @param id
     * @param userid
     */
    public function actionFdeleteremark(){
        self::checkParamIsEmpty(
                [
                    'userid'    => '用户不能为空',
                    'id'        => '备注id'
                ]
        );
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        $id     = Yii::$app->getRequest()->getBodyParam('id');
        $this   -> checkAuth($userid);
        $result = FRemark::deleteRemark($id,$userid);
        if($result){
            return $this->Rsuccess('删除成功');
        }else{
            return $this->Error('删除失败');
        }
    }
    /**
     * @todo 患者随访-添加文件         follow/fupfile
     * @param userid
     * @param UploadForm[file][]
     */
    public function actionFupfile(){
        self::checkParamIsEmpty(
                [
                    'userid'       => '用户不能为空'
                ]
        );
        $this       -> checkAuth(Yii::$app->getRequest()->getBodyParam('userid'));
        $model      = new UploadForm();
        $uploadFile = UploadedFile::getInstances($model, 'file');
        if ($uploadFile == null) {
            return $this->Error('请上传照片');
        } else {
            $info = FFile::upFile($uploadFile);
            return $this->Rsuccess('图片信息:', $info);
        }
    }
    /**
     * @todo 患者随访-附件添加文字说明        follow/faddfilenote
     * @param userid
     * @param recordid
     * @param data (json: [{"url":"www.sdf","fname":"123.jpj","note": "录音"}])
     * @return string
     */
    public function actionFaddfilenote(){
        self::checkParamIsEmpty(
                [
                    'userid'    => '用户不能为空',
                    'recordid'  => '记录id不能为空'
                ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $data       = Yii::$app->getRequest()->getBodyParam('data');
        $recordid   = Yii::$app->getRequest()->getBodyParam('recordid');
        $this   -> checkAuth($userid);
        $result = FFile::addFileNote($userid,$data,$recordid);
        if($result){
            return $this->Rsuccess('添加成功');
        }else{
            return $this->Error('添加失败');
        }
    }
    /**
     * @todo 患者随访-附件列表          follow/ffilelist
     * @param recordid
     * @return string
     */
    public function actionFfilelist(){
        self::checkParamIsEmpty(
                [
                    'recordid'    => '记录id不能为空'
                ]
        );
        $recordid  = Yii::$app->getRequest()->getBodyParam('recordid');
        $result    = FFile::getFile($recordid);
        return $this->Rsuccess('附件列表：',$result);
    }
    /**
     * @todo 患者随访-删除附件        follow/fdeletefile
     * @param id 附件id
     * @param userid 用户id
     */
    public function actionFdeletefile(){
        self::checkParamIsEmpty(
                [
                    'id'        => '附件id不能为空',
                    'userid'    => '用户id不能为空',
                ]
        );
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        $id     = Yii::$app->getRequest()->getBodyParam('id');
        $result = FFile::deleteFile($userid,$id);
        if($result){
            return $this->Rsuccess('删除成功');
        }else{
            return $this->Error('删除失败');
        }
    }
    /**
     * @todo 患者随访-工作日志        follow/fworkdata
     * @param userid
     * @param projectid
     * @param pagenum
     * @param type  (name:患者姓名；age:年龄；gender:性别；status:状态；updatetime:时间)
     * @param sort  (asc desc)
     * @param search 搜索内容
     */
    public function actionFworkdata(){
        self::checkParamIsEmpty(
                [
                    'userid'       => '用户不能为空',
                    'projectid'    => '项目id不能为空',
                    'pagenum'      => '页数不能为空'
                ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $pagenum   = Yii::$app->getRequest()->getBodyParam('pagenum');
        $type      = Yii::$app->getRequest()->getBodyParam('type');
        $sort      = Yii::$app->getRequest()->getBodyParam('sort');
        $search    = Yii::$app->getRequest()->getBodyParam('search');
        $this      -> checkAuth($userid);
        if($type   == ''){
            $type  = 'updatetime';
        }
        if($sort   == ''){
            $sort  = 'desc';
        }
        $result    = UFollowPatient::workList($userid,$projectid,$pagenum,$type,$sort,$search);
        return $this->Rsuccess('工作日志：',$result);
    }
    /**
     * @todo 患者随访-保存/提交/退回的随访列表      follow/ffollowlist
     * @param userid
     * @param projectid
     * @param type (save/commit/return)
     * @param pagenum
     * @param search
     */
    public function actionFfollowlist(){
        self::checkParamIsEmpty(
                [
                    'userid'       => '用户不能为空',
                    'projectid'    => '项目id不能为空',
                    'type'         => '类型不能为空',
                    'pagenum'      => '当前页不能为空',
                ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $type      = Yii::$app->getRequest()->getBodyParam('type');
        $pagenum   = Yii::$app->getRequest()->getBodyParam('pagenum');
        $search    = Yii::$app->getRequest()->getBodyParam('search');
        $this      -> checkAuth($userid);
        $result    = UFollowPatient::statusList($userid,$projectid,$type,$pagenum,$search);
        return $this->Rsuccess('随访：',$result);
    }
    /**
     * @todo 患者随访-应随访患者        follow/fshouldfollow
     * @param userid     用户id
     * @param projectid  项目id
     * @param pagenum    当前页
     * @param type       排序内容(name,gender,age,overtime,lasttime)
     * @param sort       顺序(asc desc)
     * @param search     搜索内容
     */
    public function actionFshouldfollow(){
        self::checkParamIsEmpty(
                [
                    'userid'       => '用户不能为空',
                    'projectid'    => '项目id不能为空',
                    'pagenum'      => '页数不能为空'
                ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $pagenum   = Yii::$app->getRequest()->getBodyParam('pagenum');
        $type      = Yii::$app->getRequest()->getBodyParam('type');
        $sort      = Yii::$app->getRequest()->getBodyParam('sort');
        $search    = Yii::$app->getRequest()->getBodyParam('search');
        $this      -> checkAuth($userid);
        $result    = UPatientbase::shouldFollow($userid,$projectid,$pagenum,$type,$sort,$search);
        return $this->Rsuccess('应随访患者列表：',$result);
    }
    /**
     * @todo 患者随访-患者随访详情    follow/fpatientdata
     * @param recordid
     */
    public function actionFpatientdata(){
        self::checkParamIsEmpty(
                [
                    'recordid'    => '记录id不能为空'
                ]
        );
        $recordid  = Yii::$app->getRequest()->getBodyParam('recordid');
        $result    = UFollowPatient::patientData($recordid);
        return     $this->Rsuccess('随访病例数据：',$result);
    }
    /**
     * @todo 管理端-患者随访详情    follow/mpatientdata
     * @param mdid
     * @param taskid
     */
    public function actionMpatientdata(){
        self::checkParamIsEmpty(
                [
                    'mdid'      => '患者id不能为空',
                    'taskid'    => '任务id不能为空'
                ]
        );
        $mdid    = Yii::$app->getRequest()->getBodyParam('mdid');
        $taskid  = Yii::$app->getRequest()->getBodyParam('taskid');
        $result    = UFollowPatient::mPatientData($mdid,$taskid);
        return     $this->Rsuccess('随访病例数据：',$result);
    }
    /**
     * @todo 患者随访-随访详情      follow/ffollowinfo
     * @param taskid
     */
    public function actionFfollowinfo(){
        self::checkParamIsEmpty(
                [
                    'taskid'    => '随访id不能为空'
                ]
        );
        $taskid    = Yii::$app->getRequest()->getBodyParam('taskid');
        $result    = UProjectTask::followInfo($taskid);
        return     $this->Rsuccess('随访详情：',$result);
    }
    /**
     * @todo 患者随访-不同状态下的随访数量     follow/fstatusnum
     * @param userid
     * @param projectid
     */
    public function actionFstatusnum(){
        self::checkParamIsEmpty(
                [
                    'userid'       => '用户id不能为空',
                    'projectid'    => '项目id不能为空',
                ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $this      -> checkAuth($userid);
        $result    = UFollowPatient::statusNum($userid,$projectid);
        return $this->Rsuccess('不同状态下的随访数量：',$result);
    }
    /**
     * @todo 患者历史记录    follow/fpatientrecord
     * @param mdid
     */
    public function actionFpatientrecord(){
        self::checkParamIsEmpty(
                [
                    'mdid'         => '患者id不能为空'
                ]
        );
        $mdid    = Yii::$app->getRequest()->getBodyParam('mdid');
        $result  = UPatientbase::patientRecord($mdid);
        return $this->Rsuccess('历史记录',$result);
    }
    /**
     * @todo 点击锁定患者记录        follow/fclickfollow
     * @param userid
     * @param mdid
     * @param taskid
     */
    public function actionFclickfollow(){
        self::checkParamIsEmpty(
                [
                    'userid'       => '用户id不能为空',
                    'mdid'         => '患者id不能为空',
                    'taskid'       => '任务id不能为空'
                ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $mdid      = Yii::$app->getRequest()->getBodyParam('mdid');
        $taskid    = Yii::$app->getRequest()->getBodyParam('taskid');
        $this      -> checkAuth($userid);
        //是否可以访问
        $lockinfo  = ULockpatient::isRead($userid,$mdid,$taskid);
        if($lockinfo){
            if($lockinfo == 'allow'){
                return $this->Rsuccess('允许',$lockinfo);
            }else{
                return $this->Rsuccess('不允许访问',$lockinfo);
            }
        }else{
            return $this->Error('更新失败');
        }
    }
    /**
     * @todo 审核患者          follow/checkpatient
     * @param userid
     * @param remark
     * @param recordid
     * @param projectid
     * @param status (10:退回；1：通过)
     */
    public function actionCheckpatient(){
        self::checkParamIsEmpty(
                [
                    'userid'    => '用户不能为空',
                    'recordid'  => '患者id不能为空',
                    'status'    => '审核状态不能为空',
                    'projectid' => '项目id不能为空'
                ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $this -> checkAuth($userid);
        // 权限身份验证
        $permit     = $this->getPermitInProject($userid, $projectid);
        if ($permit == 2) {
            return $this->Error("权限不足");
        }
        $result     = UFollowPatient::checkPatient($userid);
        if($result){
            return $this->Rsuccess('添加成功');
        }else{
            return $this->Error('添加失败');
        }
    }
}