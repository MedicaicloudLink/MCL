<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "patient_share".
 *
 * @property integer $id
 * @property string $userid
 * @property string $touserid
 * @property string $tomail
 * @property string $patientid
 * @property string $projectid
 * @property string $createtime
 */
class PatientShare extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient_share';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createtime'], 'safe'],
            [['userid', 'touserid', 'patientid', 'projectid'], 'string', 'max' => 11],
            [['tomail'], 'string', 'max' => 255],
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
            'tomail' => 'Tomail',
            'patientid' => 'Patientid',
            'projectid' => 'Projectid',
            'createtime' => 'Createtime',
        ];
    }
    /**
     * 分享患者
     * @param $userid
     * @param $projectid
     * @param $patientid
     * @param $tomail
     * @return bool
     */
    public static function sharePatient($userid,$projectid,$patientid,$tomail){
        //患者之前的分享列表删掉
        PatientShare::deletePatientShare($patientid);
        //个人自己信息
        $myinfo     = UUserInfo::userInfo($userid);
        //项目管理员
        $admin      = PProjectUser::adminInfo($projectid);
        foreach($admin as $key=>$val){
            $adminarr[] = $val['s_mail'];
        }
        $patientarr = explode(',',$patientid);
        $tomailarr  = explode(',',$tomail);
        foreach ($patientarr as $k1=>$v1){
            //患者的详情
            $patientinfo = PatientBase::patientInfo($v1);
            //患者是否被锁定
            if($patientinfo['islock'] == 1){
                //患者的创建人
                $createmail = UUserInfo::userInfo($patientinfo['createuser'])['s_mail'];
                foreach ($tomailarr as $k=>$v) {
                    //不是患者创建人,不是项目管理员
                    if ($v != $createmail && !in_array($v, $admin) && $v!= $myinfo['s_mail']) {
                        //是否已经分享过
                        $shareinfo = PatientShare::shareInfo($v1, $projectid, $v);
                        if (empty($shareinfo)) {
                            $userinfo = QCompanyUser::userInfoBymail($v);
                            if (empty($userinfo)) {
                                $touserid = '';
                            } else {
                                $touserid = $userinfo['userid'];
                            }
                            $model = new PatientShare();
                            $model->userid      = $userid;
                            $model->tomail      = $v;
                            $model->touserid    = $touserid;
                            $model->patientid   = $v1;
                            $model->projectid   = $projectid;
                            $model->createtime  = date('Y-m-d H:i:s');
                            $model->save();
                        }
                    }
                }
            }
        }
    }
    /**
     * 是否分享过
     * @param patientid
     * @param projectid
     * @param tomail
     * @return array
     */
    public static function shareInfo($patientid,$projectid,$tomail){
        $result = PatientShare::find()
                ->select('id')
                ->where(['patientid'=>$patientid,'projectid'=>$projectid,'tomail'=>$tomail])
                ->asArray()
                ->one();
        return $result;
    }
    /**
     * 删除患者分享清单
     * @param patientids(逗号隔开的)
     * @return bool
     */
    public static function deletePatientShare($patientids){
        $arr = explode(',',$patientids);
        PatientShare::deleteAll(['in','patientid',$arr]);
    }
    /**
     * 患者分享清单
     * @param patientid
     * @return array
     */
    public static function shareList($patientid){
        $result = PatientShare::find()
                ->select('tomail')
                ->where(['patientid'=>$patientid])
                ->asArray()
                ->all();
        return $result;
    }
    /**
     * 某人在项目中被分享的患者
     * @param userid
     * @param projectid
     * @param mail
     * @return array
     */
    public static function sharePatientids($userid,$projectid,$mail){
        $result = PatientShare::find()
                ->select('patientid')
                ->where(['and',['touserid'=>$userid],['projectid'=>$projectid]])
                ->orWhere(['and',['tomail'=>$mail],['projectid'=>$projectid]])
                ->asArray()
                ->all();
        $patientid = [];
        if(!empty($result)){
            foreach($result as $k=>$v){
                $patientid[] = $v['patientid'];
            }
        }
        return $patientid;

    }
}
