<?php

namespace app\models;

use Yii;
use app\models\UPatientbase;
use app\models\UGroup;
use app\models\UFollowPatient;
use app\models\MUserinfo;

/**
 * This is the model class for table "u_patientgroup".
 *
 * @property string $u_groupid
 * @property string $u_patientid
 * @property string $u_createuser
 * @property string $u_createtime
 */
class UPatientgroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_patientgroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_groupid'], 'required'],
            [['u_createtime'], 'safe'],
            [['u_groupid', 'u_patientid', 'u_createuser'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'u_groupid' => 'U Groupid',
            'u_patientid' => 'U Patientid',
            'u_createuser' => 'U Createuser',
            'u_createtime' => 'U Createtime',
        ];
    }
    /**
     * @todo 关联组表
     */
    public function getUGroup(){
        return $this->hasMany(UGroup::className(), ['u_groupid'=>'u_groupid']);
    }
    /**
     * @todo 关联患者表
     */
    public function getUPatientbase(){
        return $this->hasMany(UPatientbase::className(), ['u_MDID'=>'u_patientid']);
    }
    /**
     * @todo 关联患者录入端患者数据表
     */
    public function getRNewpatientdata(){
        return $this->hasMany(RNewpatientdata::className(), ['mdid'  => 'u_patientid']);
    }
    /**
     * @todo 给组内拉人
     * @param groupid    (多个组用,隔开)
     * @param patients   (多个患者用,隔开)
     * @param userid
     * @return bool
     */
    public static function addGroupPatient(){
        //是否组内已经有患者
        $str        = rtrim(Yii::$app->getRequest()->getBodyParam('patients'),',');
        $patientarr = explode(',', $str);
        $groupstr   = rtrim(Yii::$app->getRequest()->getBodyParam('groupid'),',');
        $grouparr   = explode(',', $groupstr);
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $_model     = new UPatientgroup();
        foreach ($grouparr as $key=>$val){
            foreach($patientarr as $k=>$v){
                if(UPatientgroup::isGroupPatient($val,$v)){
                    $model = clone $_model;
                    $model -> u_createuser = $userid;
                    $model -> u_groupid    = $val;
                    $model -> u_patientid  = $v;
                    $model -> u_createtime = date('Y-m-d H:i:s');
                    $model -> save();
                }
            }
        }
        return true;
    }
    /**
     * @todo 组内有无某个患者
     * @param groupid
     * @param patient
     * @return bool
     */
    public static function isGroupPatient($groupid,$patient){
        $result = UPatientgroup::find()
        ->where(['u_groupid'=>$groupid,'u_patientid'=>$patient])
        ->all();
        if(empty($result)){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 删除组内患者
     * @param groupid
     * @param patients
     */
    public static function deleteGroupPatient(){
        $groupid    = Yii::$app->getRequest()->getBodyParam('groupid');
        $patient    = rtrim(Yii::$app->getRequest()->getBodyParam('patients'),',');
        $patientarr = explode(',', $patient);
        if($groupid == ''){
            $result     = UPatientgroup::deleteAll(['u_patientid'=>$patientarr]);
        }else{
            $result     = UPatientgroup::deleteAll(['u_groupid'=>$groupid,'u_patientid'=>$patientarr]);
        }
        if($result){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 组内成员个数
     */
    public static function peopleNum($groupid){
        $num = UPatientgroup::find()
        ->where(['u_groupid'=>$groupid])
        ->count();
        return $num;
    }
    /**
     * @todo 组内患者列表
     * @param groupid
     * @param userid
     * @param pagenum
     */
    public static function groupPatientList(){
        $groupid    = Yii::$app->getRequest()->getBodyParam('groupid');
        $pagenum    = Yii::$app->getRequest()->getBodyParam('pagenum');
        $projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $start      = $pagenum-1 <= 0 ? 0 : ($pagenum-1) * 30;
        //患者列表
        if($groupid == 'no'){
            //未分组
            #项目里已经分组的患者们
            $groupidarr = UGroup::getGroupid($projectid);
            $mdidarr    = UPatientgroup::find()
                        ->select('u_patientid')
                        ->where(['u_groupid'=>$groupidarr])
                        ->asarray()
                        ->all();
            $mdids      = [];
            if(!empty($mdidarr)){
                foreach ($mdidarr as $k=>$v){
                    $mdids[] = $v['u_patientid'];
                }
            }
            $data     = UPatientbase::getgroupdata($mdids,$projectid,$start);
            $result   = $data['data'];
            $count    = $data['count'];
            foreach($result as $key=>$val){
                    unset($result[$key]['rNewpatientdata']);
                    $result[$key]['group']     = UPatientgroup::patientGroup($userid,$val['u_MDID']);
                    //应随访次数
                    $follownum                 = UFollowPatient::follownum($userid, $projectid);
                    //最新内容
                    $lastinfo                  = UFollowPatient::getFollowStatus($projectid,$val['u_MDID'],$userid,$follownum,$val['u_jointime']);
                    //随访状态
                    $result[$key]['follow']      = $lastinfo['status'];
                    $result[$key]['s_username']  = $lastinfo['username'];
                    $result[$key]['status']      = $lastinfo['check'];
                    $result[$key]['center']      = $lastinfo['center'];
            }
            $arr['num']  = $count;
            $arr['data'] = $result;
        }else{
            //已分组
            $patient    = UPatientgroup::find()
                        ->joinWith('rNewpatientdata')
                        ->select('u_patientid,r_newpatientdata.status')
                        ->where(['u_groupid'=>$groupid,'r_newpatientdata.status'=>[2,3,4]])
                        ->offset($start)
                        ->limit(30)
                        ->orderBy(['u_createtime'=>SORT_DESC])
                        ->asarray()
                        ->all();
            if(empty($patient)){
                $result = '';
            }else{
                foreach($patient as $k=>$v){
                    $patientarr[] = $v['u_patientid'];
                }
                $result = UPatientbase::getPatientBase($patientarr);
                foreach($result as $key=>$val){
                    $result[$key]['group']     = UPatientgroup::patientGroup($userid,$val['u_MDID']);
                    //应随访次数
                    $follownum                 = UFollowPatient::follownum($userid, $projectid);
                    //最新内容
                    $lastinfo                  = UFollowPatient::getFollowStatus($projectid,$val['u_MDID'],$userid,$follownum,$val['u_jointime']);
                    //随访状态
                    $result[$key]['follow']      = $lastinfo['status'];
                    $result[$key]['s_username']  = $lastinfo['username'];
                    $result[$key]['status']      = $lastinfo['check'];
                    $result[$key]['center']      = $lastinfo['center'];
                }
            }
            $count      = UPatientgroup::find()
                        ->joinWith('rNewpatientdata')
                        ->select('u_patientid,r_newpatientdata.status')
                        ->where(['u_groupid'=>$groupid,'r_newpatientdata.status'=>[2,3,4]])
                        ->count();
            $arr['num']  = $count;
            $arr['data'] = $result;
        }
        return $arr;
    }
    /**
     * @todo 患者组
     * @param mdid
     */
    public static function patientGroup($userid,$mdid){
        $result = UPatientgroup::find()
        ->select('u_patientgroup.u_groupid,u_group.u_groupname')
        ->joinWith('uGroup')
        ->where(['u_patientgroup.u_patientid'=>$mdid,'u_patientgroup.u_createuser'=>$userid])
        ->asarray()
        ->all();
        $str = '';
        if(!empty($result)){
            foreach($result as $key=>$val){
                $str .= $val['u_groupname'].'、';
            }
            $str = mb_substr($str,0,-1,'utf-8');
            //$str = rtrim($str,'、');
        }
        if($str == ''){
            $str = '未分组';
        }
        return $str;
    }
}
