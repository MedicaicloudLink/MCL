<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\Commonfun;
use app\models\UProjectTask;
use app\models\FCommitLog;
/**
 * This is the model class for table "u_follow_patient".
 *
 * @property string $u_follow_id
 * @property string $u_MDID
 * @property string $u_follow_user
 * @property string $u_follow_into_date
 * @property string $updatetime
 * @property string $updateuserid
 * @property string $sourcedata
 * @property string $patientdata
 * @property string $formdata
 * @property string $formname
 * @property integer $projectid
 * @property integer $u_follow_status
 * @property string $remark
 * @property string $checkuserid
 * @property string $checktime
 * @property string $recordid
 */
class UFollowPatient extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_follow_patient';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_follow_into_date', 'updatetime', 'checktime'], 'safe'],
            [['sourcedata', 'patientdata', 'formdata', 'remark','other_reason'], 'string'],
            [['projectid', 'u_follow_status','isuse'], 'integer'],
            [['u_follow_id', 'u_MDID', 'u_follow_user', 'updateuserid', 'checkuserid', 'recordid'], 'string', 'max' => 32],
            [['formname'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'u_follow_id' => 'U Follow ID',
            'u_MDID' => 'U  Mdid',
            'u_follow_user' => 'U Follow User',
            'u_follow_into_date' => 'U Follow Into Date',
            'updatetime' => 'Updatetime',
            'updateuserid' => 'Updateuserid',
            'sourcedata' => 'Sourcedata',
            'patientdata' => 'Patientdata',
            'formdata' => 'Formdata',
            'formname' => 'Formname',
            'projectid' => 'Projectid',
            'u_follow_status' => 'U Follow Status',
            'remark' => 'Remark',
            'checkuserid' => 'Checkuserid',
            'checktime' => 'Checktime',
            'recordid' => 'Recordid',
            'isuse'    => 'Isuse',
        ];
    }
    /**
     * @todo 关联患者基本信息表
     */
    public function getUPatientbase(){
        return $this->hasMany(UPatientbase::className(), ['u_MDID' => 'u_MDID']);
    }
    /**
     * @todo 项目随访次数
     * @param userid
     * @param projectid
     */
    public static function follownum($userid,$projectid){
        //应随访次数$projectid
        $follow = UProjectTask::showFollow($userid, $projectid);
        return count($follow);
    }
    /**
     * @todo 患者的随访状态      已入组，待随访，已随访次数/应随访次数
     * @param projectid
     * @param mdid
     */
    public static function getFollowStatus($projectid,$mdid,$userid,$follownum,$jointime){
        if($follownum == 0){
            $str = '已入组';
        }else{
            //患者是否随访过
            $follow = UFollowPatient::find()
                    ->select('id')
                    ->where(['u_MDID'=>$mdid,'projectid'=>$projectid,'u_follow_status'=>[1,9,10],'isuse'=>1])
                    ->orderby(['updatetime'=>SORT_DESC])
                    ->asarray()
                    ->all();
            $finishfollow = UFollowPatient::find()
                        ->select('id')
                        ->where(['u_MDID'=>$mdid,'projectid'=>$projectid,'u_follow_status'=>1,'isuse'=>1])
                        ->orderby(['updatetime'=>SORT_DESC])
                        ->asarray()
                        ->all();
            if(empty($follow)){
                //是否到了随访期限
                #项目中最近一次随访时间段
                $minfollow = UProjectTask::lastFollow($projectid,$userid);
                #是否到了随访时间
                if(time()-strtotime($jointime)>86400*$minfollow || time()-strtotime($jointime) == 86400*$minfollow){
                    //患者登记是否通过审核；通过为待随访。没通过为已入组
                    $patientinfo = RNewpatientdata::patientRecordStatus($mdid);
                    if($patientinfo['status'] == 4){
                        $str = '待随访';
                    }else{
                        $str = '已入组';
                    }
                }else{
                    $str = '已入组';
                }
            }else{
                //审核通过的数量
                $followready = count($finishfollow);
                
                $str         = '已随访'.$followready.'/'.$follownum;
            }
        }
        //最近一次的操作人-审核状态-中心   (2，提交（待审核）；3.被退回；4.审核通过)
        if($str == '已入组'){
            //登记信息
            $patientinfo = RNewpatientdata::patientRecordStatus($mdid);
            //操作人
            $username    = MUserinfo::userInfo($patientinfo['updateuserid'])[0]['s_username'];
            //操作人所在中心
            $center      = UCenterUser::getCenterId($patientinfo['updateuserid'],$projectid)['u_centername'];
            //审核状态
            $check       = $patientinfo['status'];
        }else if($str == '待随访'){
            //登记信息
            $patientinfo = RNewpatientdata::patientRecordStatus($mdid);
            //操作人
            $username    = MUserinfo::userInfo($patientinfo['updateuserid'])[0]['s_username'];
            //操作人所在中心
            $center      = UCenterUser::getCenterId($patientinfo['updateuserid'],$projectid)['u_centername'];
            //审核状态为空
            $check = '';
        }else{
            //审核状态为最近一次随访的
            $follow = UFollowPatient::find()
                    ->select('u_follow_status,updateuserid')
                    ->where(['u_MDID'=>$mdid,'projectid'=>$projectid,'u_follow_status'=>[1,9,10],'isuse'=>1])
                    ->orderby(['updatetime'=>SORT_DESC])
                    ->limit(1)
                    ->asarray()
                    ->one();
            if($follow['u_follow_status'] == 1){
                $check = "4";
            }
            if($follow['u_follow_status'] == 9){
                $check = "2";
            }
            if($follow['u_follow_status'] == 10){
                $check = "3";
            }
            //操作人
            $username    = MUserinfo::userInfo($follow['updateuserid'])[0]['s_username'];
            //操作人所在中心
            $center      = UCenterUser::getCenterId($follow['updateuserid'],$projectid)['u_centername'];
        }
        $result['center']   = $center;
        $result['username'] = $username;
        $result['check']    = $check;
        $result['status']   = $str;
        return $result;
    }
    /**
     * @todo 各个随访下不同随访结果的人数、
     * @param taskid
     * @param shouldfollow
     */
    public static function followType($taskid,$shouldfollow){
        $result = UFollowPatient::find()
                ->select('count(u_follow_id) as count,u_follow_status')
                ->where(['u_follow_id'=>$taskid,'u_follow_status'=>[1,2],'isuse'=>1])
                ->groupBy('u_follow_status')
                ->asarray()
                ->all();
        $waitfollow         = $shouldfollow;
        $status             = [];
        foreach($result as $key=>$val){
            //已有随访结果状态
            $status[]                   =  $val['u_follow_status'];
            if($val['u_follow_status'] == 1){
                $waitfollow = $shouldfollow - $val['count'];
            }
            $newarr[$val['u_follow_status']]['count']           = $val['count'];
            $newarr[$val['u_follow_status']]['u_follow_status'] = $val['u_follow_status'];
        }
        //应该有的状态
        $shouldstatus = [1,2];
        for($i=0;$i<count($shouldstatus);$i++){
            if(!in_array($shouldstatus[$i], $status)){
                $newarr[$shouldstatus[$i]]['count']             = 0;
                $newarr[$shouldstatus[$i]]['u_follow_status']   = $shouldstatus[$i];
            }
        }
        //键值从0开始
        $newresult = array_values($newarr);
        ArrayHelper::multisort($newresult,'u_follow_status',SORT_ASC);
        //待随访数赋值到数组最后  类型8
        $num = count($newresult);
        $newresult[$num]['count']           = $waitfollow;
        $newresult[$num]['u_follow_status'] = 8;
        return $newresult;
    }
    /**
     * @todo 项目中各个随访结果类型占的百分比和个数
     * @param projectid
     * @param followid     没删除的随访id们
     * @param shouldfollow 应随访数
     */
    public static function everyStatusInfo($projectid,$followid,$shouldfollow){
        $result = UFollowPatient::find()
                ->select('count(u_follow_id) as count,u_follow_status')
                ->where(['u_follow_id'=>$followid,'isuse'=>1])
                ->groupBy('u_follow_status')
                ->asarray()
                ->all();
        //带随防
        $waitfollow         = $shouldfollow;
        $status             = [];
        //应该有的状态
        $shouldstatus       = [1,2,3,4,5,6];
        foreach($result as $key=>$val){
            //已有随访结果状态
            $status[]                   =  $val['u_follow_status'];
            if($val['u_follow_status'] == 1){
                $waitfollow = $shouldfollow - $val['count'];
            }
            $newarr[$val['u_follow_status']]['count']           = $val['count'];
            $newarr[$val['u_follow_status']]['countper']        = sprintf("%.1f", $val['count']/$shouldfollow*100).'%';
            $newarr[$val['u_follow_status']]['u_follow_status'] = $val['u_follow_status'];
        }
        for($i=0;$i<count($shouldstatus);$i++){
            if(!in_array($shouldstatus[$i], $status)){
                $newarr[$shouldstatus[$i]]['count']           = 0;
                $newarr[$shouldstatus[$i]]['countper']        = '0.0%';
                $newarr[$shouldstatus[$i]]['u_follow_status'] = $shouldstatus[$i];
            }
        }
        //键值从0开始
        $newresult = array_values($newarr);
        ArrayHelper::multisort($newresult,'u_follow_status',SORT_ASC);
        //待随访数赋值到数组最后  类型8
        $num       = count($newresult);
        $newresult[$num]['count']            = $waitfollow;
        if($shouldfollow == 0){
            $newresult[$num]['countper']     = '0.0%';
        }else{
            $newresult[$num]['countper']     = sprintf("%.1f", $waitfollow/$shouldfollow*100).'%';
        }
        $newresult[$num]['u_follow_status']  = 8;
        return $newresult;
    
    }
    /**
     * @todo 统计随访
     * @param userid
     * @param centerid
     * @param projectid
     */
    public static function followSta($projectid,$userid){
        #***昨天新增随访***
        $date          = date('Y-m-d');
        $yesterdayarr  = UFollowPatient::find()
                       ->where(['u_follow_status'=>1,'projectid'=>$projectid,'isuse'=>1])
                       ->andwhere(['like','u_follow_into_date',date('Y-m-d',strtotime($date. '-1 days'))])
                       ->asarray()
                       ->count();
        #应随访数        到目前为止
        //项目的随访
        $projectfollow = UProjectTask::showFollow($userid, $projectid);
        //每次随访应随访患者数
        $shouldfollow  = 0;
        $followid      = [];
        $followtype    = [];
        foreach($projectfollow as $key=>$val){
            //随访id
            $followid[]                     = $val['taskid'];
            //本次随访处理过的患者
            $patientid                      = UFollowPatient::getPatientid($val['taskid']);
            //每次随访应随访数
            $patientnum[$val['taskid']]     = UPatientbase::patientNum($projectid,$val['taskmonth'],$patientid);
            #***应随访数 ***
            $shouldfollow                   = $shouldfollow + count($patientnum[$val['taskid']]);
            #每次随访各类型的人数
            $followtype[$key]['arrdata']    = UFollowPatient::followType($val['taskid'],count($patientnum[$val['taskid']]));
            $followtype[$key]['taskname']   = $val['taskname'];
    
        }
        #累计录入随访数据
        $alreadyfollow = 0;
        if(empty($followtype)){
            $alreadyfollow = 0;
        }else {
            foreach ($followtype as $k=>$v){
                foreach($v['arrdata'] as $key=>$val){
                    if($val['u_follow_status'] == 1){
                        #***累计录入随访数据***
                        $alreadyfollow = $val['count'] + $alreadyfollow;
                    }
                }
            }
        }
                #***累计录入数据占百分比***
        if($shouldfollow == 0){
            $alreadyper = '0.0%';
        }else{
            $alreadyper = sprintf("%.1f", $alreadyfollow/$shouldfollow*100).'%';
        }
        #***项目中各个随访结果类型占的百分比和个数***
        $everystatus   = UFollowPatient::everyStatusInfo($projectid,$followid,$shouldfollow);
        ###
        $result['yesterday']         = $yesterdayarr;
        $result['shouldfollow']      = $shouldfollow;
        $result['alreadyfollow']     = $alreadyfollow;
        $result['alreadyper']        = $alreadyper;
        $result['everystatus']       = $everystatus;
        $result['followtype']        = $followtype;
        return $result;
    }
    public static function followPatient($mdid,$taskid){
        $result = UFollowPatient::find()
                ->where(['u_MDID'=>$mdid,'u_follow_id'=>$taskid,'isuse'=>1])
                ->asarray()
                ->one();
        return $result;
    }
    /**
     * @todo 添加/编辑患者随访
     */
    public static function createFollow(){
        $mdid      = Yii::$app->getRequest()->getBodyParam('mdid');
        $taskid    = Yii::$app->getRequest()->getBodyParam('taskid');
        //此随访里是否有此患者
        $follow    = UFollowPatient::followPatient($mdid,$taskid);
        //有则把之前数据改为历史状态
        if(!empty($follow)){
            UFollowPatient::updateAll(['isuse'=>2],['u_MDID'=>$mdid,'u_follow_id'=>$taskid]);
        }
        //患者所在项目
        $model = new UFollowPatient();
        $model -> recordid             = Commonfun::randpw();
        $model -> u_follow_id          = $taskid;
        $model -> u_MDID               = $mdid;
        $model -> projectid            = Yii::$app->getRequest()->getBodyParam('projectid');
        $model -> patientdata          = Yii::$app->getRequest()->getBodyParam('patientdata');
        $model -> sourcedata           = Yii::$app->getRequest()->getBodyParam('sourcedata');
        $model -> u_follow_status      = Yii::$app->getRequest()->getBodyParam('status');
        $model -> other_reason         = Yii::$app->getRequest()->getBodyParam('other_reason');
        $model -> u_follow_user        = Yii::$app->getRequest()->getBodyParam('userid');
        $model -> updateuserid         = Yii::$app->getRequest()->getBodyParam('userid');
        $model -> u_follow_into_date   = date('Y-m-d H:i:s');
        $model -> updatetime           = date('Y-m-d H:i:s');
        //模板
        $model -> formdata             = UProjectTask::followInfo($taskid)['formdata'];
        $model -> formname             = UProjectTask::followInfo($taskid)['formname'];
        if($model -> save()){
            if($model -> u_follow_status == 9){
                FCommitLog::addLog($mdid,$taskid,$model -> updateuserid,9,'');
            }
            return $model -> recordid;
        }else{
            return false;
        }
    }
    /**
     * @todo 随访工作日志
     * 
     */
    public static function workList($userid,$projectid,$pagenum,$type,$sort,$search){
        if($sort == 'asc'){
            $sort = SORT_ASC;    #正序
        }
        if($sort == 'desc'){
            $sort = SORT_DESC;   #倒叙
        }
        if($type == 'updatetime'){
            $order = ['u_follow_patient.updatetime' => $sort];#更新时间
        }
        if($type == 'status'){
            $order = ['u_follow_patient.updatetime' => $sort];#病例状态(无用)
        }
        if($type == 'age'){
            $order = ['u_patientbase.u_birthday'    => $sort];#当前年龄
        }
        if($type == 'gender'){
            $order = ['u_patientbase.u_gender'      => $sort];#性别
        }
        if($type == 'name'){
            $order = ['u_patientbase.u_patientname' => $sort];#患者姓名
        }
        $start  = $pagenum-1 <= 0 ? 0 : ($pagenum-1) * 30;
        $post   = UFollowPatient::find()
                ->joinWith('uPatientbase')
                ->select('u_follow_patient.recordid,u_follow_patient.projectid,u_follow_patient.u_follow_id,u_patientbase.u_patientname,u_patientbase.u_gender,u_patientbase.u_birthday,u_patientbase.u_phone,u_patientbase.u_secondphone,u_follow_patient.u_MDID,u_follow_patient.u_follow_status,u_follow_patient.updatetime')
                ->where(['u_follow_patient.projectid'=>$projectid,'u_follow_patient.updateuserid'=>$userid,'u_follow_patient.isuse'=>1,'u_patientbase.u_status'=>0])
                ->offset($start)
                ->limit(30)
                ->orderby($order)
                ->asarray();
        if($search != ''){
            $post   -> andFilterWhere(['like','u_follow_patient.u_MDID',$search])
                    -> orFilterWhere(['like','u_patientbase.u_patientname',$search])
                    -> orFilterWhere(['like','u_patientbase.u_phone',$search])
                    -> orFilterWhere(['like','u_patientbase.u_secondphone',$search]);
        }
        $result  = $post->all();
        foreach($result as $k=>$v){
            unset($result[$k]['uPatientbase']);
            if(in_array($v['u_follow_status'], [2,3,4,5,6])){
                $result[$k]['statusnote'] = '已取消';
            }
            if($v['u_follow_status'] == 7){
                $result[$k]['statusnote'] = '已保存';
            }
            if($v['u_follow_status'] == 10){
                $result[$k]['statusnote'] = '被退回';
            }
            if(in_array($v['u_follow_status'], [1,9])){
                $result[$k]['statusnote'] = '已提交';
            }
        }
        $num    = UFollowPatient::find()
                ->joinWith('uPatientbase')
                ->select('u_follow_patient.recordid')
                ->where(['u_follow_patient.projectid'=>$projectid,'u_follow_patient.updateuserid'=>$userid,'u_follow_patient.isuse'=>1,'u_patientbase.u_status'=>0])
                ->asarray();
        if($search != ''){
            $num    -> andFilterWhere(['like','u_follow_patient.u_MDID',$search])
            -> orFilterWhere(['like','u_patientbase.u_patientname',$search])
            -> orFilterWhere(['like','u_patientbase.u_phone',$search])
            -> orFilterWhere(['like','u_patientbase.u_secondphone',$search]);
        }
        $count  = $num->count();
        $arr    = array_values($result);
        if($type == 'status'){
            ArrayHelper::multisort($arr,'statusnote',$sort);
        }
        $data['result']      = $arr;
        $data['totalnum']    = $count;
        return $data;
    }
    /**
     * @todo 随访患者数据
     * @param recordid
     */
    public static function patientData($recordid){
        $result = UFollowPatient::find()
                ->select('recordid,u_follow_id,u_MDID,updatetime,updateuserid,sourcedata,patientdata,formdata,formname,projectid,u_follow_status')
                ->where(['recordid'=>$recordid])
                ->asarray()
                ->all();
        return $result;
    }
    /**
     * @todo 随访患者数据
     * @param mdid
     * @param taskid
     */
    public static function mPatientData($mdid,$taskid){
        $result = UFollowPatient::find()
        ->select('recordid,u_follow_id,u_MDID,updatetime,updateuserid,sourcedata,patientdata,formdata,formname,projectid,u_follow_status')
        ->where(['u_MDID'=>$mdid,'u_follow_id'=>$taskid,'isuse'=>1])
        ->asarray()
        ->all();
        foreach($result as $key=>$val){
            $result[$key]['commitlog'] = FCommitLog::commitLog($mdid,$taskid);
        }
        return $result;
    }
    /**
     * @todo 随访患者提交数据
     * @param mdid
     * @param taskid
     */
    public static function commitPatientData($mdid,$taskid){
        $result = UFollowPatient::find()
        ->select('recordid')
        ->where(['u_MDID'=>$mdid,'u_follow_id'=>$taskid,'isuse'=>1,'u_follow_status'=>[1,9]])
        ->asarray()
        ->all();
        return $result;
    }
    /**
     * @todo 保存/提交/退回
     */
    public static function statusList($userid,$projectid,$type,$pagenum,$search){
        if($type == 'save'){      #已保存
            $status = 7;
            $order  = ['u_follow_patient.updatetime' => SORT_DESC];
        }
        if($type == 'commit'){    #已提交
            $status = [1,9,10];
            $order  = ['u_follow_patient.updatetime' => SORT_DESC];
        }
        if($type == 'return'){    #被退回
            $status = 10;
            $order  = ['u_follow_patient.checktime' => SORT_DESC];
        }
        $where      = ['u_follow_patient.projectid'=>$projectid,'u_follow_patient.isuse'=>1,'u_follow_patient.u_follow_status'=>$status,'u_patientbase.u_status'=>0];
        //数据
        $start      = $pagenum-1 <= 0 ? 0 : ($pagenum-1) * 30;
        $post       = UFollowPatient::find()
                    ->joinWith('uPatientbase')
                    ->select('u_patientbase.u_patientname,u_patientbase.u_gender,u_patientbase.u_birthday,u_patientbase.u_phone,u_patientbase.u_secondphone,u_follow_patient.u_MDID,u_follow_patient.u_follow_status,u_follow_patient.updatetime,u_follow_patient.updateuserid,u_follow_patient.remark,u_follow_patient.checktime,u_follow_patient.recordid,u_follow_patient.u_follow_id,u_follow_patient.projectid');
        if($search != ''){
            $condition1 = ['and',$where,['like','u_follow_patient.u_MDID',$search]];
            $condition2 = ['and',$where,['like','u_patientbase.u_patientname',$search]];
            $condition3 = ['and',$where,['like','u_patientbase.u_phone',$search]];
            $condition4 = ['and',$where,['like','u_patientbase.u_secondphone',$search]];
            $post   ->where($condition1)
                    ->orwhere($condition2)
                    ->orwhere($condition3)
                    ->orwhere($condition4);
        }else{
            $post   ->where($where);
        }
        $post       ->orderby($order)
                    ->offset($start)
                    ->limit(30)
                    ->asarray();
        $list       = $post ->all();
        foreach($list as $k=>$v){
            unset($list[$k]['uPatientbase']);
            if($type == 'commit' || $type == 'save'){
                unset($list[$k]['remark']);
                unset($list[$k]['checktime']);
            }
            $list[$k]['username'] = MUserinfo::userInfo($v['updateuserid'])[0]['s_username'];
        }
        $num       = UFollowPatient::find()
                   ->joinWith('uPatientbase')
                   ->select('u_patientbase.u_patientname,u_patientbase.u_gender,u_patientbase.u_birthday,u_patientbase.u_phone,u_patientbase.u_secondphone,u_follow_patient.u_MDID,u_follow_patient.u_follow_status,u_follow_patient.updatetime,u_follow_patient.updateuserid,u_follow_patient.remark,u_follow_patient.checktime,u_follow_patient.recordid,u_follow_patient.u_follow_id,u_follow_patient.projectid');
        if($search != ''){
            $condition1 = ['and',$where,['like','u_follow_patient.u_MDID',$search]];
            $condition2 = ['and',$where,['like','u_patientbase.u_patientname',$search]];
            $condition3 = ['and',$where,['like','u_patientbase.u_phone',$search]];
            $condition4 = ['and',$where,['like','u_patientbase.u_secondphone',$search]];
            $num    ->where($condition1)
                    ->orwhere($condition2)
                    ->orwhere($condition3)
                    ->orwhere($condition4);
        }else{
            $num   ->where($where);
        }
        $count           = $num ->count();
        $arr['data']     = array_values($list);
        $arr['totalnum'] = $count;
        return $arr;
    }
    /**
     * @todo 某次随访处理过的患者
     * @param taskid
     */
    public static function getPatientid($taskid){
        $followpatient = UFollowPatient::find()
                       ->select('u_MDID')
                       ->where(['u_follow_id'=>$taskid,'u_follow_status'=>[1,7,9,10],'isuse'=>1])
                       ->asarray()
                       ->all();
        $patientid     = [];
        foreach($followpatient as $k=>$v){
            $patientid[] = $v['u_MDID'];
        }
        return $patientid;
    }
    /**
     * @todo 取消的患者随访
     * @param taskid
     */
    public static function returnFollow($taskid){
        $returnfollow   = UFollowPatient::find()
                        ->select('u_MDID')
                        ->where(['u_follow_id'=>$taskid,'u_follow_status'=>[2,3,4,5,6],'isuse'=>1])
                        ->asarray()
                        ->all();
        $patientid       = [];
        foreach($returnfollow as $k=>$v){
            $patientid[] = $v['u_MDID'];
        }
        return $patientid;
    }
    /**
     * @todo 患者上一次随访
     * @param projectid
     * @param patientid
     * @param taskid
     */
    public static function lastFollow($projectid,$patientid,$taskid){
        $info = UFollowPatient::find()
            ->select('u_MDID,u_follow_into_date,projectid')
            ->where(['u_MDID'=>$patientid,'projectid'=>$projectid,'isuse'=>1])
            ->andwhere(['not in','u_follow_status',[2,3,4,5,6]])
            ->andwhere(['<>','u_follow_id',$taskid])
            ->orderby(['u_follow_into_date'=>SORT_DESC])
            ->limit(1)
            ->one();
        if(empty($info)){
            $date = '';
        }else{
            $date = $info['u_follow_into_date'];
        }
        return $date;
    }
    /**
     * @todo 项目中随访提交/保存数量
     * @
     */
    public static function followStatusNum($projectid,$type){
        if($type == 'save'){      #已保存
            $status = 7;
        }
        if($type == 'commit'){    #已提交
            $status = [1,9];
        }
        if($type == 'return'){    #退回
            $status = 10;
        }
        $where      = ['u_follow_patient.projectid'=>$projectid,'u_follow_patient.u_follow_status'=>$status,'u_follow_patient.isuse'=>1,'u_patientbase.u_status'=>0];
        $num        = UFollowPatient::find()
                    ->joinWith('uPatientbase')
                    ->select('u_follow_patient.recordid')
                    ->where($where)
                    ->count();
        return $num;
    }
    /**
     * @todo 项目中今天随访提交/保存数量
     * @
     */
    public static function todayStatusNum($projectid,$type,$userid=''){
        if($type == 'save'){      #已保存
            $status = 7;
        }
        if($type == 'commit'){    #已提交
            $status = [1,9];
        }
        $where      = ['u_follow_patient.projectid'=>$projectid,'u_follow_patient.updateuserid'=>$userid,'u_follow_patient.u_follow_status'=>$status,'u_follow_patient.isuse'=>1,'u_patientbase.u_status'=>0];
        $num        = UFollowPatient::find()
                    ->joinWith('uPatientbase')
                    ->select('u_follow_patient.recordid')
                    ->where($where)
                    ->andwhere(['like','u_follow_patient.updatetime',date('Y-m-d')])
                    ->count();
        return $num;
    }
    /**
     * @todo 不同状态下的随访数量
     * @param projectid
     */
    public static function statusNum($userid,$projectid){
        //今日提交随访数
        $data['todaycommitnum']   = UFollowPatient::todayStatusNum($projectid,'commit');
        //今日保存随访数
        $data['todaysavenum']     = UFollowPatient::todayStatusNum($projectid,'save');
        //今日随访总数
        $data['todaynum']         = $data['todaycommitnum']+$data['todaysavenum'];
        //我的今日提交随访数
        $data['mytodaycommitnum'] = UFollowPatient::todayStatusNum($projectid,'commit',$userid);
        //我的今日保存随访数
        $data['mytodaysavenum']   = UFollowPatient::todayStatusNum($projectid,'save',$userid);
        //我的今日随访总数
        $data['mytodaynum']       = $data['mytodaycommitnum']+$data['mytodaysavenum'];
        //应随访患者数
        $data['shouldnum']        = UPatientbase::shouldNum($userid, $projectid);
        //已保存
        $data['savenum']          = UFollowPatient::followStatusNum($projectid,'save');
        //已提交
        $data['commitnum']        = UFollowPatient::followStatusNum($projectid,'commit');
        //被退回
        $data['returnnum']        = UFollowPatient::followStatusNum($projectid,'return');
        //我的工作日志总数
        $data['workdatanum']      = UFollowPatient::workdataNum($userid, $projectid);
        return $data;
    }
    /**
     * @todo 工作台日志数量
     * 
     */
    public static function workdataNum($userid, $projectid){
        $num    = UFollowPatient::find()
        ->joinWith('uPatientbase')
        ->select('u_follow_patient.recordid')
        ->where(['u_follow_patient.projectid'=>$projectid,'u_follow_patient.updateuserid'=>$userid,'u_follow_patient.isuse'=>1,'u_patientbase.u_status'=>0])
        ->count();
        return $num;
    }
    /**
     * @todo 每个患者的随访历史记录(已经提交成功的)
     * @param mdid
     */
    public static function followPatientList($mdid){
        $follow = UFollowPatient::find()
                ->select('u_MDID,updateuserid,checktime,recordid,u_follow_id')
                ->where(['u_MDID'=>$mdid,'isuse'=>1,'u_follow_status'=>1])
                ->asarray()
                ->all();
        foreach($follow as $k=>$v){
            //用户名
            $follow[$k]['username'] = MUserinfo::userInfo($v['updateuserid'])[0]['s_username'];
            //随访名称
            $follow[$k]['typename'] = UProjectTask::followInfo($v['u_follow_id'])['taskname'];
            //类型
            $follow[$k]['type']     = 'follow';
            //状态
            $follow[$k]['status']   = 1;
        }
        return $follow;
    }
    /**
     * @todo 每个患者的随访历史记录(所有的)
     * @param mdid
     */
    public static function allFollowPatientList($mdid){
        $follow = UFollowPatient::find()
        ->select('u_MDID,updateuserid,checktime,recordid,u_follow_id,updatetime,u_follow_status as status')
        ->where(['u_MDID'=>$mdid,'isuse'=>1])
        ->asarray()
        ->all();
        foreach($follow as $k=>$v){
            //用户名
            $follow[$k]['username'] = MUserinfo::userInfo($v['updateuserid'])[0]['s_username'];
            //随访名称
            $follow[$k]['typename'] = UProjectTask::followInfo($v['u_follow_id'])['taskname'];
            //随访月份
            $follow[$k]['month']    = UProjectTask::followInfo($v['u_follow_id'])['taskmonth'];
            //类型
            $follow[$k]['type']     = 'follow';
        }
        return $follow;
    }
    /**
     * @todo 患者随访
     */
    public static function patientfollows($mdid){
        $follow = UFollowPatient::find()
                ->where(['u_MDID'=>$mdid])
                ->orderby(['isuse'=>1,'updatetime'=>SORT_DESC])
                ->limit(1)
                ->asarray()
                ->one();
        return $follow;
    }
    /**
     * @todo 随访数据通过审核
     * 
     */
    public static function checkPatient($userid){
        $recordid   = Yii::$app->getRequest()->getBodyParam('recordid');
        //此条记录的信息
        $followinfo = UFollowPatient::patientData($recordid);
        $model      = UFollowPatient::findOne(['recordid'=>$recordid]);
        $model -> u_follow_status = Yii::$app->getRequest()->getBodyParam('status');
        $model -> checkuserid     = $userid;
        $model -> checktime       = date('Y-m-d H:i:s');
        $model -> remark          = Yii::$app->getRequest()->getBodyParam('remark');
        if($model -> save()){
            FCommitLog::addLog($followinfo[0]['u_MDID'], $followinfo[0]['u_follow_id'], $userid, $model -> u_follow_status,$model -> remark);
            //通知
            if($model->u_follow_status == 10){
                UMessage::updateMessage($userid, $model->updateuserid, 302, 1,'',$recordid);
            }
            return true;
        }else{
            return false;
        }
    }
}
