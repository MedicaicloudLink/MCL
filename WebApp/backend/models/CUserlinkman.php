<?php

namespace app\models;

use Yii;
use app\models\MUserinfo;
use app\models\ULetter;
/**
 * This is the model class for table "c_userlinkman".
 *
 * @property integer $id
 * @property string $userid
 * @property string $touserid
 * @property string $createtime
 */
class CUserlinkman extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'c_userlinkman';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'touserid'], 'required'],
            [['createtime'], 'safe'],
            [['userid', 'touserid'], 'string', 'max' => 32],
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
            'createtime' => 'Createtime',
        ];
    }
    /**
     * @todo 关联用户表
     */
    public function getMUserinfo(){
        return $this->hasMany(MUserinfo::className(), ['s_userid'=>'touserid']);
    }
    /**
     * @todo 是否和某个用户是好友
     */
    public static function isContactman($userid,$touserid){
        $contactinfo = CUserlinkman::find()
                     ->where(['userid'=>$userid,'touserid'=>$touserid])
                     ->asarray()
                     ->one();
        return $contactinfo;
    }
    /**
     * @todo 加好友
     */
    public static function addContact($userid,$touserid){
        //是否已是好友
        $info = CUserlinkman::isContactman($userid, $touserid);
        if(empty($info)){
            $model = new CUserlinkman();
            $model -> userid = $userid;
            $model -> touserid = $touserid;
            $model -> createtime = date('Y-m-d H:i:s');
            $model -> save();
        }
        return true;
    }
    /**
     * @todo 联系人列表
     */
    public static function contactmanList($userid,$pagenum){
        $start  = $pagenum-1 <= 0 ? 0 : ($pagenum-1) * 30;
        $result = CUserlinkman::find()
                ->joinWith('mUserinfo')
                ->select('m_userinfo.s_username,s_cellphone,s_workunti,touserid')
                ->where(['userid'=>$userid])
                ->orderby(['CONV( HEX( LEFT( CONVERT( s_username USING gbk ) , 1 ) ) , 16, 10 )'=>SORT_ASC])
                ->offset($start)
                ->limit(30)
                ->asarray()
                ->all();
        foreach($result as $key=>$val){
            unset($result[$key]['mUserinfo']);
        }
        $count  = CUserlinkman::find()
                ->select('id')
                ->where(['userid'=>$userid])
                ->asarray()
                ->count();
        $data['result'] = $result;
        $data['count']  = $count;
        return $data;
    }
    /**
     * @todo 搜索联系人
     */
    public static function searchContactman($userid,$type,$name,$pagenum){
        if($type == 1){
            $start        = $pagenum-1 <= 0 ? 0 : ($pagenum-1) * 30;
            $connection   = Yii::$app->db;
            $sql          = "SELECT m_userinfo.s_username,m_userinfo.s_cellphone,m_userinfo.s_workunti,touserid FROM c_userlinkman INNER JOIN m_userinfo on touserid=s_userid, u_letter WHERE CONV( HEX( LEFT( CONVERT( s_username USING gbk ) , 1 ) ) , 16, 10 )
       BETWEEN cBegin  
       AND cEnd  
       AND fPY = '".$name."' and userid = '".$userid."' limit ".$start.",30";
            $command      = $connection->createCommand($sql);
            $result       = $command->queryAll();
            $sqlnum       = "SELECT m_userinfo.s_username,m_userinfo.s_cellphone,m_userinfo.s_workunti,touserid FROM c_userlinkman INNER JOIN m_userinfo on touserid=s_userid, u_letter WHERE CONV( HEX( LEFT( CONVERT( s_username USING gbk ) , 1 ) ) , 16, 10 )
       BETWEEN cBegin
       AND cEnd
       AND fPY = '".$name."' and userid = '".$userid."'";
            $commandnum   = $connection->createCommand($sqlnum);
            $resultnum    = $commandnum->queryAll();
            $data['result']   = $result;
            $data['count']    = count($resultnum);
            $data['flag']     = 1;
            return $data;
        }
        if($type == 2){
            $where      = ['userid'=>$userid];
            $condition1 = ['and',$where,['like','s_username',$name]];
            $condition2 = ['and',$where,['like','s_cellphone',$name]];
            $start  = $pagenum-1 <= 0 ? 0 : ($pagenum-1) * 30;
            $result = CUserlinkman::find()
                    ->joinWith('mUserinfo')
                    ->select('m_userinfo.s_username,s_cellphone,s_workunti,touserid')
                    ->where($condition1)
                    ->orwhere($condition2)
                    ->orderby(['CONV( HEX( LEFT( CONVERT( s_username USING gbk ) , 1 ) ) , 16, 10 )'=>SORT_ASC])
                    ->offset($start)
                    ->limit(30)
                    ->asarray()
                    ->all();
            foreach($result as $key=>$val){
                unset($result[$key]['mUserinfo']);
            }
            $count  = CUserlinkman::find()
                    ->joinWith('mUserinfo')
                    ->select('c_userlinkman.id')
                    ->where($condition1)
                    ->orwhere($condition2)
                    ->asarray()
                    ->count();
            $data['result']   = $result;
            $data['count']    = $count;
            $data['flag']     = 1;
            if($count == 0){
                //是否为注册用户
                $isuser = MUserinfo::isUser($name);
                if(empty($isuser)){
                    #不是用户
                    $data['flag'] = 3;
                    unset($data['count']);
                    unset($data['result']);
                }else{
                    #是用户
                    $data['flag']   = 2;
                    $data['result'] = $isuser;
                    unset($data['count']);
                }
            }
            return $data;
        }
    }
    /**
     * @TODO 删除联系人
     */
    public static function deleteMan($userid,$touserid){
        $touserid = explode(',', $touserid);
        CUserlinkman::deleteAll(['userid'=>$userid,'touserid'=>$touserid]);
        return true;
    }
}
