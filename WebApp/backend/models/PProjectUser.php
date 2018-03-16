<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "p_project_user".
 *
 * @property integer $id
 * @property string $projectid
 * @property string $userid
 * @property string $companyid
 * @property integer $permission
 * @property string $createtime
 * @property string $quicktime
 * @property integer $show_feed
 * @property integer $status
 * @property integer $quick_status
 * @property string $top_time
 * @property string $access_time
 */
class PProjectUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'p_project_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['projectid'], 'required'],
            [['permission', 'show_feed', 'status'], 'integer'],
            [['createtime', 'quicktime'], 'safe'],
            [['projectid', 'userid','companyid'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'projectid' => 'Projectid',
            'userid' => 'Userid',
            'permission' => 'Permission',
            'createtime' => 'Createtime',
            'quicktime' => 'Quicktime',
            'show_feed' => 'Show Feed',
            'status' => 'Status',
            'companyid' => 'Companyid',
            'quick_status' => 'Quick Status',
            'top_time' => '',
            'access_time' => ''
        ];
    }
    /**
     * 关联用户基本信息表
     */
    public function getUUserInfo(){
        return $this->hasMany(UUserInfo::className(), ['s_userid'  => 'userid']);
    }
    /**
     * 关联项目表
     */
    public function getPProject(){
        return $this->hasMany(PProject::className(), ['projectid'  => 'projectid']);
    }
    /**
     * 关联企业用户表
     */
    public function getQCompanyUser(){
        return $this->hasMany(QCompanyUser::className(), ['userid'  => 'userid']);
    }
    /**
     * 项目中成员信息
     * @param $userid
     * @param $projectid
     * @return array
     */
    public static function userInfo($userid,$projectid){
        $result = PProjectUser::find()
                ->where(['userid'=>$userid,'projectid'=>$projectid])
                ->asarray()
                ->one();
        return $result;
    }
    /**
     * 创建成员
     * @param $userid
     * @param $projectid
     * @param $permission
     * @param companyid用户所在企业
     * @return bool
     */
    public static function addUser($userid,$projectid,$permission,$companyid){
        //项目中是否已有此成员
        $userinfo = PProjectUser::userInfo($userid,$projectid);
        if(empty($userinfo)){
            $model = new PProjectUser();
            $model -> projectid     = $projectid;
            $model -> userid        = $userid;
            $model -> permission    = $permission;
            $model -> createtime    = date('Y-m-d H:i:s');
            $model -> quicktime     = date('Y-m-d H:i:s');
            $model -> show_feed     = 1;
            $model -> status        = 1;
            $model -> quick_status  = 1;
            $model -> access_time   = date('Y-m-d H:i:s');
            $model -> companyid     = $companyid;
            $model -> save();
        }
        return true;
    }
    /**
     * 项目管理员信息
     * @param projectid
     * @return array
     */
    public static function adminInfo($projectid){
        $result = PProjectUser::find()
                ->joinWith('uUserInfo')
                ->select('userid,s_username,s_mail')
                ->where(['projectid'=>$projectid,'permission'=>1])
                ->orderBy(['createtime'=>SORT_ASC])
                ->asArray()
                ->all();
        if(!empty($result)){
            foreach ($result as $key=>$val){
                unset($result[$key]['uUserInfo']);
            }
        }
        return $result;
    }
    /**
     * 项目成员个数
     * @param projectid
     * @return string
     */
    public static function countUser($projectid){
        $count = PProjectUser::find()
                ->select('userid')
                ->where(['projectid'=>$projectid])
                ->count();
        return $count;
    }
    /**
     * 用户参加的项目列表
     * @param userid
     * @param companyid
     * @param type
     * @param search
     * @return array
     */
    public static function userProjectList($userid,$companyid,$type,$search){
        #快速访问
        if($type == 'fast'){
            $data   = PProjectUser::find()
                    ->joinWith('pProject')
                    ->select('p_project_user.createtime,quick_status,p_project.projectid,projectname,access_type,p_project.status')
                    ->where(['p_project.status'=>[1,2],'p_project_user.companyid'=>$companyid,'p_project_user.userid'=>$userid,'p_project_user.status'=>1])
                    ->limit(5)
                    ->orderby(['p_project_user.quicktime'=>SORT_DESC])
                    ->asArray()
                    ->all();
        }else{
            $where  = ['p_project.status'=>[1,2],'p_project_user.companyid'=>$companyid,'p_project_user.userid'=>$userid,'p_project_user.status'=>1];
            if($search != ''){
                $andwhere = ['like','p_project.projectname',$search];
            }
            #全部
            $dataob = PProjectUser::find()
                    ->joinWith('pProject')
                    ->select('p_project_user.createtime,p_project.projectid,projectname,access_type,p_project.status,show_feed,quick_status')
                    ->where($where);
            if($search != ''){
                $dataob ->andWhere($andwhere);
            }
            $data = $dataob ->orderby(['createtime'=>SORT_DESC])
                            ->asArray()
                            ->all();
        }
        foreach($data as $key=>$val){
            unset($data[$key]['pProject']);
            $data[$key]['admin'] = PProjectUser::adminInfo($val['projectid']);
            $data[$key]['num']   = PProjectUser::countUser($val['projectid']);
        }
        $result['data']  = $data;
        return $result;
    }
    /**
     * 访问别人的项目列表
     * @Parma userid
     * @param touserid
     * @param companyid
     * @param search
     * @return array
     */
    public static function touserProjectList($userid,$touserid,$companyid,$search){
        //访问者参加的除公开以外的项目
        $userpid         = PProjectUser::userProjectButPublic($userid);
        //被访问者参加的除公开以外的项目
        $touserpid       = PProjectUser::userProjectButPublic($touserid);;
        #共同参加的非公开项目
        $publicpid       = array_intersect ($userpid, $touserpid);
        #被访问者参加的公开项目
        $touserpublicpid = PProjectUser::userProjectPublic($touserid);
        #被访问者和访问者共同参加的和被访者参加的公开项目
        $allpid          = array_merge($publicpid,$touserpublicpid);
        $where  = [
            'projectid'=>$allpid,
        ];
        if($search != ''){
            $andwhere = ['like','projectname',$search];
        }
        #全部
        $dataob = PProject::find()
                ->select('projectid,projectname,access_type,status')
                ->where($where);
        if($search != ''){
            $dataob ->andWhere($andwhere);
        }
        $data   = $dataob ->asArray()
                ->all();
        foreach($data as $key=>$val){
            //用户在项目中情况
            $userprojectinfo          = PProjectUser::userInfo($touserid,$val['projectid']);
            $data[$key]['createtime'] = $userprojectinfo['createtime'];
            $data[$key]['show_feed']  = $userprojectinfo['show_feed'];
            $data[$key]['admin']      = PProjectUser::adminInfo($val['projectid']);
            $data[$key]['num']        = PProjectUser::countUser($val['projectid']);
        }
        return $data;

    }
    /**
     * 加入快速访问列表
     * @param userid
     * @param projectid
     * @return bool
     */
    public static function addFast($userid,$projectid){
        PProjectUser::updateAll(['quicktime'=>date('Y-m-d H:i:s')],['userid'=>$userid,'projectid'=>$projectid]);
        return true;
    }
    /**
     * 用户在企业中参加的项目并且没有屏蔽这个项目的动态
     * @param userid
     * @param companyid
     * @return array
     */
    public static function userConpanyProject($userid,$companyid){
        $result = PProjectUser::find()
                ->select('projectid')
                ->where(['userid'=>$userid,'companyid'=>$companyid,'show_feed'=>1])
                ->asarray()
                ->all();
        if(!empty($result)){
            foreach($result as $k=>$v){
                $projectid[] = $v['projectid'];
            }
        }else{
            $projectid = [];
        }
        return $projectid;
    }
    /**
     * 用户在企业中参加的项目个数
     * @param userid
     * @param companyid
     * @return integer
     */
    public static function userCompanyProjectNum($userid,$companyid){
        $count = PProjectUser::find()
                ->joinWith('pProject')
                ->select('p_project_user.id')
                ->where(['p_project_user.userid'=>$userid,'p_project_user.companyid'=>$companyid,'p_project.status'=>[1,2]])
                ->count();
        return $count;
    }
    /**
     * 用户在企业中管理的项目
     * @param userid
     * @param companyid
     * @return array
     */
    public static function userAdminCompanyProject($userid,$companyid){
        $result = PProjectUser::find()
                ->select('projectid')
                ->where(['userid'=>$userid,'companyid'=>$companyid,'permission'=>1])
                ->asArray()
                ->all();
        return $result;
    }
    /**
     * 项目中管理员个数
     * @param projectid
     * @return integer
     */
    public static function projectAdminNum($projectid){
        $count = PProjectUser::find()
                ->joinWith('pProject')
                ->where(['p_project_user.projectid'=>$projectid,'p_project_user.status'=>1,'p_project_user.permission'=>1,'p_project.status'=>[1,2,3]])
                ->count();
        return $count;
    }
    /**
     * 退出项目
     * @param userid
     * @param projectid
     * @return string
     */
    public static function exitProject($userid,$projectid){
        //用户是否为项目管理员
        $isadmin = PProjectUser::userInfo($userid,$projectid);
        if($isadmin['permission'] == 1){
            //项目中管理员数量
            $adminnum = PProjectUser::projectAdminNum($projectid);
            if($adminnum < 2){
                return false;
            }
        }
        //退出
        PProjectUser::deleteAll(['projectid'=>$projectid,'userid'=>$userid]);
        return true;
    }
    /**
     * 申请加入项目
     * @param userid
     * @param projectid
     * @return bool
     */
    public static function applyProject($userid,$projectid){
        //项目是否存在
        $project  = PProject::projectByid($projectid);
        //用户是否存在
        $user     = UUserInfo::userInfo($userid);
        if(empty($project) || empty($user)){
            return false;
        }
        //用户是否在项目中
        $ismember = PProjectUser::userInfo($userid,$projectid);
        if(!empty($ismember)){
            return false;
        }
        //项目的权限
        if($project['access_type'] == 1){
            //公开性：直接加入项目（邀请表中的可以show改为1）
            //用户所在企业
            $companyid = QCompanyUser::userInfo($userid)['companyid'];
            PProjectUser::addUser($userid,$projectid,2,$companyid);
            PProjectInvite::upShowByUserid($projectid,$userid);
        }else{
            //其他：加入申请表
            //是否申请过
            $isapply = PApplyToProject::applyingInfo($userid,$projectid);
            if(empty($isapply)){
                //添加
                PApplyToProject::addApply($userid,$projectid);
            }else{
                //修改
                PApplyToProject::upApply($isapply['id']);
            }
        }
        return true;
    }
    /**
     * 管理员-项目管理员列表
     * @param userid
     * @param companyid
     * @param page
     * @return array
     */
    public static function projectManager($companyid,$page){
        $start  = $page-1 <= 0 ? 0 : ($page-1) * 10;
        //项目管理员
        $admin  = PProjectUser::find()
                ->select('p_project_user.userid,s_username,s_avatar')
                ->joinWith('uUserInfo')
                ->joinWith('qCompanyUser')
                ->where(['p_project_user.permission'=>1,'p_project_user.status'=>1,'p_project_user.companyid'=>$companyid,'q_company_user.companyid'=>$companyid,'q_company_user.status'=>[2,3]])
                ->groupBy('p_project_user.userid,s_username,s_avatar')
                ->offset($start)
                ->limit(10)
                ->asArray()
                ->all();
        if(!empty($admin)){
            foreach($admin as $k=>$v){
                unset($admin[$k]['uUserInfo']);
                unset($admin[$k]['qCompanyUser']);
                //账户在企业中的状态
                $admin[$k]['status']  = QCompanyUser::userInfo($v['userid'])['status'];
                //管理的项目
                $admin[$k]['project'] = PProjectUser::userAdminCompanyProjectDetail($v['userid'],$companyid);
            }
        }
        $count  = PProjectUser::find()
                ->joinWith('qCompanyUser')
                ->select('p_project_user.userid')
                ->where(['p_project_user.permission'=>1,'p_project_user.status'=>1,'p_project_user.companyid'=>$companyid,'q_company_user.companyid'=>$companyid,'q_company_user.status'=>[2,3]])
                ->groupBy('p_project_user.userid')
                ->count();
        $data['data']  = $admin;
        $data['count'] = $count;
        return $data;
    }
    /**
     * 用户在企业中管理的项目
     * @param userid
     * @param companyid
     * @return array
     */
    public static function userAdminCompanyProjectDetail($userid,$companyid){
        $result = PProjectUser::find()
                ->joinWith('pProject')
                ->select('p_project_user.projectid,projectname')
                ->where(['p_project.status'=>[1,2],'p_project_user.userid'=>$userid,'p_project_user.companyid'=>$companyid,'p_project_user.permission'=>1,'p_project_user.status'=>1])
                ->asArray()
                ->all();
        if(!empty($result)){
            foreach($result as $k=>$v){
                unset($result[$k]['pProject']);
            }
        }
        return $result;
    }
    /**
     * 移除管理员
     * @param touserid
     * @param projectid
     * @return bool
     */
    public static function removeAdmin($touserid,$projectid){
        //项目里管理员个数
        $adminnum = PProjectUser::projectAdminNum($projectid);
        if($adminnum < 2){
            return false;
        }
        PProjectUser::updateAll(['permission'=>2],['userid'=>$touserid,'projectid'=>$projectid]);
        return true;
    }
    /**
     * 设置管理员
     * @param touserid
     * @param projectid
     * @return bool
     */
    public static function setAdmin($touserid,$projectid){
        PProjectUser::updateAll(['permission'=>1],['userid'=>$touserid,'projectid'=>$projectid]);
        return true;
    }
    /**
     * 项目成员列表
     * @param $projectid
     * @param $page
     * @param companyid
     * @param type
     * @return array
     */
    public static function userList($projectid,$page,$companyid,$type){
        if($type == 'admin'){
            $andwhere = ['p_project_user.permission'=>1];
        }else{
            $andwhere = ['p_project_user.permission'=>[1,2]];
        }
        $start  = $page-1 <= 0 ? 0 : ($page-1) * 10;
        $result = PProjectUser::find()
                ->joinWith('uUserInfo')
                ->select('p_project_user.userid,p_project_user.permission,p_project_user.companyid,s_username,s_avatar')
                ->where(['p_project_user.projectid'=>$projectid,'p_project_user.status'=>1])
                ->andWhere($andwhere)
                ->offset($start)
                ->limit(10)
                ->asarray()
                ->all();
        if(!empty($result)){
            foreach ($result as $k=>$v){
                unset($result[$k]['uUserInfo']);
                //企业名称
                $result[$k]['companyname'] = QCompany::companyInfo($v['companyid'])['name'];
            }
        }
        $count = PProjectUser::find()
                ->select('p_project_user.userid')
                ->where(['p_project_user.projectid'=>$projectid,'p_project_user.status'=>1])
                ->andWhere($andwhere)
                ->count();
        $data['data']  = $result;
        $data['count'] = $count;
        return $data;
    }
	/**
     * 用户参加的项目
     * @param $userid
     * @return array
     */
	public static function userProject($userid){
		$result = PProjectUser::find()
				->select('projectid')
				->where(['userid'=>$userid])
				->asarray()
				->all();
		if(empty($result)){
		    $pid = [];
        }else{
		    foreach($result as $k=>$v){
		        $pid[] = $v['projectid'];
            }
        }
		return $pid;
	}
    /**
     * 用户参加除公开的项目
     * @param $userid
     * @return array
     */
    public static function userProjectButPublic($userid){
        $result = PProjectUser::find()
                ->joinWith('pProject')
                ->select('p_project_user.projectid')
                ->where(['p_project_user.userid'=>$userid,'p_project.access_type'=>[2,3]])
                ->asarray()
                ->all();
        if(empty($result)){
            $pid = [];
        }else{
            foreach($result as $k=>$v){
                $pid[] = $v['projectid'];
            }
        }
        return $pid;
    }
    /**
     * 用户参加的公开项目
     * @param userid
     * @return array
     */
    public static function userProjectPublic($userid){
        $result = PProjectUser::find()
            ->joinWith('pProject')
            ->select('p_project_user.projectid')
            ->where(['p_project_user.userid'=>$userid,'p_project.access_type'=>['1']])
            ->asarray()
            ->all();
        if(empty($result)){
            $pid = [];
        }else{
            foreach($result as $k=>$v){
                $pid[] = $v['projectid'];
            }
        }
        return $pid;
    }
    /**
     * 关闭/开启项目动态消息
     * @param $userid
     * @param $projectid
     * @param $status
     * @return bool
     */
    public static function doProjectFeed($userid,$projectid,$status){
        PProjectUser::updateAll(['show_feed'=>$status],['userid'=>$userid,'projectid'=>$projectid]);
        return true;
    }
    /**
     * 项目quanbu成员
     * @param $projectid
     * @return array
     */
    public static function allMember($projectid){
        $result = PProjectUser::find()
            ->joinWith('uUserInfo')
            ->select('p_project_user.userid,s_username')
            ->where(['p_project_user.projectid'=>$projectid,'p_project_user.status'=>1])
            ->asarray()
            ->all();
        return $result;
    }
    /**
     * 快速访问列表
     * @param userid
     * @param type
     * @return array
     */
    public static function fastList($userid,$type=''){
        //置顶的
        $topdata = PProjectUser::find()
                ->joinWith('pProject')
                ->select('p_project_user.createtime,p_project.projectid,projectname,access_type,p_project.status,quick_status,access_time,top_time')
                ->where(['p_project.status'=>[1,2],'p_project_user.userid'=>$userid,'p_project_user.status'=>1,'quick_status'=>2])
                ->orderby(['p_project_user.top_time'=>SORT_DESC])
                ->asArray()
                ->all();
        $topnum = count($topdata);
        if($type != ''){
            return $topnum;
        }
        if($topnum > 20 || $topnum == 20){
            foreach ($topdata as $k=>$v){
                unset($topdata[$k]['pProject']);
            }
            return $topdata;
        }else{
            $num = 20-$topnum;
            $accessdata = PProjectUser::find()
                        ->joinWith('pProject')
                        ->select('p_project_user.createtime,p_project.projectid,projectname,access_type,p_project.status,quick_status,access_time,top_time')
                        ->where(['p_project.status'=>[1,2],'p_project_user.userid'=>$userid,'p_project_user.status'=>1,'quick_status'=>1])
                        ->orderby(['p_project_user.access_time'=>SORT_DESC])
                        ->limit($num)
                        ->asArray()
                        ->all();
            $result = array_merge_recursive($topdata,$accessdata);
            foreach ($result as $k=>$v){
                unset($result[$k]['pProject']);
            }
            return $result;
        }
    }
    /**
     * 操作快速访问列表
     * @param $userid
     * @param $do_data
     * @return bool
     */
    public static function doFast($userid,$do_data){
        //用户置顶的项目个数
        $num  = PProjectUser::fastList($userid,1);
        $data = json_decode($do_data,true);
        if(!empty($data)){
            foreach ($data as $k=>$v){
                if(in_array($v['status'],[1,2,3])){
                    #访问时间
                    if($v['status'] == 1){
                        $do = ['quick_status'=>$v['status'],'access_time'=>date('Y-m-d H:i:s')];
                    }
                    #置顶
                    if($v['status'] == 2 && $num < 20){
                        $do = ['quick_status'=>$v['status'],'top_time'=>date('Y-m-d H:i:s')];
                    }
                    #移除
                    if($v['status'] == 3){
                        $do = ['quick_status'=>$v['status'],'access_time'=>date('Y-m-d H:i:s')];
                    }
                    PProjectUser::updateAll($do,['userid'=>$userid,'projectid'=>$v['projectid']]);
                }
            }
            return true;
        }else{
            return false;
        }
    }
    /**
     * 项目中的所有成员
     * @param projectid
     * @return array
     */
    public static function allMemberNoJoin($projectid){
        $result = PProjectUser::find()
                ->select('userid')
                ->where(['projectid'=>$projectid,'status'=>1])
                ->asarray()
                ->all();
        $useridarr = array_column($result,'userid');
        $userid    = implode(',',$useridarr);
        return $userid;
    }
}
