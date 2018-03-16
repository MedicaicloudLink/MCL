<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "p_project_invite".
 *
 * @property integer $id
 * @property string $userid
 * @property string $companyid
 * @property string $projectid
 * @property string $tomail
 * @property string $tocompany
 * @property string $invitetime
 * @property integer $status
 * @property integer $show
 * @property string $touserid
 */
class PProjectInvite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'p_project_invite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invitetime'], 'safe'],
            [['status', 'show'], 'integer'],
            [['userid', 'companyid', 'projectid', 'tocompany','touserid'], 'string', 'max' => 11],
            [['tomail'], 'string', 'max' => 225],
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
            'companyid' => 'Companyid',
            'projectid' => 'Projectid',
            'tomail' => 'Tomail',
            'tocompany' => 'Tocompany',
            'invitetime' => 'Invitetime',
            'status' => 'Status',
            'show' => 'Show',
            'touserid'  => 'Touserid',
        ];
    }
    /**
     * 修改show的状态
     * @param companyid
     * @param mail
     * @return bool
     */
    public static function upShow($companyid,$mail){
        PProjectInvite::updateAll(['show'=>2],['tocompany'=>$companyid,'tomail'=>$mail]);
        return true;
    }
    /**
     * 修改show的状态
     * @param projectid
     * @param userid
     * @return bool
     */
    public static function upShowByUserid($projectid,$userid){
        PProjectInvite::updateAll(['show'=>2],['touserid'=>$userid,'projectid'=>$projectid]);
        return true;
    }
    /**
     * 是否已经在邀请表中
     * @param userid
     * @param projectid
     * @return array
     */
    public static function inviteInfo($userid,$projectid){
        $result = PProjectInvite::find()
                ->where(['touserid'=>$userid,'projectid'=>$projectid,'status'=>3])
                ->asarray()
                ->one();
        return $result;
    }
    /**
     * 是否已经在邀请表中
     * @param mail
     * @param projectid
     * @return array
     */
    public static function inviteInfoByMail($mail,$projectid){
        $result = PProjectInvite::find()
            ->where(['tomail'=>$mail,'projectid'=>$projectid,'status'=>3])
            ->one();
        return $result;
    }
    /**
     * 创建邀请
     * @param $userid
     * @param $projectid
     * @param $touserid
     * @param $companyid
     * @return bool
     */
    public static function addInvite($userid,$projectid,$touserid,$companyid){
        //用户是否为项目管理员
        $isadmin = PProjectUser::userInfo($userid,$projectid);
        if(empty($isadmin) || $isadmin['permission']!=1 || $isadmin['status'] == 2){
            return false;
        }
        $touserarr = explode(',',$touserid);
        if(empty($touserarr)){
            return false;
        }
        $_model    = new PProjectInvite();
        foreach($touserarr as $k=>$v){
            //是否已经是项目成员
            $ismember = PProjectUser::userInfo($v,$projectid);
            if(empty($ismember)){
                //是否已经在邀请表中
                $isinvite = PProjectInvite::inviteInfo($v,$projectid);
                if(empty($isinvite)){
                    #添加
                    $model = clone $_model;
                    $model -> userid     = $userid;
                    $model -> companyid  = $companyid;
                    $model -> projectid  = $projectid;
                    $model -> tomail     = UUserInfo::userInfo($v)['s_mail'];
                    $model -> touserid   = $v;
                    $model -> tocompany  = $companyid;
                    $model -> invitetime = date('Y-m-d H:i:s');
                    $model -> status     = 3;
                    $model -> show       = 1;
                    $model -> save();
                }else{
                    #修改
                    PProjectInvite::updateAll(['invitetime'=>date('Y-m-d H:i:s'),'userid'=>$userid],['touserid'=>$v,'projectid'=>$projectid,'status'=>3]);
                }
            }
            NNotice::addNotice($userid,$v,1,$projectid);
        }
        return true;
    }
    /**
     * 邀请不是企业成员的
     * @param $userid
     * @param $projectid
     * @param $touserarr
     * @param $companyid
     * @return bool
     */
    public static function addNoWorker($userid,$projectid,$touserarr,$companyid){
        //用户是否为项目管理员
        $isadmin = PProjectUser::userInfo($userid,$projectid);
        if(empty($isadmin) || $isadmin['permission']!=1 || $isadmin['status'] == 2){
            return false;
        }
        $_model = new PProjectInvite();
        foreach($touserarr as $k=>$v){
            //根据邮箱查出已经激活成功的用户id。
            $userinfo = QCompanyUser::userStatus($v['tomail']);
            if(!empty($userinfo) && $userinfo['status'] != 4 && $userinfo['status'] != 1){
                //注册过
                //是否在项目中
                $ismember = PProjectUser::userInfo($userinfo['userid'],$projectid);
                if(empty($ismember)){
                    //是否在邀请名单中
                    $isinvite = PProjectInvite::inviteInfo($userinfo['userid'],$projectid);
                    if(empty($isinvite)){
                        //添加
                        $model = clone $_model;
                        $model -> userid     = $userid;
                        $model -> companyid  = $companyid;
                        $model -> projectid  = $projectid;
                        $model -> tomail     = $v['tomail'];
                        $model -> touserid   = $userinfo['userid'];
                        $model -> tocompany  = $userinfo['companyid'];
                        $model -> invitetime = date('Y-m-d H:i:s');
                        $model -> status     = 3;
                        $model -> show       = 1;
                        $model ->save();
                    }else{
                        //修改
                        PProjectInvite::updateAll(['invitetime'=>date('Y-m-d H:i:s'),'userid'=>$userid],['touserid'=>$userinfo['userid'],'projectid'=>$projectid,'status'=>3]);
                    }
                    NNotice::addNotice($userid,$userinfo['userid'],1,$projectid);
                }
            }else{
                //没注册过  是否在邀请名单中
                $isinvite = PProjectInvite::inviteInfoByMail($v['tomail'],$projectid);
                if(empty($isinvite)){
                    //添加
                    $model = clone $_model;
                    $model -> userid     = $userid;
                    $model -> companyid  = $companyid;
                    $model -> projectid  = $projectid;
                    $model -> tomail     = $v['tomail'];
                    $model -> touserid   = "";
                    $model -> tocompany  = "";
                    $model -> invitetime = date('Y-m-d H:i:s');
                    $model -> status     = 3;
                    $model -> show       = 1;
                    $model ->save();
                }else{
                    //修改
                    PProjectInvite::updateAll(['invitetime'=>date('Y-m-d H:i:s'),'userid'=>$userid],['tomail'=>$v['tomail'],'projectid'=>$projectid,'status'=>3]);
                }
            }


        }
        return true;
    }
    /**
     * 修改touserid为空的值
     * @param mail
     * @param userid
     * @param companyid
     * @return bool
     */
    public static function upTouserid($mail,$userid,$companyid){
        $result = PProjectInvite::find()
                ->where(['tomail'=>$mail])
                ->asarray()
                ->all();
        if(!empty($result)){
            foreach($result as $k=>$v){
                if($v['touserid'] == ''){
                    PProjectInvite::updateAll(['touserid'=>$userid,'tocompany'=>$companyid],['id'=>$v['id']]);
                    NNotice::addNotice($v['userid'],$userid,1,$v['projectid']);
                }
            }
        }
        return true;
    }
    /**
     * 邀请信息
     * @param inviteid
     * @return array
     */
    public static function infoById($inviteid){
        $result = PProjectInvite::find()
                ->where(['id'=>$inviteid,'status'=>3])
                ->asArray()
                ->one();
        return $result;
    }
    /**
     * 处理邀请
     * @param userid
     * @param type
     * @param inviteid
     * @return bool
     */
    public static function doInvite($userid,$type,$inviteid){
        //邀请信息
        $inviteinfo = PProjectInvite::infoById($inviteid);
        if(empty($inviteinfo)){
            return false;
        }
        if($userid != $inviteinfo['touserid']){
            return false;
        }
        if($type == 2){
            #拒绝
            PProjectInvite::updateAll(['status'=>2],['id'=>$inviteid]);
        }
        if($type == 1){
            #同意加入
            PProjectInvite::updateAll(['status'=>1],['id'=>$inviteid]);
            #1.加入项目
            PProjectUser::addUser($userid,$inviteinfo['projectid'],2,$inviteinfo['tocompany']);
            #2.删除申请
            PApplyToProject::deleteAll(['userid'=>$userid,'projectid'=>$inviteinfo['projectid']]);
        }
        return true;
    }
}
