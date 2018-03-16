<?php

namespace app\models;

use Yii;
use app\models\Commonfun;
use app\models\UProjectdata;

/**
 * This is the model class for table "u_message".
 *
 * @property integer $id
 * @property string $userid
 * @property string $touserid
 * @property integer $type
 * @property integer $isread
 * @property string $createtime
 * @property integer $status
 * @property string $douserid
 * @property string $dotime
 */
class UMessage extends \yii\db\ActiveRecord
{
    const NODO = [101,102,201,202];#不用做已读操作处理的
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'isread', 'status'], 'integer'],
            [['createtime', 'dotime'], 'safe'],
            [['id','userid', 'touserid', 'douserid','projectid','recordid'], 'string', 'max' => 32],
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
            'type' => 'Type',
            'isread' => 'Isread',
            'createtime' => 'Createtime',
            'status' => 'Status',
            'douserid' => 'Douserid',
            'dotime' => 'Dotime',
            'projectid' => 'Projectid',
            'recordid' => 'Recordid',
        ];
    }
    /**
     * @todo 关联用户表
     */
    public function getMUserinfo(){
        return $this->hasMany(MUserinfo::className(), ['s_userid'=>'userid']);
    }
    /**
     * @todo 是否有此条通知
     */
    public static function isMessage($userid,$touserid,$type,$status,$projectid='',$recordid=''){
        $messageinfo = UMessage::find()
                    ->where(['userid'=>$userid,'touserid'=>$touserid,'type'=>$type,'status'=>$status,'projectid'=>$projectid,'recordid'=>$recordid])
                    ->asarray()
                    ->one();
        return $messageinfo;
    }
    /**
     * @todo 创建/更新通知
     */
    public static function updateMessage($userid,$touserid,$type,$status,$projectid='',$recordid=''){
        //是否已有此条通知
        $messageinfo = UMessage::isMessage($userid, $touserid, $type, $status, $projectid, $recordid);
        if(empty($messageinfo)){
            //创建
            $model = new UMessage();
            $model -> id         = Commonfun::randpw();
            $model -> userid     = $userid;
            $model -> touserid   = $touserid;
            $model -> type       = $type;
            $model -> status     = $status;
            $model -> projectid  = $projectid;
            $model -> recordid   = $recordid;
            $model -> createtime = date('Y-m-d H:i:s');
        }else{
            //更新
            $model = UMessage::findOne(['id'=>$messageinfo['id']]);
            $model -> createtime = date('Y-m-d H:i:s');
            $model -> isread     = 1;
        }
        if($model -> save()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 不需要操作的设置为已读
     */
    public static function doRead($userid){
        UMessage::updateAll(['isread'=>2],['and','touserid = "'.$userid.'"',['not in','type',self::NODO]]);
        return true;
    }
    /**
     * @todo 所有通知
     */
    public static function allNotice($userid,$pagenum,$type){
        //所有不需要操作的设置为已读
        $read     = UMessage::doRead($userid);
        //通知
        $start    = $pagenum-1 <= 0 ? 0 : ($pagenum-1) * 30;
        $where    = ['touserid'=>$userid,'status'=>1];
        if($type == 'nodo'){
            $where    = ['touserid'=>$userid,'status'=>1,'type'=>[102,202]];
        }
        if($type == 'return'){
            $where    = ['touserid'=>$userid,'status'=>1,'type'=>[301,302]];
        }
        if($type == 105 || $type == 106 || $type == 205 || $type == 206){
            $where    = ['touserid'=>$userid,'status'=>1,'type'=>$type];
        }
        $result   = UMessage::find()
                  ->joinWith('mUserinfo')
                  ->select('u_message.id,u_message.userid,u_message.type,u_message.createtime,u_message.projectid,u_message.recordid,s_username')
                  ->where($where)
                  ->orderby(['createtime'=>SORT_DESC])
                  ->offset($start)
                  ->limit(30)
                  ->asarray()
                  ->all();
        foreach($result as $k=>$v){
            unset($result[$k]['mUserinfo']);
            if($v['projectid'] != ''){
                $result[$k]['projectname'] = UProjectdata::getProjectDetailByProjectId($v['projectid'])[0]['u_projectName'];
            }
        }
        $count    = UMessage::find()
                  ->select('id')
                  ->where($where)
                  ->count();
        $data['result'] = $result;
        $data['count']  = $count;
        return $data;
    }
    /**
     * @todo 未读条数
     */
    public static function noRead($userid){
        $count  = UMessage::find()
                ->select('id')
                ->where(['touserid'=>$userid,'status'=>1,'isread'=>1])
                ->count();
        return $count;
    }
}
