<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contact_person_log".
 *
 * @property string $id
 * @property string $userid
 * @property string $touserid
 * @property integer $status
 * @property string $updatetime
 * @property integer $tagid
 */
class ContactPersonLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contact_person_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status','tagid'], 'integer'],
            [['updatetime'], 'safe'],
            [['userid', 'touserid'], 'string', 'max' => 11],
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
            'touserid' => 'Touserid',
            'status' => 'Status',
            'updatetime' => 'Updatetime',
            'tagid' => 'Tagid',
        ];
    }
    /**
     * 是否有已经有申请记录
     * @param userid
     * @param touserid
     * @return array
     */
    public static function logInfo($userid,$touserid){
        $result = ContactPersonLog::find()
                ->select('id,tagid')
                ->where(['userid'=>$userid,'touserid'=>$touserid,'status'=>1])
                ->asArray()
                ->one();
        return $result;
    }
    /**
     * 申请创建联系人
     * @param $userid
     * @param $touserid
     * @param $tagid
     * @return bool
     */
    public static function addLog($userid,$touserid,$tagid){
        if(!empty(ContactPersonLog::logInfo($userid,$touserid))){
            $model = ContactPersonLog::findOne(['userid'=>$userid,'touserid'=>$touserid,'status'=>1]);
        }else{
            $model = new ContactPersonLog();
        }
        $model ->userid     = $userid;
        $model ->touserid   = $touserid;
        $model ->status     = 1;
        $model ->tagid      = $tagid;
        $model ->updatetime = date('Y-m-d H:i:s');
        if($model->save()){
            NNotice::addNotice($userid,$touserid,2,'');
            return true;
        }else{
            return false;
        }
    }
    /**
     * 处理申请
     * @param userid
     * @param touserid
     * @param status
     * @return bool
     */
    public static function doApply($userid,$touserid,$status){
        if($status == 2){
            #同意
            ContactPerson::addContact($userid,$touserid);
            ContactPersonLog::updateAll(['status'=>$status,'updatetime'=>date('Y-m-d H:i:s')],['userid'=>$userid,'touserid'=>$touserid,'status'=>1]);
            ContactPersonLog::updateAll(['status'=>$status,'updatetime'=>date('Y-m-d H:i:s')],['userid'=>$touserid,'touserid'=>$userid,'status'=>1]);
        }
        if($status == 3){
            #拒绝
            ContactPersonLog::updateAll(['status'=>$status,'updatetime'=>date('Y-m-d H:i:s')],['userid'=>$touserid,'touserid'=>$userid,'status'=>1]);
        }
        return true;
    }
    /**
     * 申请加我的所有人
     * @param userid
     * @return array
     */
    public static function applyMyInfo($userid){
        $result = ContactPersonLog::find()
                ->select('userid,updatetime')
                ->where(['touserid'=>$userid,'status'=>1])
                ->asArray()
                ->all();
        foreach($result as $k=>$v){
            $userinfo = UUserInfo::userInfo($v['userid']);
            $result[$k]['s_username'] = $userinfo['s_username'];
            $result[$k]['s_avatar']   = $userinfo['s_avatar'];
        }
        return $result;
    }

}
