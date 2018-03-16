<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "q_company_user".
 *
 * @property integer $id
 * @property string $userid
 * @property string $mail
 * @property string $companyid
 * @property string $inviter
 * @property integer $isadmin
 * @property integer $status
 * @property string $invitetime
 * @property string $activetime
 * @property integer $permission
 * @property string $admintime
 * @property string $upstatustime
 * @property integer $isauthorize
 */
class QCompanyUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'q_company_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['isadmin', 'status', 'permission','isauthorize'], 'integer'],
            [['invitetime', 'activetime','admintime','upstatustime'], 'safe'],
            [['permission'], 'required'],
            [['userid', 'companyid', 'inviter'], 'string', 'max' => 11],
            [['mail'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'mail' => 'Mail',
            'companyid' => 'Companyid',
            'inviter' => 'Inviter',
            'isadmin' => 'Isadmin',
            'status' => 'Status',
            'invitetime' => 'Invitetime',
            'activetime' => 'Activetime',
            'permission' => 'Permission',
            'admintime' => 'Admintime',
            'upstatustime' => 'Upstatustime',
            'isauthorize' => 'Isauthorize'
        ];
    }
    /**
     * 关联用户基本信息表
     */
    public function getUUserInfo(){
        return $this->hasMany(UUserInfo::className(), ['s_userid'  => 'userid']);
    }
    /**
     * 用户在企业中身份
     * @param userid
     * @param companyid
     * @return array
     */
    public static function userPermission($userid,$companyid){
        $info = QCompanyUser::find()
        ->select('permission,isauthorize')
        ->where(['userid'=>$userid,'companyid'=>$companyid,'status'=>2])
        ->asarray()
        ->one();
        return $info;
    }
    /**
     * 用户状态
     * @param mail
     * @return array
     */
    public static function userStatus($mail){
        $info = QCompanyUser::find()
              ->select('userid,mail,companyid,status')
              ->where(['mail'=>$mail])
              ->asarray()
              ->one();
        return $info;
    }
    /**
     * 用户状态
     * @param mail
     * @return array
     */
    public static function userInfoBymail($mail){
        $info = QCompanyUser::find()
            ->select('userid,mail,companyid,status')
            ->where(['mail'=>$mail])
            ->andWhere(['in','status',[2,3]])
            ->asarray()
            ->one();
        return $info;
    }
    /**
     * 用户所有状态
     * @param mail
     * @return array
     */
    public static function userStatusInAllCompany($mail){
        $result = QCompanyUser::find()
                ->select('userid,mail,companyid,status')
                ->where(['mail'=>$mail])
                ->andWhere(['in','status',[2,3]])
                ->asarray()
                ->all();
        $status    = [];
        $companyid = '';
        if(!empty($result)){
            foreach($result as $k=>$v){
                $status[] = $v['status'];
                if($v['status'] == 2){
                    $companyid = $v['companyid'];
                }
            }
        }
        $data['status']    = $status;
        $data['companyid'] = $companyid;
        return $data;
    }
    /**
     * 用户信息
     * @param userid
     * @return array
     */
    public static function userInfo($userid){
        $info = QCompanyUser::find()
        ->select('userid,mail,companyid,status')
        ->where(['userid'=>$userid])
        ->asarray()
        ->one();
        return $info;
    }
    /**
     * 用户在企业中的状态
     * @param userid
     * @param companyid
     * @return array
     */
    public static function userCompanyStatus($userid,$companyid){
        $info = QCompanyUser::find()
            ->select('userid,mail,companyid,status')
            ->where(['userid'=>$userid,'companyid'=>$companyid])
            ->andWhere(['in','status',[1,2,3]])
            ->asarray()
            ->one();
        return $info;
    }
    /**
     * 用户邮箱在企业中的状态
     * @param mail
     * @param companyid
     * @return array
     */
    public static function mailCompanyStatus($mail,$companyid){
        $info = QCompanyUser::find()
            ->select('userid,mail,companyid,status')
            ->where(['mail'=>$mail,'companyid'=>$companyid])
            ->andWhere(['in','status',[2,3]])
            ->asarray()
            ->one();
        return $info;
    }
    /**
     * 删除未激活
     * @param mail
     * @return bool
     */
    public static function delUser($mail){
        QCompanyUser::deleteAll(['mail'=>$mail,'status'=>1]);
        return true;
    }
    /**
     * 删除未激活
     * @param mail
     * @param userid
     * @return bool
     */
    public static function delUser2($mail,$userid){
        QCompanyUser::deleteAll('userid != :userid AND mail = :mail AND status = :status', ['mail'=>$mail,'userid'=>$userid,'status'=>1]);
        return true;
    }
    /**
     * 创建
     * @param userid
     * @param companyid
     * @param mail
     * @param role（1.管理员 2.普通成员）
     * @param status用户状态（2.正常）
     * @return bool
     */
    public static function createUser($createuser,$companyid,$mail,$role,$status){
        //删除以前邀请记录
        QCompanyUser::delUser($mail);
        //修改邀请加入项目的表中touserid
        PProjectInvite::upTouserid($mail,$createuser,$companyid);
        if(empty(QCompanyUser::userInfo($createuser))){
            $model = new QCompanyUser();
            $model -> userid     = $createuser;
            $model -> mail       = $mail;
            $model -> companyid  = $companyid;
            $model -> status     = $status;
            $model -> permission = $role;
            $model -> activetime = date('Y-m-d H:i:s');
            $model -> upstatustime = date('Y-m-d H:i:s');
            if($role == 1){
                $model -> admintime   = date('Y-m-d H:i:s');
                $model -> isauthorize = 1;
            }
            if($model ->save()){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }
    /**
     * 创建
     * @param data
     * @param role
     * @return string
     */
    public static function createUser2($data,$role){
        //删除以前邀请记录  
        QCompanyUser::delUser2($data['mail'],$data['userid']);
        $model = QCompanyUser::findOne(['userid'=>$data['userid']]);
        $model -> mail       = $data['mail'];
        //企业id是否不一样，不一样就是自己进来的
        if($model->companyid != $data['companyid']){
            $model -> isadmin = 3;
        }
        $model -> companyid    = $data['companyid'];
        $model -> status       = 2;
        $model -> permission   = $role;
        $model -> activetime   = date('Y-m-d H:i:s');
        $model -> upstatustime = date('Y-m-d H:i:s');
        $company = QCompany::companyInfo($data['companyid']);
        if($model-> isadmin == 1){
            $model -> isauthorize = 1;
        }else{
            $model -> isauthorize  = QCompanyUser::isAuthorize($company,$data['mail']);
        }
        if($role == 1){
            $model -> admintime = date('Y-m-d H:i:s');
        }
        if($model ->save()){
            //加入用户表 ，基本信息表
            UUser::createUser($data['userid'], $data);
            UUserInfo::createUser($data['userid'], $data);
            //修改邀请加入项目的表中touserid
            PProjectInvite::upTouserid($data['mail'],$data['userid'],$data['companyid']);
            //登录
            $info['token']     = UUser::login($data['userid'], $data['password'])['token'];
            $info['mail']      = $data['mail'];
            $info['name']      = $data['username'];
            $info['companyid'] = $data['companyid'];
            $info['userid']    = $data['userid'];
            $info['type']      = 2;//个人用户
            //直接登录
            UUser::login($data['userid'], $data['password']);
            return $info;
        }else{
            return false;
        }
    }
    /**
     * 创建邀请
     * @param userid
     * @param $companyid
     * @param $inviteinfo
     * @param $type
     * @return array
     */
    public static function createInvite($userid,$companyid,$inviteinfo,$type,$flag=''){
        //邮箱是否已经被占用
        if($flag == ''){
            $invitearr = json_decode($inviteinfo,true);
        }
        //企业信息
        $company   = QCompany::companyInfo($companyid);
        $userinfo  = UUserInfo::userInfo($userid);
        $sendmail  = Yii::$app->params['loginmail'];
//        if($type == 1){
//            #企业
//            $subject = $company['name'].'发送的梅地卡尔临床云帐户激活邀请';
//            $path    = '/mail/company_invite_mail';
//        }else{
            #个人
            $subject = '快来一起工作！'.$userinfo['s_username'].'邀请您注册梅地卡尔临床数据云';
            $path    = '/mail/worker_invite_mail';
//        }
        $_model    = new QCompanyUser();
        $_mmodel   = new EMail();
        $_umodel   = new UUserInfo();
        if($flag == 'excel'){
            $invitearr = $inviteinfo;
        }
        //发送总的数量
        $count   = 0;
        //成功的数量
        $succnum = 0;
        foreach($invitearr as $k=>$v){
            if($v['mail'] != ''){
                $mmodel   = clone $_mmodel;
                $mmodel   -> id       = Commonfun::randpw();
                $mmodel   -> to_mail  = $v['mail'];
                $mmodel   -> sendtime = date('Y-m-d H:i:s');
                $mmodel   -> offtime  = date('Y-m-d H:i:s',time()+86400*Yii::$app->params['invitetime']);
                //此邮箱是否已经注册并且激活
                $user = QCompanyUser::userAllStatus($v['mail'],$companyid);
                $model    = clone $_model;
                $model    -> userid       = Commonfun::randpw();
                $model    -> inviter      = $userid;
                $model    -> companyid    = $companyid;
                $model    -> isadmin      = $type;
                $model    -> mail         = $v['mail'];
                $model    -> status       = 1;
                $model    -> invitetime   = date('Y-m-d H:i:s');
                $model    -> upstatustime = date('Y-m-d H:i:s');
                $model    -> permission   = 2;
                if($type == 2){
                    $model->isauthorize   = QCompanyUser::isAuthorize($company,$v['mail']);
                }
                if(empty($user)){
                    $umodel = clone $_umodel;
                    $umodel -> s_userid   = $model -> userid;
                    $umodel -> s_mail     = $v['mail'];
                    $umodel -> s_username = $v['name'];
                    if($flag == 'excel'){
                        $umodel -> s_cellphone  = $v['phone'];
                        $umodel -> s_department = $v['department'];
                        $umodel -> s_job        = $v['job'];
                    }
                    $umodel   -> save();
                    $model    -> save();
                    $succnum++;
                }
                $mmodel   -> save();
                $count++;
//                if($type == 1){
//                    #企业
//                    $content  = ['url'=>"".$company['domain'].".".Yii::$app->params['domainurl']."/company/company?company_id=".$companyid."&mailid=".$mmodel->id."&userid=".$model->userid."",'name'=>$company['name']];
//                }else{
                    #个人
                    $content  = ['url'=>"".$company['domain'].".".Yii::$app->params['domainurl']."/company/company?company_id=".$companyid."&mailid=".$mmodel->id."&userid=".$model->userid."",'name'=>$userinfo['s_username']];
//                }
                if($v['mail'] != ''){
                    Commonfun::sendMail($sendmail,$v['mail'],$subject,$path,$content);
                }
            }
        }
        $result['count']     = $count;
        $result['succcount'] = $succnum;
        return $result;
    }

    /**
     * @param $userid
     * @param $company
     * @param $mail
     * @param $type
     * @param $touserid
     * @return bool
     */
    public static function reInvite($userid,$touserid,$company,$mail,$type){
        //修改用户在企业中的邮箱
        QCompanyUser::updateAll(['mail'=>$mail,'invitetime'=>date('Y-m-d H:i:s')],['companyid'=>$company['id'],'userid'=>$touserid]);
        UUserInfo::updateAll(['s_mail'=>$mail,'s_update_time'=>date('Y-m-d H:i:s')],['s_userid'=>$touserid]);
        $mmodel   = new EMail();
        $mmodel   -> id       = Commonfun::randpw();
        $mmodel   -> to_mail  = $mail;
        $mmodel   -> sendtime = date('Y-m-d H:i:s');
        $mmodel   -> offtime  = date('Y-m-d H:i:s',time()+86400*Yii::$app->params['invitetime']);
        $mmodel   -> save();
        $userinfo = UUserInfo::userInfo($userid);
//        if($type == 1){
//            #企业
//            $subject = $company['name'].'发送的梅地卡尔临床云帐户激活邀请';
//            $path    = '/mail/company_invite_mail';
//            $content  = ['url'=>"".$company['domain'].".".Yii::$app->params['domainurl']."/company/company?company_id=".$company['id']."&mailid=".$mmodel->id."&userid=".$touserid."",'name'=>$company['name']];
//        }else{
            #个人
            $subject = '快来一起工作！'.$userinfo['s_username'].'邀请您注册梅地卡尔临床数据云';
            $path    = '/mail/worker_invite_mail';
            $content  = ['url'=>"".$company['domain'].".".Yii::$app->params['domainurl']."/company/company?company_id=".$company['id']."&mailid=".$mmodel->id."&userid=".$touserid."",'name'=>$userinfo['s_username']];
//        }
        if($mail != ''){
            Commonfun::sendMail(Yii::$app->params['loginmail'],$mail,$subject,$path,$content);
        }
        return true;
    }
    /**
     * 企业的管理员
     * @param companyid
     * @return array
     */
    public static function companyAdmins($companyid){
        $result = QCompanyUser::find()
                ->select('userid')
                ->where(['companyid'=>$companyid,'permission'=>1,'status'=>2])
                ->asArray()
                ->all();
        return $result;
    }
    /**
     * 企业内用户列表
     * @param companyid
     * @param page
     * @param type(all,admin)
     * @param search
     * @return array
     */
    public static function userList($companyid,$page,$type,$search){
        $start  = $page-1 <= 0 ? 0 : ($page-1) * 10;
        //全部成员
        if($type == 'all'){
            $where = ['companyid'=>$companyid,'status'=>[1,2,3]];
        }
        //管理员
        if($type == 'admin'){
            $where = ['companyid'=>$companyid,'status'=>[1,2,3],'permission'=>1];
        }
        if($search != ''){
            $andwhere = ['like','u_user_info.s_username',$search];
        }else{
            $andwhere = [];
        }
        $result = QCompanyUser::find()
                ->joinWith('uUserInfo')
                ->select('userid,isadmin,status,invitetime,activetime,admintime,s_username,s_mail,s_avatar')
                ->where($where)
                ->andWhere($andwhere)
                ->orderBy(['status'=>SORT_DESC,'permission'=>SORT_ASC,'isadmin'=>SORT_ASC,'invitetime'=>SORT_DESC])
                ->offset($start)
                ->limit(10)
                ->asarray()
                ->all();
        if(!empty($result)){
            foreach($result as $k=>$v){
                unset($result[$k]['uUserInfo']);
                //参与项目
                $result[$k]['projectnum'] = PProjectUser::userCompanyProjectNum($v['userid'],$companyid);
            }
        }
        $count  = QCompanyUser::find()
                ->joinWith('uUserInfo')
                ->select('userid')
                ->where($where)
                ->andWhere($andwhere)
                ->count();
        $data['data']  = $result;
        $data['count'] = $count;
        return $data;
    }
    /**
     * 设置用户状态
     * @param userid
     * @param touserid
     * @param status
     * @param companyid
     * @return bool
     */
    public static function doCompanyUser($userid,$touserid,$status,$companyid){
        //是否为企业管理员
        $isadmin = QCompanyUser::userPermission($userid,$companyid);
        if($isadmin['permission'] != 1){
            return false;
        }
        //用户是否在企业中(未删除的)
        $usercompanystatus = QCompanyUser::userCompanyStatus($touserid,$companyid);
        if(empty($usercompanystatus)){
            return false;
        }

        if($status == 3 || $status == 4 ){
            //判断是否为项目管理员
            //用户管理的项目
            $adminproject = PProjectUser::userAdminCompanyProject($touserid,$companyid);
            if(!empty($adminproject)){
                foreach($adminproject as $k=>$v){
                    //每个项目中有几个管理员
                    if(PProjectUser::projectAdminNum($v['projectid']) == 1){
                        #项目中需要指定其他管理员
                        return 'needadmin';
                    }
                }
            }
            if($status == 4){
                $mail = UUserInfo::userInfo($touserid)['s_mail'];
                PProjectInvite::upShow($companyid,$mail);
                PProjectUser::deleteAll(['userid'=>$touserid]);
                EMail::deleteAll(['to_mail'=>$mail]);
            }
            //删除登录表中内容
            UUserLogin::deleteUser($touserid);
        }

        QCompanyUser::updateAll(['status'=>$status,'upstatustime'=>date('Y-m-d H:i:s')],['userid'=>$touserid,'companyid'=>$companyid]);
        return 'ok';
    }
    /**
     * 设置/取消企业管理员
     * @param userid
     * @param touserid
     * @param permission
     * @param companyid
     * @return bool
     */
    public static function setAdmin($userid,$touserid,$permission,$companyid){
        //用户是否为企业管理员
        $isadmin = QCompanyUser::userPermission($userid,$companyid);
        if($isadmin['permission'] != 1){
            return 'nopermission';
        }
        if($permission == 1){
            //查看是否已经是管理员
            $toadmin = QCompanyUser::userPermission($touserid,$companyid);
            if($toadmin['permission'] == 1){
                return 'alreadyadmin';
            }
            QCompanyUser::updateAll(['permission'=>1,'admintime'=>date('Y-m-d H:i:s')],['userid'=>$touserid,'companyid'=>$companyid]);
        }else{
            //企业管理员个数
            $companyadmin = QCompanyUser::companyAdmins($companyid);
            $cadmincount  = count($companyadmin);
            if($cadmincount == 1){
                return 'nocadmin';
            }
            //用户管理的项目
            $adminproject = PProjectUser::userAdminCompanyProject($touserid,$companyid);
            if(!empty($adminproject)){
                foreach($adminproject as $k=>$v){
                    //每个项目中有几个管理员
                    if(PProjectUser::projectAdminNum($v['projectid']) == 1){
                        #项目中需要指定其他管理员
                        return 'nopadmin';
                    }
                }
            }
            QCompanyUser::updateAll(['permission'=>2],['userid'=>$touserid,'companyid'=>$companyid]);
        }
        return 'ok';
    }
    /**
     * 用户动态
     * @param userid
     * @param companyid
     * @return array
     */
    public static function userDynamic($userid,$companyid){
        //用户是否为企业管理员
        $isadmin = QCompanyUser::userPermission($userid,$companyid);
        if(empty($isadmin) || $isadmin['permission'] == 2){
            return 'nopermission';
        }
        $result = QCompanyUser::find()
                ->joinWith('uUserInfo')
                ->select('userid,upstatustime,status,s_username,s_avatar')
                ->where(['status'=>[2,3],'companyid'=>$companyid])
                ->orderBy(['upstatustime'=>SORT_DESC])
                ->limit(5)
                ->asArray()
                ->all();
        if(!empty($result)){
            foreach($result as $k=>$v){
                unset($result[$k]['uUserInfo']);
            }
        }
        return $result;
    }
    /**
     * 用户在企业中所有状态
     * @param mail
     * @param companyid
     * @return array
     */
    public static function userAllStatus($mail,$companyid){
        $result = QCompanyUser::find()
                ->where(['mail'=>$mail,'companyid'=>$companyid,'status'=>[2,3]])
                ->asArray()
                ->all();
        $status = [];
        if(!empty($result)){
            foreach($result as $k=>$v){
                $status[] = $v['status'];
            }
        }
        return $status;
    }
    /**
     * 创建用户是判断批准字段
     * @param company企业信息
     * @param mail邮箱
     * @return integer
     */
    public static function isAuthorize($company,$mail){
        //企业信息-非管理员邀请是否需要审核
        if($company['isinviteagree'] == 2){
            $isauthorize = 1;
        }else{
            //邮箱是否可以自动加入
            if($company['isagree'] == 2){
                $isauthorize = 2;
            }else{
                //邮箱后缀是否在企业已经验证的后缀里
                $company_finish = json_decode($company['finish_mail'],true);
                if(!empty($company_finish)){
                    foreach($company_finish as $key=>$val){
                        $finish[] = $val['mail'];
                    }
                    //邮箱后缀名
                    $mailarr = explode('@',$mail);
                    $houzhui = $mailarr[1];
                    if(in_array($houzhui,$finish)){
                        $isauthorize = 1;
                    }else{
                        $isauthorize = 2;
                    }
                }else{
                    $isauthorize = 2;
                }
            }
        }
        return $isauthorize;
    }
    /**
     * 申请列表
     * @param $companyid
     * @param $page
     * @return array
     */
    public static function applyUser($companyid,$page){
        $start  = $page-1 <= 0 ? 0 : ($page-1) * 10;
        $result = QCompanyUser::find()
                ->joinWith('uUserInfo')
                ->select('s_username,userid,invitetime,mail,inviter,isadmin')
                ->where(['isauthorize'=>2,'companyid'=>$companyid,'status'=>2])
                ->orderBy(['invitetime'=>SORT_DESC])
                ->offset($start)
                ->limit(10)
                ->asArray()
                ->all();
        foreach ($result as $k=>$v){
            unset($result[$k]['uUserInfo']);
            if($v['inviter'] != ''){
                $result[$k]['invitename'] = UUserInfo::userInfo($v['inviter'])['s_username'];
            }
        }
        $count  = QCompanyUser::find()
                ->select('userid')
                ->where(['isauthorize'=>2,'companyid'=>$companyid,'status'=>2])
                ->count();
        $data['data']  = $result;
        $data['count'] = $count;
        return $data;
    }
    /**
     * 处理申请
     * @param touserid
     * @param status
     * @param type
     * @param companyid
     * @return bool
     */
    public static function doApplyUser($companyid,$status,$touserid,$type){
        if($type == 'all'){
            if($status == 1){
                QCompanyUser::updateAll(['isauthorize'=>1],['companyid'=>$companyid,'isauthorize'=>2]);
            }else{
                QCompanyUser::deleteAll(['companyid'=>$companyid,'isauthorize'=>2]);
            }
        }else{
            if($status == 1){
                QCompanyUser::updateAll(['isauthorize'=>1],['userid'=>$touserid,'isauthorize'=>2]);
            }else{
                QCompanyUser::deleteAll(['userid'=>$touserid,'isauthorize'=>2]);
            }
        }
        return true;
    }
    /**
     * 企业内所有同事（未删除的）
     * @param userid
     * @param page
     * @return array
     */
    public static function allContact($userid,$page){
        //用户所在企业
        $cid    = QCompanyUser::userInfo($userid)['companyid'];
        $start  = $page-1 <= 0 ? 0 : ($page-1) * 15;
//        $result = QCompanyUser::find()
//                ->joinWith('uUserInfo')
//                ->select('q_company_user.userid,s_username,s_mail,s_cellphone,s_avatar')
//                ->where(['status'=>[2,3],'companyid'=>$cid])
//                ->andWhere(['<>','q_company_user.userid',$userid])
//                ->offset($start)
//                ->limit(15);
//        echo $result->createCommand()->getRawSql();exit;
//                ->asArray()
//                ->all();
        $result = Yii::$app->db->createCommand("SELECT
	q_company_user.userid,
	s_username,
	s_mail,
	s_cellphone,
	s_avatar
FROM
	q_company_user
LEFT JOIN u_user_info ON q_company_user.userid = u_user_info.s_userid
WHERE
	(
		(status IN(2, 3))
		AND (companyid = '$cid')
	)
AND (
	q_company_user.userid <> '$userid'
)
order by convert_to(s_username, 'GBK') 
LIMIT 15 offset $start ")->queryAll();
        foreach ($result as $k=>$v){
            unset($result[$k]['uUserInfo']);
            //是否为联系人
            $contact = ContactPerson::contactInfo($userid,$v['userid']);
            if(!empty($contact)){
                $result[$k]['iscontact'] = 1; #是联系人
                $result[$k]['tagid']     = $contact['tagid'];
                $result[$k]['tagname']   = ContactTag::tagInfo($contact['tagid'])['tagname'];
            }else{
                $result[$k]['iscontact'] = 2; #不是联系人
                $result[$k]['tagid']     = "";
                $result[$k]['tagname']   = "";
            }
            $result[$k]['touserid'] = $result[$k]['userid'];
            unset($result[$k]['userid']);
        }
        $count = QCompanyUser::find()
                ->select('userid')
                ->where(['status'=>[2,3],'companyid'=>$cid])
                ->andWhere(['<>','userid',$userid])
                ->count();
        $data['data']  = $result;
        $data['count'] = $count;
        return $data;
    }
}
