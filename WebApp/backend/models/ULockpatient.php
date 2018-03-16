<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "u_lockpatient".
 *
 * @property integer $id
 * @property string $userid
 * @property string $mdid
 * @property string $taskid
 * @property string $locktime
 */
class ULockpatient extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_lockpatient';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['locktime'], 'required'],
            [['locktime'], 'safe'],
            [['userid', 'mdid', 'taskid'], 'string', 'max' => 32],
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
            'mdid' => 'Mdid',
            'taskid' => 'Taskid',
            'locktime' => 'Locktime',
        ];
    }
    /**
     * @todo 患者锁定信息
     */
    public static function lockPatientInfo($mdid,$taskid){
        $info = ULockpatient::find()
              ->where(['mdid'=>$mdid,'taskid'=>$taskid])
              ->one();
        return $info;
    }
    /**
     * @todo 是否可以访问
     */
    public static function isRead($userid,$mdid,$taskid){
        //可以访问 type：allow  否则为no
        $info = ULockpatient::lockPatientInfo($mdid, $taskid);
        //是否已经锁定
        if(!empty($info)){
            //是否已经超时
            $time = time()-strtotime($info['locktime']);
            if($time>86400){
                //修改数据
                $model = ULockpatient::findOne(['id'=>$info['id']]);
                $model -> userid = $userid;
                $model -> locktime = date('Y-m-d H:i:s');
                if($model -> save()){
                    return 'allow';
                }else{
                    return false;
                }
            }else{
                //判断是否为本人
                if($userid == $info['userid']){
                    //修改数据
                    $model = ULockpatient::findOne(['id'=>$info['id']]);
                    $model -> locktime = date('Y-m-d H:i:s');
                    if($model -> save()){
                        return 'allow';
                    }else{
                        return false;
                    }
                }else{
                    return 'no';
                }
            }
        }else{
            //创建新数据
            $model = new ULockpatient();
            $model -> userid = $userid;
            $model -> mdid   = $mdid;
            $model -> taskid = $taskid;
            $model -> locktime = date('Y-m-d H:i:s');
            if($model -> save()){
                return 'allow';
            }else{
                return false;
            }
        }
    }
    /**
     * @TODO 解除锁定
     */
    public static function unLock($userid,$mdid,$taskid){
        if(ULockpatient::deleteAll(['userid'=>$userid,'mdid'=>$mdid,'taskid'=>$taskid])){
            return true;
        }else{
            return false;
        }
    }
}
