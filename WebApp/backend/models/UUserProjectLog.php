<?php

namespace app\models;

use Yii;
use app\models\UUserProjectLog;

/**
 * This is the model class for table "u_user_project_log".
 *
 * @property integer $id
 * @property string $userid
 * @property string $touserid
 * @property string $projectid
 * @property integer $status
 * @property string $createtime
 * @property string $dotime
 */
class UUserProjectLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_user_project_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['createtime', 'dotime'], 'safe'],
            [['userid', 'touserid', 'projectid'], 'string', 'max' => 32],
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
            'projectid' => 'Projectid',
            'status' => 'Status',
            'createtime' => 'Createtime',
            'dotime' => 'Dotime',
        ];
    }
    /**
     * @todo 是否有同样的记录
     */
    public static function isLog($userid,$projectid,$touserid){
        $info = UUserProjectLog::find()
              ->where(['userid'=>$userid,'projectid'=>$projectid,'touserid'=>$touserid,'status'=>1])
              ->asarray()
              ->one();
        return $info;
    }
    /**
     * @todo 添加记录
     */
    public static function addLog($userid,$projectid,$touserid){
        //是否已经有此记录
        $loginfo = UUserProjectLog::isLog($userid,$projectid,$touserid);
        if(empty($loginfo)){
            $model = new UUserProjectLog();
            $model -> userid     = $userid;
            $model -> projectid  = $projectid;
            $model -> touserid   = $touserid;
            $model -> createtime = date('Y-m-d H:i:s');
            $model -> save();
        }else{
            UUserProjectLog::updateAll(['createtime'=>date('Y-m-d H:i:s')],['id'=>$loginfo['id']]);
        }
        //创建通知
        $addnotice   = UMessage::updateMessage($userid, $touserid, 201, 2,$projectid);
        $addtonotice = UMessage::updateMessage($userid, $touserid, 202, 1,$projectid);
        return true;
    }
    /**
     * @todo 处理申请
     */
    public static function doApply($userid,$projectid,$touserid,$status){
        //加为项目成员
        if ($status == 2 && !UUserProject::userInProjectIsExist($userid, $projectid)){
            UUserProject::addMemberForProject($projectid, $userid, 'n','');
        }
        //处理日志
        UUserProjectLog::updateAll(['dotime'=>date('Y-m-d H:i:s'),'status'=>$status],['userid'=>$touserid,'touserid'=>$userid,'projectid'=>$projectid]);
        //处理通知
        if($status == 2){
            $type    = 203;
            $oldtype = 205;
        }else{
            $type    = 204;
            $oldtype = 206;
        }
        #加通知
        UMessage::updateMessage($userid, $touserid, $type, 1,$projectid);
        #原来通知状态
        UMessage::updateAll(['type'=>$oldtype,'dotime'=>date('Y-m-d H:i:s'),'douserid'=>$userid,'isread'=>2],['userid'=>$touserid,'touserid'=>$userid,'type'=>[201,202],'projectid'=>$projectid]);
        return true;
    }
}
