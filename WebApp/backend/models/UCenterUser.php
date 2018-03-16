<?php

namespace app\models;

use Yii;
use app\models\UUserTask;

/**
 * This is the model class for table "u_center_user".
 *
 * @property string $u_centerid
 * @property string $u_userid
 */
class UCenterUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_center_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type','projectid'], 'integer'],
            [['u_centerid', 'u_userid'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'u_centerid' => 'U Centerid',
            'u_userid' => 'U Userid',
        ];
    }
    /**
     * @todo 关联中心表
     */
    public function getUProjectdata(){
        return $this->hasMany(UProjectdata::className(), ['u_centerID' => 'u_centerid']);
    }
    /**
     * @todo 关联用户表
     */
    public function getMUserinfo(){
        return $this->hasMany(MUserinfo::className(), ['s_userid' => 'u_userid']);
    }
    /**
     * @todo 关联项目_中心表
     */
    public function getUProjectCenter(){
        return $this->hasMany(UProjectCenter::className(), ['u_centerid' => 'u_centerid']);
    }

    /**
     * @todo 中心成员列表
     * @param $centerId
     */
    public static function findMemberListByCenterId($centerIds){
        $memberList = UCenterUser::find()
            ->select("u_center_user.*,m_userinfo.s_username,m_userinfo.s_cellphone,m_userinfo.s_sex,m_userinfo.s_mybirthday")
            ->joinWith("mUserinfo")
            ->where(["u_centerid" => $centerIds])
            ->asArray()
            ->all();
        foreach ($memberList as $k => $v) {
            unset($memberList[$k]["mUserinfo"]);
            $memberList[$k]['task'] = UUserTask::usertask($v['projectid'], $v['u_userid']);
        }
        return $memberList;
    }

    /**
     * @todo 用户是否已被分配到中心
     * @param $userId
     * @param $projectId
     * @return bool
     */
    public static function userInCenterByProjectIsExist($userId, $projectId){
        $list = UCenterUser::findAll(["projectid" => $projectId, "u_userid" => $userId]);
        if (empty($list)) {
            return false;
        }
        return true;
    }

    /**
     * @todo 用户是否已存在该中心
     * @param $userId
     * @param $centerId
     * @return bool
     */
    public static function userInCenterIsExist($userId, $centerId){
        if (UCenterUser::findOne(["u_centerid" => $centerId, "u_userid" => $userId])) {
            return true;
        }
        return false;
    }

    /**
     * todo 添加中心成员
     * @param $centerId
     * @param $memberId
     * @return bool
     */
    public static function addMemberForCenter ($centerId, $memberId, $type,$projectid){
        if($type == 1){
            //判断是否已经在中心
            if(UCenterUser::userInCenterIsExist($memberId, $centerId)){
                return false;
            }
        }
        $model = new UCenterUser();
        $model -> u_centerid = $centerId;
        $model -> u_userid   = $memberId;
        $model -> type       = $type;
        $model -> projectid  = $projectid;
        if ($model->save()) {
            return true;
        }
        return false;
    }

    /**
     * todo 移除中心成员
     * @param $centerId
     * @param $memberId
     * @return bool
     */
    public static function removeMemberForCenter ($centerId, $memberId){
        if($memberId == '' || empty($memberId)){
            UCenterUser::deleteAll(["u_centerid" => $centerId]);
        }else{
            UCenterUser::deleteAll(["u_centerid" => $centerId, "u_userid" => $memberId]);
        }
        return true;
    }

    /**
     * @todo 删除分中心（解除该中心与用户的关系）
     * @param $centerId
     * @return bool
     */
    public static function delCenterUser($centerId){
        if (!UCenterUser::findAll(["u_centerid" => $centerId])) {
            /* 中心不存在或已被删除 */
            return true;
        }
        if (UCenterUser::deleteAll(["u_centerid" => $centerId])) {
            return true;
        }

        return false;
    }
    /**
     * @todo 用户在项目中的中心名称
     * @param userid
     * @param projectid
     */
    public static function getCenterId($userid,$projectid){
        $result = UCenterUser::find()
                ->where(['u_userid'=>$userid,'projectid'=>$projectid])
                ->asarray()
                ->one();
        if(empty($result)){
            $center['u_centerID']   = '';
            $center['u_centername'] = '';
        }else{
            //获取中心信息
            $center = UCentertable::getCentername($result['u_centerid']);
        }
        return $center;
    }
    /**
     * @todo 用户是否是项目中中心的一员
     * @param userid
     * @param projectid
     */
    public static function userProjectCenter($userid,$projectid){
        $result = UCenterUser::find()
                ->where(['u_userid'=>$userid,'projectid'=>$projectid])
                ->all();
        if(empty($result)){
            return false;
        }
        return true;
    }
    /**
     * @todo 根据项目id和用户id删除中心中的用户
     * @param userid
     * @param projectid
     */
    public static function deleteUser($userid,$projectid){
        //用户是否是项目中中心的一员
        if(UCenterUser::userProjectCenter($userid, $projectid)){
            //删除
            UCenterUser::deleteAll(["projectid" => $projectid, "u_userid" => $userid]);
        }
        return true;
    }
    /**
     * @todo 用户所在项目中分中心成员
     * @param userid
     * @param projectid
     */
    public static function getCenteruser($userid,$projectid){
        $centerinfo = UCenterUser::find()
                    ->where(['u_userid'=>$userid,'projectid'=>$projectid])
                    ->asarray()
                    ->all();
        $userid     = [];
        if(empty($centerinfo)){
            $userid[] = $userid;
        }else{
            foreach($centerinfo as $k=>$v){
                $centerid[] = $v['u_centerid'];
            }
            $userarr  = UCenterUser::find()
                      ->where(['u_centerid'=>$centerid])
                      ->asarray()
                      ->all();
            foreach($userarr as $key=>$val){
                $userid[$val['u_userid']] = $val['u_userid'];
            }
        }
        return $userid;
    }
}
