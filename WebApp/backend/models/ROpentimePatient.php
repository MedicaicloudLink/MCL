<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "r_opentime_patient".
 *
 * @property integer $id
 * @property string $userid
 * @property string $patientid
 * @property string $createtime
 */
class ROpentimePatient extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_opentime_patient';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createtime'], 'safe'],
            [['userid', 'patientid'], 'string', 'max' => 32],
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
            'patientid' => 'Patientid',
            'createtime' => 'Createtime',
        ];
    }
    /**
     * @todo 用户打开项目时间
     * @param userid
     * @param patientid
     */
    public static function openTime($userid,$patientid){
        $openinfo = ROpentimePatient::find()
        ->select('createtime')
        ->where(['userid'=>$userid,'patientid'=>$patientid])
        ->asarray()
        ->one();
        if(empty($openinfo)){
            $opentime = '';
        }
        $opentime = $openinfo['createtime'];
        return $opentime;
    }
    /**
     * @todo 记录打开患者时间
     * @param userid
     * @param patientid
     */
    public static function openPatient($userid,$patientid){
        //查看是否有
        if(ROpentimePatient::openTime($userid, $patientid) == ''){
            //创建一条新数据
            $model = new ROpentimePatient();
            $model -> userid     = $userid;
            $model -> patientid  = $patientid;
            $model -> createtime = date('Y-m-d H:i:s');
            if($model->save()){
                return true;
            }else{
                return false;
            }
        }else{
            //修改
            $result = ROpentimePatient::updateAll(['createtime'=>date('Y-m-d H:i:s')],['userid'=>$userid,'patientid'=>$patientid]);
            if($result){
                return true;
            }else{
                return false;
            }
        }
    }
}
