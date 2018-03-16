<?php

namespace app\models;

use Yii;
use app\models\UPatientbase;
use app\models\UCenterUser;
use app\models\MUserinfo;
use app\models\UProjectdata;
use app\models\RCommitLog;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Cell;
use app\models\UMessage;
/**
 * This is the model class for table "r_newpatientdata".
 *
 * @property integer $id
 * @property string $mdid
 * @property string $patientdata
 * @property string $sourcedata
 * @property string $createuserid
 * @property string $createtime
 * @property string $updateuserid
 * @property string $updatetime
 * @property integer $recordid
 * @property integer $status
 * @property string $remark
 * @property string $checkuserid
 * @property string $checktime
 */
class RNewpatientdata extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_newpatientdata';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mdid'], 'required'],
            [['patientdata', 'sourcedata', 'remark','template','formname'], 'string'],
            [['createtime', 'updatetime', 'checktime'], 'safe'],
            [['recordid', 'status','projectid'], 'integer'],
            [['mdid', 'createuserid', 'updateuserid', 'checkuserid'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mdid' => 'Mdid',
            'patientdata' => 'Patientdata',
            'sourcedata' => 'Sourcedata',
            'createuserid' => 'Createuserid',
            'createtime' => 'Createtime',
            'updateuserid' => 'Updateuserid',
            'updatetime' => 'Updatetime',
            'recordid' => 'Recordid',
            'status' => 'Status',
            'remark' => 'Remark',
            'checkuserid' => 'Checkuserid',
            'checktime' => 'Checktime',
        ];
    }
    /**
     * @todo 关联患者基本信息表
     */
    public function getUPatientbase(){
        return $this->hasMany(UPatientbase::className(), ['u_MDID' => 'mdid']);
    }
    /**
     * @todo 患者id查询病例
     * @param mdid
     */
    public static function patientRecord($mdid){
        $patient = RNewpatientdata::find()
                 -> where(['mdid'=>$mdid])
                 -> asarray()
                 -> one();
        return $patient;
    }
    /**
     * @todo 患者id查询病例zhuangtai
     * @param mdid
     */
    public static function patientRecordStatus($mdid){
        $patient = RNewpatientdata::find()
        ->select('updateuserid,status')
        -> where(['mdid'=>$mdid])
        -> asarray()
        -> one();
        return $patient;
    }
    /**
     * @todo 最大的记录id
     * return   int
     */
    public static function maxRecordid(){
        $arr = RNewpatientdata::find()
        ->select('recordid')
        ->orderBy(['recordid'=>SORT_DESC])
        ->limit(1)
        ->asarray()
        ->all();
        if(empty($arr) || $arr[0]['recordid']==''){
            $record = 1;
        }else{
            $record = $arr[0]['recordid']+1;
        }
        return $record;
    }
    /**
     * @todo 创建病例
     */
    public static function createRecord(){
        $mdid      = Yii::$app->getRequest()->getBodyParam('mdid');
        if($mdid == ''){
            //创建患者基本信息
            $randmdid = Commonfun::randpw();
            $mdid     = UPatientbase::createPatient($randmdid);
            //创建患者详情
            $model = new RNewpatientdata();
            $model -> mdid         = $mdid;
            $model -> projectid    = Yii::$app->getRequest()->getBodyParam('projectid');
            $model -> patientdata  = Yii::$app->getRequest()->getBodyParam('patientdata');
            $model -> sourcedata   = Yii::$app->getRequest()->getBodyParam('sourcedata');
            $model -> status       = Yii::$app->getRequest()->getBodyParam('status');
            $model -> createuserid = Yii::$app->getRequest()->getBodyParam('userid');
            $model -> updateuserid = Yii::$app->getRequest()->getBodyParam('userid');
            $model -> createtime   = date('Y-m-d H:i:s');
            $model -> updatetime   = date('Y-m-d H:i:s');
            $model -> recordid     = RNewpatientdata::maxRecordid();
            //模板
            $model -> template     = UProjectdata::getProjectDetailByProjectId($model -> projectid)[0]['formdata'];
            $model -> formname     = UProjectdata::getProjectDetailByProjectId($model -> projectid)[0]['formname'];
            if($model -> save()){
                if($model ->status == 2){
                    RCommitLog::addLog($mdid, $model -> updateuserid, '',2);
                }
                //修改患者基本信息里修改时间
                UPatientbase::uptime($mdid, $model -> updatetime);
                //写入日志
                //日志内容
                $filename                  = date('Y-m-d').'-RNewpatientdata.log';
                $content['mdid']           = $model -> mdid;
                $content['projectid']      = $model -> projectid;
                $content['patientdata']    = $model -> patientdata;
                $content['sourcedata']     = $model -> sourcedata;
                $content['status']         = $model -> status;
                $content['createuserid']   = $model -> createuserid;
                $content['updateuserid']   = $model -> updateuserid;
                $content['createtime']     = $model -> createtime;
                $content['updatetime']     = $model -> updatetime;
                $content['recordid']       = $model -> recordid;
                //模板
                $content['template']       = $model -> template;
                $content['formname']       = $model -> formname;
                Commonfun::createLog($filename,$content);
                $return['type'] = 'add';
                $return['mdid'] = $model -> mdid;
                return $return;
            }else{
                $return['type'] = 'false';
                return $return;
            }
        }else{
            //编辑基本信息
            UPatientbase::updatePatientbase();
            //是否已经有此病例
            $patient   = RNewpatientdata::patientRecord($mdid);
            //有则修改，没有创建新数据
            if(empty($patient)){
                //患者所在项目
                $model = new RNewpatientdata();
                $model -> mdid         = $mdid;
                $model -> projectid    = Yii::$app->getRequest()->getBodyParam('projectid');
                $model -> patientdata  = Yii::$app->getRequest()->getBodyParam('patientdata');
                $model -> sourcedata   = Yii::$app->getRequest()->getBodyParam('sourcedata');
                $model -> status       = Yii::$app->getRequest()->getBodyParam('status');
                $model -> createuserid = Yii::$app->getRequest()->getBodyParam('userid');
                $model -> updateuserid = Yii::$app->getRequest()->getBodyParam('userid');
                $model -> createtime   = date('Y-m-d H:i:s');
                $model -> updatetime   = date('Y-m-d H:i:s');
                $model -> recordid     = RNewpatientdata::maxRecordid();
                //模板
                $model -> template     = UProjectdata::getProjectDetailByProjectId($model -> projectid)[0]['formdata'];
                $model -> formname     = UProjectdata::getProjectDetailByProjectId($model -> projectid)[0]['formname'];
                if($model -> save()){
                    //修改患者基本信息里修改时间
                    UPatientbase::uptime($mdid, $model -> updatetime);
                    //写入日志
                    //日志内容
                    $filename                  = date('Y-m-d').'-RNewpatientdata.log';
                    $content['mdid']           = $model -> mdid;
                    $content['projectid']      = $model -> projectid;
                    $content['patientdata']    = $model -> patientdata;
                    $content['sourcedata']     = $model -> sourcedata;
                    $content['status']         = $model -> status;
                    $content['createuserid']   = $model -> createuserid;
                    $content['updateuserid']   = $model -> updateuserid;
                    $content['createtime']     = $model -> createtime;
                    $content['updatetime']     = $model -> updatetime;
                    $content['recordid']       = $model -> recordid;
                    //模板
                    $content['template']       = $model -> template;
                    $content['formname']       = $model -> formname;
                    Commonfun::createLog($filename,$content);
                    $return['type'] = 'edit';
                    return $return;
                }else{
                    $return['type'] = 'false';
                    return $return;
                }
            }else{
                if($patient['status'] == 4){
                    return false;
                }
                $model = RNewpatientdata::findOne(['mdid'=>$mdid]);
                $model -> patientdata  = Yii::$app->getRequest()->getBodyParam('patientdata');
                $model -> sourcedata   = Yii::$app->getRequest()->getBodyParam('sourcedata');
                $model -> status       = Yii::$app->getRequest()->getBodyParam('status');
                $model -> updateuserid = Yii::$app->getRequest()->getBodyParam('userid');
                $model -> updatetime   = date('Y-m-d H:i:s');
                if($model -> save()){
                    //修改患者基本信息里修改时间
                    UPatientbase::uptime($mdid, $model -> updatetime);
                    //写入日志
                    //日志内容
                    $filename                  = date('Y-m-d').'-RNewpatientdata.log';
                    $content['mdid']           = $model -> mdid;
                    $content['projectid']      = $model -> projectid;
                    $content['patientdata']    = $model -> patientdata;
                    $content['sourcedata']     = $model -> sourcedata;
                    $content['status']         = $model -> status;
                    $content['createuserid']   = $model -> createuserid;
                    $content['updateuserid']   = $model -> updateuserid;
                    $content['createtime']     = $model -> createtime;
                    $content['updatetime']     = $model -> updatetime;
                    $content['recordid']       = $model -> recordid;
                    //模板
                    $content['template']       = $model -> template;
                    $content['formname']       = $model -> formname;
                    Commonfun::createLog($filename,$content);
                    $return['type'] = 'edit';
                    return $return;
                }else{
                    $return['type'] = 'false';
                    return $return;
                }
            }
        }
        
        
    }
    /**
     * @todo 用户项目中提交患者数量
     * @param userid
     * @param projectid
     */
    public static function commitNum($userid,$projectid){
        //用户所在分中心用户
        $useridarr = UCenterUser::getCenteruser($userid,$projectid);
        $useridarr = array_values($useridarr);
        if(empty($useridarr[0])){
            $useridarr = [$userid];
        }
        $num       = RNewpatientdata::find()
                   ->joinWith('uPatientbase')
                   ->where(['updateuserid'=>$useridarr,'projectid'=>$projectid,'status'=>[2,4],'u_patientbase.u_status'=>0])
                   ->count();
        return $num;
    }
    /**
     * @todo 用户项目中保存的患者数量（所在分中心）
     * @param userid
     * @param projectid
     */
    public static function saveNum($userid,$projectid){
        //用户所在分中心用户
        $useridarr = UCenterUser::getCenteruser($userid,$projectid);
        $useridarr = array_values($useridarr);
        if(empty($useridarr[0])){
            $useridarr = [$userid];
        }
        $num       = RNewpatientdata::find()
                   ->joinWith('uPatientbase')
                   ->where(['updateuserid'=>$useridarr,'projectid'=>$projectid,'status'=>1,'u_patientbase.u_status'=>0])
                   ->count();
        return $num;
    }
    /**
     * @todo 今天提交和保存的数量
     * @param userid
     * @param projectid
     */
    public static function getTodayNum($userid,$projectid){
        //用户所在分中心用户
        $useridarr   = UCenterUser::getCenteruser($userid,$projectid);
        $useridarr   = array_values($useridarr);
        if(empty($useridarr[0])){
            $useridarr = [$userid];
        }
        $savenum     = RNewpatientdata::find()
                     ->where(['updateuserid'=>$useridarr,'projectid'=>$projectid,'status'=>1])
                     ->andwhere(['like','updatetime',date('Y-m-d')])
                     ->count();
        $commitnum   = RNewpatientdata::find()
                     ->where(['updateuserid'=>$useridarr,'projectid'=>$projectid,'status'=>[2,3,4]])
                     ->andwhere(['like','updatetime',date('Y-m-d')])
                     ->count();
        $result['savenum']    = $savenum;
        $result['commitnum']  = $commitnum;
        return $result;
    }
    /**
     * @todo 工作日志
     * @param userid
     * @param projectid
     */
    public static function patientList($userid,$projectid,$pagenum,$type,$sort,$search){
        if($sort == 'asc'){
            $sort = SORT_ASC;    #正序
        }
        if($sort == 'desc'){
            $sort = SORT_DESC;   #倒叙
        }
        if($type == 'updatetime'){
            $order = ['r_newpatientdata.updatetime' => $sort];#更新时间
        }
        if($type == 'status'){
            $order = ['r_newpatientdata.status'     => $sort];#病例状态
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
        $data   = UPatientbase::rWorkData($projectid,$userid,$start,$order,$search);
        $result = $data['result'];
        $count  = $data['count'];
        foreach($result as $k=>$v){
            unset($result[$k]['rNewpatientdata']);
        }
        $arr                 = array_values($result);
        $data['result']      = $arr;
        $data['totalnum']    = $count;
        return $data;
    }
    /**
     * @todo 已保存/已提交/被退回的病例
     * @param userid
     * @param projectid
     * @param type
     * @param search
     */
    public static function statusList($userid,$projectid,$type,$pagenum,$search){
        if($type == 'save'){      #已保存
            $status = 1;
            $order  = ['r_newpatientdata.updatetime' => SORT_DESC];
        }
        if($type == 'commit'){    #已提交
            $status = [2,4];
            $order  = ['r_newpatientdata.updatetime' => SORT_DESC];
        }
        if($type == 'return'){    #被退回
            $status = 3;
            $order  = ['r_newpatientdata.checktime' => SORT_DESC];
        }
        if($type == 'all'){
            $status = [1,2,3,4];
            $order  = ['r_newpatientdata.updatetime' => SORT_DESC];
        }
        //用户所在中心成员
        $useridarr  = UCenterUser::getCenteruser($userid,$projectid);
        $useridarr  = array_values($useridarr);
        
        if(empty($useridarr[0])){
            $useridarr = [$userid];
        }
        $where      = ['r_newpatientdata.updateuserid'=>$useridarr,'r_newpatientdata.projectid'=>$projectid,'r_newpatientdata.status'=>$status,'u_patientbase.u_status'=>0];
        
        //数据
        $start      = $pagenum-1 <= 0 ? 0 : ($pagenum-1) * 30;
        $post       = RNewpatientdata::find()
                    ->joinWith('uPatientbase')
                    ->select('u_patientbase.u_patientname,u_patientbase.u_createuserid,u_patientbase.u_gender,u_patientbase.u_birthday,u_patientbase.u_phone,u_patientbase.u_secondphone,u_patientbase.u_jointime,r_newpatientdata.mdid,r_newpatientdata.status,r_newpatientdata.updatetime,r_newpatientdata.updateuserid,r_newpatientdata.remark,r_newpatientdata.checktime');
        if($search != ''){
            $condition1 = ['and',$where,['like','r_newpatientdata.mdid',$search]];
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
        $postnum    = RNewpatientdata::find()
                    ->joinWith('uPatientbase')
                    ->select('u_patientbase.u_patientname,u_patientbase.u_gender,u_patientbase.u_birthday,u_patientbase.u_phone,u_patientbase.u_secondphone,r_newpatientdata.mdid,r_newpatientdata.status,r_newpatientdata.updatetime,r_newpatientdata.updateuserid,r_newpatientdata.remark,r_newpatientdata.checktime');
        if($search != ''){
            $condition1 = ['and',$where,['like','r_newpatientdata.mdid',$search]];
            $condition2 = ['and',$where,['like','u_patientbase.u_patientname',$search]];
            $condition3 = ['and',$where,['like','u_patientbase.u_phone',$search]];
            $condition4 = ['and',$where,['like','u_patientbase.u_secondphone',$search]];
            $postnum   ->where($condition1)
                       ->orwhere($condition2)
                       ->orwhere($condition3)
                       ->orwhere($condition4);
            }else{
                $postnum   ->where($where);
            }
        $num             = $postnum ->count();
        $arr['data']     = array_values($list);
        $arr['totalnum'] = $num;
        return $arr;
    }
    /**
     * @todo 各个状态下患者数量
     * @param userid
     * @param projectid
     */
    public static function statusNum($userid,$projectid){
        //workdata num
        $workdata   = RNewpatientdata::find()
                    ->joinWith('uPatientbase')
                    ->select('u_patientbase.u_patientname,u_patientbase.u_gender,u_patientbase.u_birthday,u_patientbase.u_phone,u_patientbase.u_secondphone,r_newpatientdata.mdid,r_newpatientdata.status,r_newpatientdata.updatetime')
                    ->where(['r_newpatientdata.projectid'=>$projectid,'r_newpatientdata.updateuserid'=>$userid,'u_patientbase.u_status'=>0])
                    ->count();
        //用户所在中心成员
        $useridarr  = UCenterUser::getCenteruser($userid,$projectid);
        $useridarr  = array_values($useridarr);
        if(empty($useridarr[0])){
            $useridarr = [$userid];
        }
        //save num
        $where1     = ['r_newpatientdata.updateuserid'=>$useridarr,'r_newpatientdata.projectid'=>$projectid,'r_newpatientdata.status'=>1,'u_patientbase.u_status'=>0];
        $savenum    = RNewpatientdata::find()
                    ->joinWith('uPatientbase')
                    ->select('u_patientbase.u_patientname,u_patientbase.u_gender,u_patientbase.u_birthday,u_patientbase.u_phone,u_patientbase.u_secondphone,r_newpatientdata.mdid,r_newpatientdata.status,r_newpatientdata.updatetime,r_newpatientdata.updateuserid,r_newpatientdata.remark,r_newpatientdata.checktime')
                    ->where($where1)
                    ->count();
        //commit num
        $where2     = ['r_newpatientdata.updateuserid'=>$useridarr,'r_newpatientdata.projectid'=>$projectid,'r_newpatientdata.status'=>[2,4],'u_patientbase.u_status'=>0];
        $commitnum  = RNewpatientdata::find()
                    ->joinWith('uPatientbase')
                    ->select('u_patientbase.u_patientname,u_patientbase.u_gender,u_patientbase.u_birthday,u_patientbase.u_phone,u_patientbase.u_secondphone,r_newpatientdata.mdid,r_newpatientdata.status,r_newpatientdata.updatetime,r_newpatientdata.updateuserid,r_newpatientdata.remark,r_newpatientdata.checktime')
                    ->where($where2)
                    ->count();
        //return num
        $where3     = ['r_newpatientdata.updateuserid'=>$useridarr,'r_newpatientdata.projectid'=>$projectid,'r_newpatientdata.status'=>3,'u_patientbase.u_status'=>0];
        $returnnum  = RNewpatientdata::find()
                    ->joinWith('uPatientbase')
                    ->select('u_patientbase.u_patientname,u_patientbase.u_gender,u_patientbase.u_birthday,u_patientbase.u_phone,u_patientbase.u_secondphone,r_newpatientdata.mdid,r_newpatientdata.status,r_newpatientdata.updatetime,r_newpatientdata.updateuserid,r_newpatientdata.remark,r_newpatientdata.checktime')
                    ->where($where3)
                    ->count();
        $result['workdatanum'] = $workdata;
        $result['savenum']     = $savenum;
        $result['commitnum']   = $commitnum;
        $result['returnnum']   = $returnnum;
        return $result;
    }
    /**
     * @todo 患者病例数据
     * @param patientid
     */
    public static function patientData($patientid){
        $result = RNewpatientdata::find()
                ->select('mdid,patientdata,sourcedata,projectid,updateuserid,updatetime,status,template,formname,remark')
                ->where(['mdid'=>$patientid])
                ->asarray()
                ->all();
        foreach ($result as $k=>$v){
            $result[$k]['commitlog'] = RCommitLog::commitLog($v['mdid']);
        }
        return $result;
    }
    /**
     * @todo 审核患者
     */
    public static function checkPatient($userid){
        $mdid  = Yii::$app->getRequest()->getBodyParam('mdid');
        $model = RNewpatientdata::findOne(['mdid'=>$mdid]);
        $model -> status      = Yii::$app->getRequest()->getBodyParam('status');
        $model -> checkuserid = $userid;
        $model -> checktime   = date('Y-m-d H:i:s');
        $model -> remark      = Yii::$app->getRequest()->getBodyParam('remark');
        if($model -> save()){
            //审核表里新加
            RCommitLog::addLog($mdid, $userid, $model -> remark, $model -> status);
            //通知
            if($model->status == 3){
                UMessage::updateMessage($userid, $model->updateuserid, 301, 1,'',$mdid);
            }
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 批量审核
     */
    public static function passCheck($userid,$mdid){
        //患者最新动作
        $mdidarr = explode(',', $mdid);
        foreach($mdidarr as $k=>$v){
            //患者是否随访过
            $follow = UFollowPatient::patientfollows($v);
            if(empty($follow)){
                //登记通过审核
                RNewpatientdata::updateAll(['status'=>4,'checkuserid'=>$userid,'checktime'=>date('Y-m-d H:i:s')],['mdid'=>$v]);
                //审核表里新加
                RCommitLog::addLog($mdid, $userid, '', 4);
            }else{
                UFollowPatient::updateAll(['u_follow_status'=>1,'checkuserid'=>$userid,'checktime'=>date('Y-m-d H:i:s')],['recordid'=>$follow['recordid']]);
                //记录
                //此条记录的信息
                $followinfo = UFollowPatient::patientData($follow['recordid']);
                FCommitLog::addLog($v, $followinfo[0]['u_follow_id'], $userid, 1,'');
            }
        }
        return true;
    }
    /**
     * @todo 导出患者
     * @param mdid
     */
    public static function exportPatient($mdid){
        $projectid = Yii::$app->request->get('projectid');
        if($projectid != ''){
            $mdidarr = UPatientbase::patientids($projectid);
        }else{
            $mdidarr = explode(',',$mdid);
        }
        $result  = UPatientbase::rpatientInfo($mdidarr);
        if(!empty($result)){
            $patient = [];
            foreach ($result as $k=>$v){
                $patient[$v['u_MDID']]['u_MDID']         = $v['u_MDID'];
                $patient[$v['u_MDID']]['u_patientname']  = $v['u_patientname'];
                $patient[$v['u_MDID']]['u_gender']       = $v['u_gender'];
                $patient[$v['u_MDID']]['u_birthday']     = $v['u_birthday'];
                $patient[$v['u_MDID']]['u_jointime']     = $v['u_jointime'];
                $patient[$v['u_MDID']]['u_address']      = $v['u_address'];
                $patient[$v['u_MDID']]['u_phone']        = $v['u_phone'];
                $patient[$v['u_MDID']]['u_secondphone']  = $v['u_secondphone'];
                $patient[$v['u_MDID']]['username']       = MUserinfo::userInfo($v['rNewpatientdata'][0]['updateuserid'])[0]['s_username'];
                $patient[$v['u_MDID']]['center']         = UCenterUser::getCenterId($v['rNewpatientdata'][0]['updateuserid'],$v['rNewpatientdata'][0]['projectid'])['u_centername'];
                unset($result[$k]['rNewpatientdata']);
                $patientdata = json_decode($v['patientdata'],true);
                $flag = 0;#防止键相同覆盖前边键值
                foreach ($patientdata as $key=>$val){
                    if($val['title'] == 'SECTION'){
                        unset($patientdata[$key]);
                    }else{
                        $flag++;
                        $patient[$v['u_MDID']][$val['title'].'-fenge'.$flag] = $val['answer'];
                    }
                }
            }
            //模板信息(最近一条是最新模板信息)
            $forminfo = json_decode($result[0]['patientdata'],true);
            //取出模板字段
            $formarr   = [];
            $formarr[] = 'u_MDID';
            $formarr[] = '姓名';
            $formarr[] = '性别（1男2女）';
            $formarr[] = '生日';
            $formarr[] = '入组时间';
            $formarr[] = '地址';
            $formarr[] = '电话';
            $formarr[] = '备用电话';
            $formarr[] = '录入人';
            $formarr[] = '中心';
            foreach ($forminfo as $k=>$v){
                if($v['title'] == 'SECTION'){
                    unset($forminfo[$k]);
                }else{
                    $formarr[] = $v['title'];
                }
            }
            //需要导出的内容
            $objPHPExcel = new PHPExcel();
            //字段
            $count = 0; #列
            foreach($formarr as $k=>$v){
                //数字转化excel字母
                $letter = PHPExcel_Cell::stringFromColumnIndex($count);
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($letter.'1', $v);
                $count++;
            }
            $num = 2;#计算行 从第二行开始
            foreach($patient as $k=>$v){
                $let = 0; #计算字母 列
                foreach($v as $key=>$val){
                    //数字转化excel字母
                    $letter = PHPExcel_Cell::stringFromColumnIndex($let);
                    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($letter.$num, $val);
                    $let++;
                }
                $num++;
            }
            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('患者统计数据');
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="statistics_patient.xls"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            
        }
        return true;
    }
}