<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "p_apply_to_project".
 *
 * @property integer $id
 * @property string $userid
 * @property string $projectid
 * @property integer $status
 * @property string $applytime
 * @property string $admin
 * @property string $admintime
 * @property string $companyid
 */
class PApplyToProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'p_apply_to_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['applytime','admintime'], 'safe'],
            [['userid', 'projectid', 'admin','companyid'], 'string', 'max' => 11],
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
            'projectid' => 'Projectid',
            'status' => 'Status',
            'applytime' => 'Applytime',
            'admin' => 'Admin',
            'admintime' => 'Admintime',
            'companyid' => 'Companyid',
        ];
    }
    /**
     * 关联用户表
     */
    public function getUUserInfo(){
        return $this->hasMany(UUserInfo::className(), ['s_userid'  => 'userid']);
    }
    /**
     * 申请记录
     * @param userid
     * @param projectid
     * @return array
     */
    public static function applyInfo($userid,$projectid){
        $result = PApplyToProject::find()
                ->where(['userid'=>$userid,'projectid'=>$projectid])
                ->andWhere(['in','status',[1,3]])
                ->one();
        return $result;
    }
    /**
     * 申请中没审核的
     * @param userid
     * @param projectid
     * @return array
     */
    public static function applyingInfo($userid,$projectid){
        $result = PApplyToProject::find()
            ->where(['userid'=>$userid,'projectid'=>$projectid,'status'=>1])
            ->one();
        return $result;
    }
    /**
     * 加入申请表
     * @param userid
     * @param projectid
     * @return bool
     */
    public static function addApply($userid,$projectid){
        $project = PProject::projectByid($projectid);
        $model = new PApplyToProject();
        $model -> userid    = $userid;
        $model -> projectid = $projectid;
        $model -> companyid = $project['companyid'];
        $model -> status    = 1;
        $model -> applytime = date('Y-m-d H:i:s');
        $model -> admin     = '';
        $model -> save();
        return true;
    }
    /**
     * 修改申请
     * @param userid
     * @param projectid
     * @return bool
     */
    public static function upApply($applyid){
        PApplyToProject::updateAll(['applytime'=>date('Y-m-d H:i:s')],['id'=>$applyid]);
        return true;
    }
    /**
     * 请求列表
     * @param userid
     * @param projectid
     * @param page
     * @return string
     */
    public static function applyList($userid,$projectid,$page){
        //用户是否为项目管理员
        $isadmin = PProjectUser::userInfo($userid,$projectid);
        if(empty($isadmin) || $isadmin['permission'] == 2){
            return false;
        }
        $start  = $page-1 <= 0 ? 0 : ($page-1) * 15;
        $result = PApplyToProject::find()
                ->joinWith('uUserInfo')
                ->select('id,userid,applytime,s_username,s_avatar')
                ->where(['projectid'=>$projectid,'status'=>1])
                ->orderBy(['applytime'=>SORT_DESC])
                ->offset($start)
                ->limit(15)
                ->asarray()
                ->all();
        foreach($result as $k=>$v){
            unset($result[$k]['uUserInfo']);
            //用户企业名称
            $result[$k]['companyid']   = QCompanyUser::userInfo($v['userid'])['companyid'];
            $result[$k]['companyname'] = QCompany::companyInfo($result[$k]['companyid'])['name'];
        }
        $count  = PApplyToProject::find()
                ->select('id')
                ->where(['projectid'=>$projectid,'status'=>1])
                ->count();
        $data['data']  = $result;
        $data['count'] = $count;
        return $data;
    }
    /**
     * 处理申请
     * @param $userid
     * @param $touserid
     * @param $projectid
     * @param $status
     * @param $applyid
     * @return bool
     */
    public static function doApply($userid,$touserid,$projectid,$status,$applyid){
        if($status == 4){
            //删除
            PApplyToProject::deleteAll(['id'=>$applyid]);
        }
        PApplyToProject::updateAll(['status'=>$status,'admin'=>$userid,'admintime'=>date('Y-m-d H:i:s')],['id'=>$applyid]);
        if($status == 2){
            //邀请表中改值
            PProjectInvite::upShowByUserid($projectid,$touserid);
            //用户所在企业
            $companyid = QCompanyUser::userInfo($touserid)['companyid'];
            //项目中添加新成员
            PProjectUser::addUser($touserid,$projectid,2,$companyid);
        }
        return true;
    }
    /**
     * 处理全部
     * @parma userid
     * @param $projectid
     * @param $status
     * @return bool
     */
    public static function doAll($userid,$projectid,$status){
        //未处理的请求
        $result = PApplyToProject::find()
                ->where(['projectid'=>$projectid,'status'=>1])
                ->asarray()
                ->all();
        if(!empty($result)){
            foreach($result as $k=>$v){
                PApplyToProject::updateAll(['status'=>$status,'admin'=>$userid,'admintime'=>date('Y-m-d H:i:s')],['id'=>$v['id']]);
                if($status == 2){
                    //邀请表中改值
                    PProjectInvite::upShowByUserid($projectid,$v['userid']);
                    //用户所在企业
                    $companyid = QCompanyUser::userInfo($v['userid'])['companyid'];
                    //项目中添加新成员
                    PProjectUser::addUser($v['userid'],$projectid,2,$companyid);
                }
            }
        }
        return true;
    }
}
