<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "u_follow_user".
 *
 * @property string $u_follow_id
 * @property string $u_userid
 */
class UFollowUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_follow_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_follow_id', 'u_userid'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'u_follow_id' => 'U Follow ID',
            'u_userid' => 'U Userid',
        ];
    }

    /**
     * @todo 关联随访表
     */
    public function getUFollowData()
    {
        return $this->hasMany(UFollowData::className(), ['u_follow_id' => 'u_follow_id']);
    }
    /**
     * @todo 关联用户表
     */
    public function getMUserinfo(){
        return $this->hasMany(MUserinfo::className(), ['s_userid' => 'u_userid']);
    }


    /**
     * @todo 成员是否已存在该随访中
     */
    public static function memberIsEmptyFollow(array $userIds, $followId) {
        $uIds = UFollowUser::find()
            ->select("u_userid")
            ->where(["u_userid" => $userIds, "u_follow_id" => $followId])
            ->asArray()
            ->all();
        if ($uIds) {
            return $uIds;
        }
        return false;
    }

    /**
     * @todo 添加随访患者
     */
    public static function addFollowMembers(array $users, $followId){
        if ($uIds = self::memberIsEmptyFollow($users, $followId)) {
            foreach ($users as $key => $val) {
                foreach ($uIds as $k => $v) {
                    if ($val == $v) {
                        unset($users[$k]);
                    }
                }
            }
        }

        $userSuccList = array();
        $_model = new UFollowUser();
        foreach ($users as $key => $val) {
            $model = clone $_model;
            $model->u_follow_id = $followId;
            $model->u_userid = $val;
            if ($model->save()) {
                $userSuccList[] = $val;
            }
        }
        return $userSuccList;
    }

    /**
     * @todo 获取随访成员列表
     * @param $followId
     */
    public static function getMemberListInFollowId($followId)
    {
        $memberList = UFollowUser::find()
            ->select("u_follow_id, u_userid, s_username, s_sex, s_cellphone, s_mem, s_workunti")
            ->joinWith("mUserinfo")
            ->where(["u_follow_id" => $followId])
            ->asArray()
            ->all();

        foreach ($memberList as $k => $v) {
            unset($memberList[$k]["mUserinfo"]);
        }
        return $memberList;
    }

    /**
     * @todo 移除随访患者
     */
    public static function removeFollowMembers(array $users, $followId)
    {
        if (UFollowUser::deleteAll(["u_userid" => $users, "u_follow_id" => $followId])) {
            return true;
        }

        return false;
    }

    /**
     * @todo 查询用户所参与的随访
     */
    public static function showFollowToUser($userId)
    {
        $follows = UFollowUser::find()
            ->joinWith("uFollowData")
            ->where(["u_userid" => $userId])
            ->asArray()
            ->all();

        return $follows;
    }

}
