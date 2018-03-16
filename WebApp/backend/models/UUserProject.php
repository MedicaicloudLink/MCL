<?php

namespace app\models;

use Yii;
use app\models\UProjectdata;
use app\models\UCenterUser;
use app\models\UUserTask;
use app\models\UProjectTask;
/**
 * This is the model class for table "u_user_project".
 *
 * @property string $u_projectID
 * @property string $u_userID
 * @property string $u_permission
 */
class UUserProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_user_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_createtime', 'u_projectID', 'u_permission', 'u_userID', 'u_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'u_projectID' => 'U Project ID',
            'u_userID' => 'U User ID',
            'u_permission' => 'U Permission',
        ];
    }

    /**
     * @todo 关联项目表
     */
    public function getUProjectdata()
    {
        return $this->hasMany(UProjectdata::className(), ['u_projectID' => 'u_projectID']);
    }

    /**
     * @todo 关联用户表
     */
    public function getMUserinfo()
    {
        return $this->hasMany(MUserinfo::className(), ['s_userid' => 'u_userID']);
    }

    /**
     * @todo 关联中心_用户表
     */
    public function getUCenterUser()
    {
        return $this->hasMany(UCenterUser::className(), ['u_userid' => 'u_userID', 'u_projectid' => 'u_projectID']);
    }

    /**
     * @todo 获取用户参与的项目
     * @param userid
     * Date 2017-02-04
     */
    public static function getProgects($userid)
    {
        $projects = UUserProject::find()
            ->select('u_projectdata.u_projectID,u_projectdata.u_projectName,u_projectdata.u_projectMem,u_projectdata.u_projectstate')
            ->joinWith('uProjectdata')
            ->where(['u_userID' => $userid])
            ->asArray()
            ->all();
        foreach ($projects as $key => $val) {
            unset($projects[$key]['uProjectdata']);
        }
        return $projects;
    }

    /**
     * @todo 获取用户在项目中的权限
     * @param userId
     * @param project
     * @return u_permission
     */
    public static function getPermitInProject($userId, $project)
    {
        $permit = UUserProject::find()
            ->select("u_permission")
            ->where(["u_projectID" => $project, "u_userID" => $userId])
            ->asArray()
            ->all();

        foreach ($permit as $key => $val) {
            return $val["u_permission"];
        }
    }

    /**
     * @todo 查找项目所有成员
     * @param $projectId
     * @return $memberList 成员列表
     */
    public static function findMemberListByProjectId($projectId)
    {
        $member = UUserProject::find()
                ->joinWith('mUserinfo')
                ->select('u_user_project.*,m_userinfo.s_username')
                ->where(['u_user_project.u_projectID'=>$projectId])
                ->asarray()
                ->all();
        foreach ($member as $k => $v){
            unset($member[$k]['mUserinfo']);
            //用户所在项目中的中心名称
            $centerinfo               = UCenterUser::getCenterId($v['u_userID'], $v['u_projectID']);;
            $member[$k]['centername'] = $centerinfo['u_centername'];
            $member[$k]['centerid']   = $centerinfo['u_centerID'];
            //用户所在项目的任务
            $member[$k]['task']       = UUserTask::usertask($v['u_projectID'], $v['u_userID'])['note'];
            $member[$k]['taskid']     = UUserTask::usertask($v['u_projectID'], $v['u_userID'])['id'];
        }
        return $member;
    }

    /**
     * @todo 用户是否存在于该项目
     * @param $userId
     * @param $projectId
     * @return bool
     */
    public static function userInProjectIsExist($userId, $projectId)
    {
        if (UUserProject::findOne(["u_userID" => $userId, "u_projectID" => $projectId])) {
            return true;    // 存在
        }
        return false;   // 不存在
    }

    /**
     * @todo 添加项目成员
     * @param $projectId
     * @param $memberId
     * @return bool
     */
    public static function addMemberForProject($projectId, $memberId, $isAdmin,$userid)
    {
        $model = new UUserProject();
        $model->u_projectID  = $projectId;
        $model->u_userID     = $memberId;
        $model->u_permission = ($isAdmin == "y" || $isAdmin == "Y") ? 1 : 2;
        $model->u_createtime = date("Y-m-d H:i:s");

        if ($model->save()) {
            //项目任务是基线的任务id
            //$taskid = UProjectTask::writeTaskid($projectId);
            //成员默认有基线任务
            //UUserTask::createUserTask($memberId, $taskid, $userid, $projectId);
            return true;
        }
        return false;
    }

    /**
     * @todo 设置项目管理员
     * @param $projectId
     * @param $memberId
     * @return bool
     */
    public static function updateMemberAsAdmin($projectId, $memberId)
    {
        $model = UUserProject::findOne(['u_projectID' => $projectId, 'u_userID' => $memberId]);
        $model->u_permission = "1";
        if ($model->save()) {
            return true;
        }
        return false;
    }

    /**
     * @todo 移除项目管理员
     * @param $projectId
     * @param $memberId
     * @return bool
     */
    public static function updateMemberRemoveAdmin($projectId, $memberId)
    {
        $model = UUserProject::findOne(['u_projectID' => $projectId, 'u_userID' => $memberId]);
        $model->u_permission = "2";

        if ($model->save()) {
            return true;
        }
        return false;
    }

    /**
     * @todo 删除项目成员
     * @param $projectId
     * @param $memberId
     * @return bool
     */
    public static function removeMemberInProject($projectId, $memberId)
    {
        //项目成员表中删除
        if (UUserProject::deleteAll(['u_projectID' => $projectId, 'u_userID' => $memberId])) {
            //项目中心删除
            UCenterUser::deleteUser($memberId, $projectId);
            //成员在项目中的任务删除
            UUserTask::deleteTask($memberId, $projectId);
            return true;
        }
        
        return false;
    }
    /**
     * @todo 项目管理员
     * @param projectid
     * @return array
     */
    public static function projectAdmin($projectid){
        $user = UUserProject::find()->select('u_userID')->where(['u_projectID'=>$projectid,'u_permission'=>1])->asarray()->all();
        foreach ($user as $k=>$v){
            $adminarr[] = $v['u_userID'];
        }
        return $adminarr;
    }
    
    
}
