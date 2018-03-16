<?php

namespace app\models;

use Yii;
use app\models\Commonfun;
use app\models\UMessage;

/**
 * This is the model class for table "c_userlink_log".
 *
 * @property integer $id
 * @property string $logid
 * @property integer $type
 * @property string $userid
 * @property string $touserid
 * @property integer $status
 * @property string $createtime
 * @property string $updatetime
 */
class CUserlinkLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'c_userlink_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['logid', 'type', 'userid', 'touserid'], 'required'],
            [['type', 'status'], 'integer'],
            [['createtime', 'updatetime'], 'safe'],
            [['logid', 'userid', 'touserid'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'logid' => 'Logid',
            'type' => 'Type',
            'userid' => 'Userid',
            'touserid' => 'Touserid',
            'status' => 'Status',
            'createtime' => 'Createtime',
            'updatetime' => 'Updatetime',
        ];
    }
    /**
     * @todo 是否申请过添加某个用户
     */
    public static function isContactLog($userid,$touserid){
        $loginfo = CUserlinkLog::find()
                 ->where(['userid'=>$userid,'touserid'=>$touserid,'status'=>1])
                 ->asarray()
                 ->one();
        #status=1;是未处理过的记录
        return $loginfo;
    }
    /**
     * @todo 创建申请记录（有则更新，没有重新创建）
     */
    public static function updateLinkLog($userid,$touserid){
        //是否有申请记录
        $loginfo = CUserlinkLog::isContactLog($userid, $touserid);
        if(!empty($loginfo)){
            //更新数据
            $model = CUserlinkLog::findOne(['userid'=>$userid,'touserid'=>$touserid,'status'=>1]);
            $model -> createtime = date('Y-m-d H:i:s');
        }else{
            //创建数据
            $model = new CUserlinkLog();
            $model -> logid      = Commonfun::randpw();
            $model -> type       = 1;
            $model -> userid     = $userid;
            $model -> touserid   = $touserid;
            $model -> createtime = date('Y-m-d H:i:s');
        }
        if($model->save()){
            //创建或更新通知
            UMessage::updateMessage($userid,$touserid,101,2);
            UMessage::updateMessage($userid,$touserid,102,1);
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 处理申请
     */
    public static function doApply($userid,$touserid,$status){
        //是否对方申请过我，我也申请过对方
        $toapplyinfo  = CUserlinkLog::isContactLog($touserid, $userid);
        $myapplyinfo  = CUserlinkLog::isContactLog($userid, $touserid);
        #都互相申请过，那么，直接改这两条数据的状态值就好了哦
        if(!empty($toapplyinfo) && !empty($myapplyinfo)){
            CUserlinkLog::updateAll(['status'=>$status,'updatetime'=>date('Y-m-d H:i:s')],['userid'=>$userid,'touserid'=>$touserid]);
            CUserlinkLog::updateAll(['status'=>$status,'updatetime'=>date('Y-m-d H:i:s')],['userid'=>$touserid,'touserid'=>$userid]);
        }
        #只是对方申请过，那么改对方那条记录的状态，再加一条自己的记录
        if(!empty($toapplyinfo) && empty($myapplyinfo)){
            CUserlinkLog::updateAll(['status'=>$status,'updatetime'=>date('Y-m-d H:i:s')],['userid'=>$touserid,'touserid'=>$userid]);
            $model = new CUserlinkLog();
            $model -> logid      = Commonfun::randpw();
            $model -> type       = 2;
            $model -> userid     = $userid;
            $model -> touserid   = $touserid;
            $model -> createtime = date('Y-m-d H:i:s');
            $model -> updatetime = date('Y-m-d H:i:s');
            $model -> status     = $status;
            $model -> save();
        }
        //数据加入好友表
        #添加好友的方法做了判断，都加一次也无妨
        if($status == 2){
            CUserlinkman::addContact($userid, $touserid);
            CUserlinkman::addContact($touserid, $userid);
        }
        //处理通知
        $tonotice_false   = UMessage::isMessage($touserid, $userid, 101, 2);
        $tonotice_true    = UMessage::isMessage($touserid, $userid, 102, 1);
        $mynotice_false   = UMessage::isMessage($userid, $touserid, 101, 2);
        $mynotice_true    = UMessage::isMessage($userid, $touserid, 102, 1);
        if($status == 2){
            $type = 105;
        }
        if($status == 3){
            $type = 106;
        }
        if(!empty($tonotice_false) || !empty($tonotice_true)){
            //修改类型
            UMessage::updateAll(['type'=>$type,'dotime'=>date('Y-m-d H:i:s'),'douserid'=>$userid,'isread'=>2],['userid'=>$touserid,'touserid'=>$userid,'type'=>[101,102]]);
        }
        if(!empty($mynotice_false) || !empty($mynotice_true)){
            //修改类型
            UMessage::updateAll(['type'=>$type,'dotime'=>date('Y-m-d H:i:s'),'douserid'=>$userid,'isread'=>2],['userid'=>$userid,'touserid'=>$touserid,'type'=>[101,102]]);
        }
        //发出通知
        if($status == 2){
            $type_new = 103;
        }
        if($status == 3){
            $type_new = 104;
        }
        UMessage::updateMessage($userid, $touserid, $type_new, 1);
        return true;
    }
}
