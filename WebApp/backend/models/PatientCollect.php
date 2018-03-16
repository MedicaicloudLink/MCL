<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "patient_collect".
 *
 * @property integer $id
 * @property string $patientid
 * @property string $projectid
 * @property string $userid
 * @property string $createtime
 */
class PatientCollect extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient_collect';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createtime'], 'safe'],
            [['patientid', 'projectid', 'userid'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'patientid' => 'Patientid',
            'projectid' => 'Projectid',
            'userid' => 'Userid',
            'createtime' => 'Createtime',
        ];
    }
    /**
     * 关联患者表
     */
    public function getPatientBase(){
        return $this->hasMany(PatientBase::className(), ['patientid'  => 'patientid']);
    }
    /**
     * 是否收藏过
     * @param $userid
     * @param $projectid
     * @param $patientid
     * @return array
     */
    public static function collectInfo($userid,$projectid,$patientid){
        $result = PatientCollect::find()
                ->select('patientid,projectid,userid')
                ->where(['patientid'=>$patientid,'projectid'=>$projectid,'userid'=>$userid])
                ->asArray()
                ->one();
        return $result;
    }
    /**
     * 收藏/取消收藏
     * @param $userid
     * @param $projectid
     * @param $patientid
     * @param $status
     * @return bool
     */
    public static function upCollect($userid,$projectid,$patientid,$status){
        if($status == 1){
            $collectinfo = PatientCollect::collectInfo($userid,$projectid,$patientid);
            if(empty($collectinfo)){
                $model = new PatientCollect();
                $model ->userid     = $userid;
                $model ->patientid  = $patientid;
                $model ->projectid  = $projectid;
                $model ->createtime = date('Y-m-d H:i:s');
                $model ->save();
            }
        }else{
            PatientCollect::deleteAll(['userid'=>$userid,'patientid'=>$patientid,'projectid'=>$projectid]);
        }
        return true;
    }
    /**
     * 患者在项目中收藏的患者
     * @param $userid
     * @param $projectid
     * @param $page
     * @param $searchname
     * @return array
     */
    public static function userProjectCollect($userid,$projectid,$page,$searchname){
        $start  = $page-1 <= 0 ? 0 : ($page-1) * 15;
        if($searchname == ''){
            $where = [];
        }else{
            $where = ['like','name',$searchname];
        }
        $result = PatientCollect::find()
                ->select('patient_base.patientid,name,sex,birthday,patient_base.updateuser,patient_base.updatetime,status,islock')
                ->joinWith('patientBase')
                ->where(['patient_collect.userid'=>$userid,'patient_collect.projectid'=>$projectid])
                ->andWhere($where)
                ->orderBy(['patient_base.updatetime'=>SORT_DESC])
                ->offset($start)
                ->limit(15)
                ->asArray()
                ->all();
        foreach ($result as $k=>$v){
            unset($result[$k]['patientBase']);
            //更新人姓名
            $result[$k]['s_username'] = UUserInfo::userInfo($v['updateuser'])['s_username'];
            $result[$k]['collect']    = 1;
        }
        if($searchname == ''){
            $count  = PatientCollect::find()
                    ->select('patient_collect.id')
                    ->where(['patient_collect.userid'=>$userid,'patient_collect.projectid'=>$projectid])
                    ->count();
        }else{
            $count  = PatientCollect::find()
                    ->joinWith('patientBase')
                    ->select('patient_collect.id')
                    ->where(['patient_collect.userid'=>$userid,'patient_collect.projectid'=>$projectid])
                    ->andWhere(['like','name',$searchname])
                    ->count();
        }

        $data['data']  = $result;
        $data['count'] = $count;
        return $data;
    }
}
