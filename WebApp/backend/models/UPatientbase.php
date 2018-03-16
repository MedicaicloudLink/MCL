<?php

namespace app\models;

use Yii;
use app\models\UUserProject;
use app\models\UProjectdata;
use app\models\UPatientdata;
use app\models\UPatientgroup;
use yii\helpers\ArrayHelper;
use app\models\UProjectTask;
/**
 * This is the model class for table "u_patientbase".
 *
 * @property integer $u_id
 * @property string $u_MDID
 * @property string $u_projectid
 * @property string $u_patientname
 * @property integer $u_gender
 * @property string $u_birthday
 * @property string $u_createuserid
 * @property string $u_createtime
 */
class UPatientbase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_patientbase';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_MDID', 'u_projectid', 'u_patientname', 'u_gender'], 'required'],
            [['u_gender'], 'integer'],
            [['u_birthday', 'u_createtime','u_jointime','u_status','u_updatetime'], 'safe'],
            [['u_MDID', 'u_projectid', 'u_patientname','u_createuserid','u_updateuserid', 'u_address','u_phone','u_secondphone'], 'string', 'max' => 32],
        ];
    }
    /**
     * @todo 关联项目表
     */
    public function getUProjectdata(){
        return $this->hasMany(UProjectdata::className(), ['u_projectID' => 'u_projectid']);
    }
    /**
     * @todo 关联用户表
     */
    public function getMUserinfo(){
        return $this->hasMany(MUserinfo::className(), ['s_userid' => 'u_createuserid']);
    }
    /**
     * @todo 关联患者数据
     */
    public function getUPatientdata(){
        return $this->hasMany(UPatientdata::className(), ['u_MDID' => 'u_MDID']);
    }
    /**
     * @todo 关联随访患者数据
     */
    public function getUFollowPatient(){
        return $this->hasMany(UFollowPatient::className(), ['u_MDID' => 'u_MDID']);
    }
    /**
     * @todo 关联患者录入端患者数据表
     */
    public function getRNewpatientdata(){
        return $this->hasMany(RNewpatientdata::className(), ['mdid'  => 'u_MDID']);
    }
    /**
     * @todo 创建患者记录
     * @param userid    创建者id  
     * @param projectid 项目id
     * @param name      患者姓名
     * @param gender    性别
     * @param birthday  生日
     * @param jointime  入组时间
     * @param u_phone
     * @param u_secondphone
     * @param u_address
     * @return recordID 新纪录id
     * date 2017-02-04
     */
    public static function createPatient($mdid){
        $model = new UPatientbase();
        $model -> u_createuserid = Yii::$app->getRequest()->getBodyParam('userid');
        $model -> u_createtime   = date('Y-m-d H:i:s');
        $model -> u_updatetime   = date('Y-m-d H:i:s');
        $model -> u_birthday     = empty(Yii::$app->getRequest()->getBodyParam('birthday')) ? null : Yii::$app->getRequest()->getBodyParam('birthday');
        $model -> u_gender       = Yii::$app->getRequest()->getBodyParam('gender');
        $model -> u_patientname  = Yii::$app->getRequest()->getBodyParam('name');
        $model -> u_projectid    = Yii::$app->getRequest()->getBodyParam('projectid');
        $model -> u_MDID         = $mdid;
        $model -> u_jointime     = empty(Yii::$app->getRequest()->getBodyParam('jointime')) ? date('Y-m-d H:i:s') : Yii::$app->getRequest()->getBodyParam('jointime');
        $model -> u_phone        = Yii::$app->getRequest()->getBodyParam('phone');
        $model -> u_secondphone  = Yii::$app->getRequest()->getBodyParam('secondphone');
        $model -> u_address      = Yii::$app->getRequest()->getBodyParam('address');
        if($model -> save()){
            return $model -> u_MDID;
        }else{
            return false;
        }
    }
    /**
     * @todo 获取患者基本信息
     * @param mdid
     */
    public static function getPatientBase($mdid){
        $arr = UPatientbase::find()
        ->select('u_patientbase.*,m_userinfo.s_username,m_userinfo.s_formalname,u_projectdata.u_projectName')
        ->joinWith('mUserinfo')
        ->joinWith('uProjectdata')
        ->where(['u_patientbase.u_MDID'=>$mdid,'u_patientbase.u_status'=>0])
        ->asarray()
        ->all();
        
        foreach($arr as $k=>$v){
            unset($arr[$k]['mUserinfo']);
            unset($arr[$k]['uProjectdata']);
        }
        return $arr;
    }
    /**
     * @todo 获取患者基本信息
     * @param mdid
     */
    public static function getPatientInfo($mdid){
        $arr = UPatientbase::find()
        ->select('u_jointime,u_projectid,u_createuserid')
        ->where(['u_patientbase.u_MDID'=>$mdid,'u_patientbase.u_status'=>0])
        ->asarray()
        ->one();
        return $arr;
    }
    /**
     * @todo 获取项目下患者
     * @param projectid
     * @param pagenum
     * @return array 
     */
    public static function getProjectPatients($projectid,$userid,$pagenum,$type){
        //判断用户在项目中身份
        $rolearr    = UUserProject::find()
                    ->select('u_permission')
                    ->where(['u_projectID'=>$projectid,'u_userID'=>$userid])
                    ->asarray()
                    ->one();
        $role       = $rolearr['u_permission'];
        //项目名称
        $projectinfo = UProjectdata::find()
                     ->select('u_projectName')
                     ->where(['u_projectID'=>$projectid])
                     ->asarray()
                     ->all();
        $projectname =  empty($projectinfo[0]['u_projectName'])?'':$projectinfo[0]['u_projectName'];
        $start       = $pagenum-1 <= 0 ? 0 : ($pagenum-1) * 30;
        if($role == 1){
            $results    = UPatientbase::find()
                        ->select('u_patientbase.*,r_newpatientdata.status')
                        ->joinWith('rNewpatientdata')
                        ->where(['u_patientbase.u_projectid'=>$projectid,'u_patientbase.u_status'=>0,'r_newpatientdata.status'=>[2,3,4]])
                        ->offset($start)
                        ->limit(30)
                        ->orderby(['u_patientbase.u_updatetime'=>SORT_DESC])
                        ->asarray()
                        ->all();
            $count      = UPatientbase::find()
                        ->select('u_patientbase.u_MDID')
                        ->joinWith('rNewpatientdata')
                        ->where(['u_patientbase.u_projectid'=>$projectid,'u_patientbase.u_status'=>0,'r_newpatientdata.status'=>[2,3,4]])
                        ->count();
        }else{
            $results    = UPatientbase::find()
                        ->select('u_patientbase.*,r_newpatientdata.status')
                        ->joinWith('rNewpatientdata')
                        ->where(['u_patientbase.u_projectid'=>$projectid,'u_patientbase.u_status'=>0,'u_createuserid'=>$userid,'r_newpatientdata.status'=>[2,3,4]])
                        ->offset($start)
                        ->limit(30)
                        ->orderby(['u_patientbase.u_updatetime'=>SORT_DESC])
                        ->asarray()
                        ->all();
            $count      = UPatientbase::find()
                        ->select('u_patientbase.u_MDID')
                        ->joinWith('rNewpatientdata')
                        ->where(['u_patientbase.u_projectid'=>$projectid,'u_patientbase.u_status'=>0,'u_createuserid'=>$userid,'r_newpatientdata.status'=>[2,3,4]])
                        ->count();
        }
        foreach ($results as $k=>$v){
            $results[$k]['projectname'] = $projectname;
            if($type == 1){
                //组名
                $results[$k]['group']       = UPatientgroup::patientGroup($userid,$v['u_MDID']);
                //应随访次数
                $follownum                  = UFollowPatient::follownum($userid, $projectid);
                //最新内容
                $lastinfo                   = UFollowPatient::getFollowStatus($projectid,$v['u_MDID'],$userid,$follownum,$v['u_jointime']);
                //随访状态
                $results[$k]['follow']      = $lastinfo['status'];
                $results[$k]['s_username']  = $lastinfo['username'];
                $results[$k]['status']      = $lastinfo['check'];
                $results[$k]['center']      = $lastinfo['center'];
            }else{
                $results[$k]['address']     = UPatientdata::lastAddress($v['u_MDID']);
            }
            unset($results[$k]['rNewpatientdata']);
        }
        $arr['allnum']      = $count;
        $arr['patientList'] = $results;
        return $arr;
    }
    /**
     * @todo 用户的患者列表    
     * @param userid
     * @param pagenum
     * @return array   返回项目名称
     */
    public static function patientList($userid,$pagenum){
        $start   = $pagenum-1 <= 0 ? 0 : ($pagenum-1) * 30;
        //用户身份为管理员参加的项目 
        $project = UUserProject::find()
        ->select('u_projectid')
        ->where(['u_userID'=>$userid,'u_permission'=>1])
        ->asarray()
        ->all();
        foreach($project as $key=>$val){
            $projectidarr[] = $val['u_projectid'];
        }
        //项目id
        if(empty($projectidarr)){
            $result = UPatientbase::find()
            ->select('u_patientbase.*,u_projectdata.u_projectName,m_userinfo.s_username,m_userinfo.s_formalname')
            ->joinWith('uProjectdata')
            ->joinWith('mUserinfo')
            ->where(['u_patientbase.u_status'=>0,'u_patientbase.u_createuserid'=>$userid])
            ->offset($start)
            ->limit(30)
            ->orderby(['u_patientbase.u_updatetime'=>SORT_DESC])
            ->asarray()
            ->all();
        }else{
            //项目下的患者
            $result = UPatientbase::find()
            ->select('u_patientbase.*,u_projectdata.u_projectName,m_userinfo.s_username,m_userinfo.s_formalname')
            ->joinWith('uProjectdata')
            ->joinWith('mUserinfo')
            ->where(['u_patientbase.u_projectid'=>$projectidarr,'u_patientbase.u_status'=>0])
            ->orwhere(['u_patientbase.u_status'=>0,'u_patientbase.u_createuserid'=>$userid])
            ->offset($start)
            ->limit(30)
            ->orderby(['u_patientbase.u_updatetime'=>SORT_DESC])
            ->asarray()
            ->all();
        }
        foreach($result as $k=>$v){
            $result[$k]['address']     = UPatientdata::lastAddress($v['u_MDID']);
            unset($result[$k]['uProjectdata']);
            unset($result[$k]['mUserinfo']);
        }
        if(empty($projectidarr)){
            $count = UPatientbase::find()
            ->select('u_patientbase.*,u_projectdata.u_projectName')
            ->joinWith('uProjectdata')
            ->where(['u_patientbase.u_status'=>0,'u_patientbase.u_createuserid'=>$userid])
            ->count();
        }else{
            $count = UPatientbase::find()
            ->select('u_patientbase.*,u_projectdata.u_projectName')
            ->joinWith('uProjectdata')
            ->where(['u_patientbase.u_projectid'=>$projectidarr,'u_patientbase.u_status'=>0])
            ->orwhere(['u_patientbase.u_status'=>0,'u_patientbase.u_createuserid'=>$userid])
            ->count();
        }
        $newresult['allnum']      = $count;
        $newresult['patientlist'] = $result;
        return $newresult;
    }
    /**
     * @todo 姓名搜索患者
     * @param name      患者姓名
     * @param projectid 项目id
     * @return array
     */
    public static function searchNamelist($name,$projectid,$userid){
        $results     = UPatientbase::find()
        ->select('u_patientbase.*,r_newpatientdata.status')
        ->joinWith('rNewpatientdata')
        ->where(['like','u_patientbase.u_patientname',$name])
        ->andwhere(['u_patientbase.u_projectid'=>$projectid,'u_patientbase.u_status'=>0,'r_newpatientdata.status'=>[2,3,4]])
        ->orderby(['u_patientbase.u_updatetime'=>SORT_DESC])
        ->asarray()
        ->all();
        foreach ($results as $k=>$v){
            $results[$k]['group']       = UPatientgroup::patientGroup($userid,$v['u_MDID']);
            //应随访次数
            $follownum                  = UFollowPatient::follownum($userid, $projectid);
            //最新内容
            $lastinfo                   = UFollowPatient::getFollowStatus($projectid,$v['u_MDID'],$userid,$follownum,$v['u_jointime']);
            //随访状态
            $results[$k]['follow']      = $lastinfo['status'];
            $results[$k]['s_username']  = $lastinfo['username'];
            $results[$k]['status']      = $lastinfo['check'];
            $results[$k]['center']      = $lastinfo['center'];
            unset($results[$k]['rNewpatientdata']);
        }
        return $results;
    }
    /**
     * @todo 入组时间搜搜患者
     */
    public static function searchJoinlist($startjoin,$endjoin,$projectid,$userid){
        $results     = UPatientbase::find()
        ->joinWith('rNewpatientdata')
        ->select('u_patientbase.*,r_newpatientdata.status')
        ->where(['between','u_patientbase.u_jointime',$startjoin,$endjoin])
        ->andwhere(['u_patientbase.u_projectid'=>$projectid,'u_patientbase.u_status'=>0,'r_newpatientdata.status'=>[2,3,4]])
        ->orderby(['u_patientbase.u_updatetime'=>SORT_DESC])
        ->asarray()
        ->all();
        foreach ($results as $k=>$v){
            $results[$k]['group']       = UPatientgroup::patientGroup($userid,$v['u_MDID']);
            //应随访次数
            $follownum                  = UFollowPatient::follownum($userid, $projectid);
            //最新内容
            $lastinfo                   = UFollowPatient::getFollowStatus($projectid,$v['u_MDID'],$userid,$follownum,$v['u_jointime']);
            //随访状态
            $results[$k]['follow']      = $lastinfo['status'];
            $results[$k]['s_username']  = $lastinfo['username'];
            $results[$k]['status']      = $lastinfo['check'];
            $results[$k]['center']      = $lastinfo['center'];
            unset($results[$k]['rNewpatientdata']);
        }
        return $results;
    }
    /**
     * @todo 添加患者的人  具有管理患者权限的人
     * @param mdid
     */
    public static function userPermission($userid,$mdid){
        //创建患者的人
        $useridarr   = UPatientbase::find()
        ->select('u_createuserid,u_projectid')
        ->where(['u_MDID'=>$mdid])
        ->asarray()
        ->all();
        $user[]      = $useridarr[0]['u_createuserid'];
        //项目管理员们
        $projectuser = UUserProject::find()
        ->select('u_userID')
        ->where(['u_permission'=>1,'u_projectID'=>$useridarr[0]['u_projectid']])
        ->asarray()
        ->all();
        foreach($projectuser as $k=>$v){
            $user[] = $v['u_userID'];
        }
        if(in_array($userid,$user)){
            //有权限
            return true;
        }else{
            //没有权限
            return false;
        }
    }
    /**
     * @todo 删除患者
     * @param mdid
     */
    public static function deletePatient($userid,$mdid){
//         $model = UPatientbase::findOne(['u_MDID'=>$mdid]);
//         $model -> u_status = 1;
//         if($model->save()){
//             return true;
//         }else{
//             return false;
//         }
        $arr      = explode(',', $mdid);
        $datetime = date('Y-m-d H:i:s');
        foreach($arr as $k=>$v){
            if(UPatientbase::userPermission($userid,$v)){
                //分组中也去掉和此患者相关的内容
                UPatientgroup::deleteAll(['u_patientid'=>$v]);
                $result = UPatientbase::updateAll(['u_status'=>1,'u_updatetime'=>$datetime],['u_MDID'=>$v]);
            }
        }
        return true;
        
    }
    /**
     * @todo 更新患者基本信息中修改时间
     * @param mdid      患者id
     * @param datetime  更新时间
     */
    public static function uptime($mdid,$datetime){
        $result = UPatientbase::updateAll(['u_updatetime'=>$datetime],['u_MDID'=>$mdid]);
        if($result){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 更新患者基本信息
     * 
     */
    public static function updatePatientbase(){
        $model = UPatientbase::findOne(['u_MDID'=>Yii::$app->getRequest()->getBodyParam('mdid')]);
        $model -> u_updatetime   = date('Y-m-d H:i:s');
        $model -> u_updateuserid = Yii::$app->getRequest()->getBodyParam('userid');
        $model -> u_birthday     = empty(Yii::$app->getRequest()->getBodyParam('birthday'))? $model->u_birthday : Yii::$app->getRequest()->getBodyParam('birthday');
        $model -> u_gender       = Yii::$app->getRequest()->getBodyParam('gender');
        $model -> u_patientname  = Yii::$app->getRequest()->getBodyParam('name');
        $model -> u_projectid    = Yii::$app->getRequest()->getBodyParam('projectid');
        $model -> u_jointime     = empty(Yii::$app->getRequest()->getBodyParam('jointime'))?date('Y-m-d H:i:s'):Yii::$app->getRequest()->getBodyParam('jointime');
        $model -> u_phone        = Yii::$app->getRequest()->getBodyParam('phone');
        $model -> u_secondphone  = Yii::$app->getRequest()->getBodyParam('secondphone');
        $model -> u_address      = Yii::$app->getRequest()->getBodyParam('address');
        if($model -> save()){
            //日志内容
            $filename                  = date('Y-m-d').'-patientbase.log';
            $content['u_updateuserid'] = $model -> u_updateuserid;
            $content['u_MDID']         = $model -> u_MDID;
            $content['requesturl']     = 'patient/updatepatientbase';
            $content['u_birthday']     = $model -> u_birthday;
            $content['u_gender']       = $model -> u_gender;
            $content['u_patientname']  = $model -> u_patientname;
            $content['u_projectid']    = $model -> u_projectid;
            $content['u_jointime']     = $model -> u_jointime;
            $content['u_updatetime']   = $model -> u_updatetime;
            Commonfun::createLog($filename,$content);
            return $model->u_MDID;
        }else{
            return false;
        }
    }
    /**
     * @todo 基线统计
     * @param projectid
     * @param centerid
     */
    public static function baselineSta($projectid,$centerid){
        if($centerid !=''){
            #中心里的人
            $userarr = UCentertable::centerList($projectid);
            foreach ($userarr as $k=>$v){
                if($v['u_centerID'] == $centerid){
                    foreach($v['userarr'] as $key=>$val){
                        $useridarr[] = $val['u_userid'];
                    }
                }
            }
            $userid = implode(',', $useridarr);
            $centerwhere = "AND `u_createuserid` in (".$userid.")";
        }else{
            $centerwhere = '';
        }
        #累计录入基线总数
        $baselineall = UPatientbase::find()
                     ->joinWith('rNewpatientdata')
                     ->where(['u_projectid'=>$projectid,'u_status'=>0,'r_newpatientdata.status'=>[2,4]])
                     ->count();
        #这周每天录入数
        $date  = date("Y-m-d"); //当前日期
        //$date         = '2017-5-24'; //当前日期
        $week         = Commonfun::getWeekday($date);
        $sql          = "SELECT
	DATE_FORMAT(u_createtime, '%Y-%m-%d') as date,
	count(u_id) AS count
FROM
	`u_patientbase` INNER JOIN `r_newpatientdata` on u_MDID=mdid
WHERE
	(
		(`u_projectid` = ".$projectid.")
		AND (`u_status` = 0) AND (`status` in (2,4)) ".$centerwhere."
	)
AND (
	`u_createtime` BETWEEN '".$week['start']."'
	AND '".$week['end']." 23:59:59'
)
GROUP BY
	DATE_FORMAT(u_createtime, '%Y-%m-%d')";
        $connection   = Yii::$app->db;
        $command      = $connection->createCommand($sql);
        $weekarr      = $command->queryAll();
        $weekdate     = [];
        foreach($weekarr as $k=>$v){
            $weekdate[] = $v['date'];
        }
        foreach($week as $k=>$v){
            if(!in_array($v,$weekdate)){
                $weekarr[]['date']  = $v;
            }
        }
        unset($weekdate);
        ArrayHelper::multisort($weekarr,'date',SORT_ASC);
        foreach($weekarr as $k=>$v){
            if(!isset($v['count'])){
                $weekarr[$k]['count'] = 0;
            }
        }
        #当月每天
        $month        = Commonfun::getMonth($date);
        $monthsql     = "SELECT
	DATE_FORMAT(u_createtime, '%Y-%m-%d') as date,
	count(u_id) AS count
FROM
	`u_patientbase` INNER JOIN `r_newpatientdata` on u_MDID=mdid
WHERE
	(
		(`u_projectid` = ".$projectid.")
		AND (`u_status` = 0) AND (`status` in (2,4)) ".$centerwhere."
	)
AND (
	`u_createtime` BETWEEN '".$month['start']."'
	AND '".$month['end']." 23:59:59'
)
GROUP BY
	DATE_FORMAT(u_createtime, '%Y-%m-%d')";
        $connection   = Yii::$app->db;
        $command      = $connection->createCommand($monthsql);
        $montharr     = $command->queryAll();
        $monthdate    = [];
        foreach($montharr as $k=>$v){
            $monthdate[] = $v['date'];
        }
        for($i = $month['start'];$i<=$month['end'];$i++){
            if(!in_array($i,$monthdate)){
                $montharr[]['date']  = $i;
            }
        }
        unset($monthdate);
        ArrayHelper::multisort($montharr,'date',SORT_ASC);
        foreach($montharr as $k=>$v){
            if(!isset($v['count'])){
                $montharr[$k]['count'] = 0;
            }
        }
        #当年
        $year['start'] = date('Y',strtotime($date)).'-1';
        $year['end']   = date('Y',strtotime($date)).'-12';
        $yearsql     = "SELECT
	DATE_FORMAT(u_createtime, '%Y-%m') as date,
	count(u_id) AS count
FROM
	`u_patientbase` INNER JOIN `r_newpatientdata` on u_MDID=mdid
WHERE
	(
		(`u_projectid` = ".$projectid.")
		AND (`u_status` = 0) AND (`status` in (2,4)) ".$centerwhere."
	)
AND (
	`u_createtime` BETWEEN '".$year['start']."'
	AND '".$year['end']."-31 23:59:59'
)
GROUP BY
	DATE_FORMAT(u_createtime, '%Y-%m')";
        $connection   = Yii::$app->db;
        $command      = $connection->createCommand($yearsql);
        $yeararr      = $command->queryAll();
        $yeardate     = [];
        foreach($yeararr as $k=>$v){
            $yeardate[] = $v['date'];
        }
        for($i = 1;$i<=12;$i++){
            if($i<10){
                $i = '0'.$i;
            }
            if(!in_array(date('Y',strtotime($date)).'-'.$i,$yeardate)){
                $yeararr[]['date']  = date('Y',strtotime($date)).'-'.$i;
            }
        }
        unset($yeardate);
        ArrayHelper::multisort($yeararr,'date',SORT_ASC);
        foreach($yeararr as $k=>$v){
            if(!isset($v['count'])){
                $yeararr[$k]['count'] = 0;
            }
        }
        $yesterday = date('Y-m-d',strtotime($date. '-1 days'));
        #昨日新增
        $yesterdaynum = 0;
        foreach($weekarr as $k=>$v){
            if($v['date'] == $yesterday){
                $yesterdaynum = $v['count'];
            }
        }
        $result['baselineall'] = $baselineall;
        $result['yesterday']   = $yesterdaynum;
        $result['week']        = $weekarr;
        $result['month']       = $montharr;
        $result['year']        = $yeararr;
        unset ($weekarr);
        unset ($montharr);
        unset ($yeararr);
        return $result;
    }
    /**
     * @todo 到了随访日期的患者
     * @param projectid
     * @param taskmonth
     */
    public static function patientNum($projectid,$taskmonth,$patientid,$search=''){
        $followdate = date('Y-m-d',time()-86400*$taskmonth);
        $where      = ['and',['u_projectid'=>$projectid,'u_status'=>0,'r_newpatientdata.status'=>4],["<=",'u_jointime', $followdate],['not in','u_MDID',$patientid]];
        $post       = UPatientbase::find()
                    ->joinWith('rNewpatientdata')
                    ->select('u_id,u_MDID,u_projectid,u_patientname,u_gender,u_birthday,u_phone,u_secondphone,u_status,u_jointime');
        if($search != ''){
            $condition1 = ['and',$where,['like','u_MDID',$search]];
            $condition2 = ['and',$where,['like','u_patientname',$search]];
            $condition3 = ['and',$where,['like','u_phone',$search]];
            $condition4 = ['and',$where,['like','u_secondphone',$search]];
            $post       ->where($condition1)
                        ->orwhere($condition2)
                        ->orwhere($condition3)
                        ->orwhere($condition4);
        }else{
            $post   ->where($where);
        }
        $post       ->asArray();
        $patientarr = $post->all();
        return $patientarr;
    }
    /**
     * @todo 患者信息
     */
    public static function patientInfo($mdidarr){
        $result = UPatientbase::find()
                ->joinWith('uPatientdata')
                ->select('u_patientdata.*,u_patientbase.u_MDID')
                ->where(['in','u_patientbase.u_MDID',$mdidarr])
                ->orderby(['u_patientdata.u_updatetime'=>SORT_ASC])
                ->asarray()
                ->all();
        return $result;
    }
    /**
     * @todo 患者信息心
     */
    public static function rpatientInfo($mdidarr){
        $result = UPatientbase::find()
        ->joinWith('rNewpatientdata')
        ->select('u_MDID,u_patientbase.u_patientname,u_gender,u_birthday,u_jointime,u_address,u_phone,u_secondphone,r_newpatientdata.*')
        ->where(['in','u_patientbase.u_MDID',$mdidarr])
        ->orderby(['r_newpatientdata.createtime'=>SORT_DESC])
        ->asarray()
        ->all();
        return $result;
    }
    /**
     * @todo 患者历史记录
     */
    public static function historyList($patientid){
        $patientbase = '';
    }

    /**
     * @todo 应随访列表
     * @param userid     用户id
     * @param projectid  项目id
     * @param pagenum    当前页
     * @param type       排序内容(name,gender,age,overtime,lasttime)
     * @param sort       顺序(asc desc)
     * @param search     搜索内容
     */
    public static function shouldFollow($userid,$projectid,$pagenum,$type,$sort,$search){
        //项目的随访
        $projectfollow = UProjectTask::showFollow($userid, $projectid);
        $shouldinfo    = [];
        foreach($projectfollow as $key=>$val){
            //本次随访过的患者
            $patientid                    = UFollowPatient::getPatientid($val['taskid']);
            //本次取消的患者
            $returnpatientid              = UFollowPatient::returnFollow($val['taskid']);
            //应随访患者
            $shouldinfo[$val['taskid']]   = UPatientbase::patientNum($projectid,$val['taskmonth'],$patientid,$search);
            //补数据 （进一步处理 or 新随访）
            foreach($shouldinfo[$val['taskid']] as $k=>$v){
                unset($shouldinfo[$val['taskid']][$k]['rNewpatientdata']);
                if(in_array($v['u_MDID'], $returnpatientid)){
                    //患者此次随访信息
                    $patientfollowinfo = UFollowPatient::followPatient($v['u_MDID'], $val['taskid']);
                    //等待下一步处理
                    $shouldinfo[$val['taskid']][$k]['type']          = 'wait';
                    //提示说明
                    $shouldinfo[$val['taskid']][$k]['follow_status'] = $patientfollowinfo['u_follow_status'];
                    $shouldinfo[$val['taskid']][$k]['other_reason']  = $patientfollowinfo['other_reason'];
                    //记录id
                    $shouldinfo[$val['taskid']][$k]['recordid']      = $patientfollowinfo['recordid'];
                }else{
                    //新随访
                    $shouldinfo[$val['taskid']][$k]['type']          = 'new';
                }
                //随访id
                $shouldinfo[$val['taskid']][$k]['taskid']            = $val['taskid'];
                //随访时间
                $shouldinfo[$val['taskid']][$k]['taskmonth']         = $val['taskmonth'];
            }
        }
        //三维数组重组二维数组
        $arr = [];
        foreach($shouldinfo as $value){
            foreach($value as $v){
                $arr[] = $v;
            }
        }
        foreach($arr as $k=>$v){
            //逾期
            $arr[$k]['overday']           = UPatientbase::overFollowDay($v['taskmonth'],$v['u_MDID']);
            //前一次随访
            $arr[$k]['lastfollow']        = UFollowPatient::lastFollow($v['u_projectid'],$v['u_MDID'],$v['taskid']);
        }
        $result  = array_values($arr);
        if($sort == '' || $sort == 'desc'){
            $order = SORT_DESC;
        }
        if($sort == 'asc'){
            $order = SORT_ASC;
        }
        if($type == '' || $type == 'overtime'){
            $type = 'overday';
        }
        if($type == 'name'){
            $type = 'u_patientname';
        }
        if($type == 'gender'){
            $type = 'u_gender';
        }
        if($type == 'age'){
            $type = 'u_birthday';
        }
        if($type == 'lasttime'){
            $type = 'lastfollow';
        }
        ArrayHelper::multisort($result,$type,$order);
        //数据总数
        $count             = count($result);
        //每页显示数量
        $pagesize          = 30;
        //总页数
        $pagetotal         = ceil($count/$pagesize);
        $newarr            = array_slice($result, ($pagenum-1)*$pagesize, $pagesize);
        $newresult['total']   = $count;
        $newresult['data']    = $newarr;
        return $newresult;
    }
    /**
     * @todo 超过随访日期天数
     * @param patientid
     * @param taskmonth
     */
    public static function overFollowDay($taskmonth,$patientid){
        //患者基本信息
        $patientinfo = UPatientbase::getPatientInfo($patientid);
        //应随访日期(加入项目的时间+随访月)
        $shouldtime  = strtotime($patientinfo['u_jointime'])+$taskmonth*86400;
        //逾期
        $overday     = intval((time()-$shouldtime)/86400);
        return $overday;
    }
    /**
     * @todo 应随访数
     */
    public static function shouldNum($userid,$projectid){
        //项目的随访
        $projectfollow = UProjectTask::showFollow($userid, $projectid);
        $shouldinfo    = [];
        foreach($projectfollow as $key=>$val){
            //本次随访过的患者
            $patientid                    = UFollowPatient::getPatientid($val['taskid']);
            //应随访患者
            $shouldinfo[$val['taskid']]   = UPatientbase::patientNum($projectid,$val['taskmonth'],$patientid);
        }
        //三维数组重组二维数组
        $result = [];
        foreach($shouldinfo as $value){
            foreach($value as $v){
                $result[] = $v;
            }
        }
        //数据总数
        $count            = count($result);
        return $count;
    }
    /**
     * @todo 分组患者
     * 
     */
    public static function getgroupdata($mdids,$projectid,$start){
        $result['data']     = UPatientbase::find()
        ->joinWith('rNewpatientdata')
        ->select('u_patientbase.*,r_newpatientdata.status')
        ->where(['not in','u_patientbase.u_MDID',$mdids])
        ->andwhere(['u_patientbase.u_status'=>0,'u_patientbase.u_projectid'=>$projectid,'r_newpatientdata.status'=>[2,3,4]])
        ->offset($start)
        ->limit(30)
        ->orderBy(['u_patientbase.u_updatetime'=>SORT_DESC])
        ->asarray()
        ->all();

        $result['count']    = UPatientbase::find()
        ->joinWith('rNewpatientdata')
        ->select('u_MDID,r_newpatientdata.status')
        ->where(['not in','u_patientbase.u_MDID',$mdids])
        ->andwhere(['u_patientbase.u_status'=>0,'u_patientbase.u_projectid'=>$projectid,'r_newpatientdata.status'=>[2,3,4]])
        ->count();
        return $result;
    }
    /**
     * @todo 录入端我的工作日志
     */
    public static function rWorkData($projectid,$userid,$start,$order,$search){
        $post   = UPatientbase::find()
        ->joinWith('rNewpatientdata')
        ->select('u_patientbase.u_patientname,u_patientbase.u_MDID,u_patientbase.u_gender,u_patientbase.u_birthday,u_patientbase.u_phone,u_patientbase.u_secondphone,u_patientbase.u_jointime,r_newpatientdata.mdid,r_newpatientdata.status,r_newpatientdata.updatetime')
        ->where(['r_newpatientdata.projectid'=>$projectid,'r_newpatientdata.updateuserid'=>$userid,'u_patientbase.u_status'=>0])
        ->offset($start)
        ->limit(30)
        ->orderby($order)
        ->asarray();
        if($search != ''){
            $post   -> andFilterWhere(['like','r_newpatientdata.mdid',$search])
            -> orFilterWhere(['like','u_patientbase.u_patientname',$search])
            -> orFilterWhere(['like','u_patientbase.u_phone',$search])
            -> orFilterWhere(['like','u_patientbase.u_secondphone',$search]);
        }
        $result  = $post->all();
        $num    = UPatientbase::find()
        ->joinWith('rNewpatientdata')
        ->select('u_patientbase.u_patientname,u_patientbase.u_MDID,u_patientbase.u_gender,u_patientbase.u_birthday,u_patientbase.u_phone,u_patientbase.u_secondphone,r_newpatientdata.mdid,r_newpatientdata.status,r_newpatientdata.updatetime')
        ->where(['r_newpatientdata.projectid'=>$projectid,'r_newpatientdata.updateuserid'=>$userid,'u_patientbase.u_status'=>0])
        ->asarray();
        if($search != ''){
            $num    -> andFilterWhere(['like','r_newpatientdata.mdid',$search])
            -> orFilterWhere(['like','u_patientbase.u_patientname',$search])
            -> orFilterWhere(['like','u_patientbase.u_phone',$search])
            -> orFilterWhere(['like','u_patientbase.u_secondphone',$search]);
        }
        $count  = $num->count();
        $newdata['result'] = $result;
        $newdata['count']  = $count;
        return $newdata;
    }
    /**
     * @todo 患者历史记录
     */
    public static function patientRecord___old($mdid){
        //登记记录
        $base = UPatientbase::find()
              ->joinWith('rNewpatientdata')
              ->select('r_newpatientdata.checktime,r_newpatientdata.updateuserid,u_patientbase.u_MDID')
              ->where(['u_patientbase.u_MDID'=>$mdid,'r_newpatientdata.status'=>4])
              ->asarray()
              ->one();
        if(!empty($base)){
            foreach($base as $k=>$v){
                unset($base['rNewpatientdata']);
                //最后修改人
                $base['username'] = MUserinfo::userInfo($base['updateuserid'])[0]['s_username'];
                //记录类型
                $base['typename'] = '入组登记';
                $base['type']     = 'input';
            }
        }
        //随访记录
        $follow    = UFollowPatient::followPatientList($mdid);
        $follow[]  = $base;
        if(empty($follow[0])){
            return $follow;
        }
        ArrayHelper::multisort($follow,'checktime',SORT_DESC);
        return $follow;
    }
    /**
     * @todo 患者历史记录
     */
    public static function patientRecord($mdid){
        //登记记录
        $base = UPatientbase::find()
        ->joinWith('rNewpatientdata')
        ->select('r_newpatientdata.checktime,r_newpatientdata.updateuserid,r_newpatientdata.updatetime,u_patientbase.u_MDID,u_patientbase.u_projectid,u_patientbase.u_jointime')
        ->where(['u_patientbase.u_MDID'=>$mdid,'r_newpatientdata.status'=>[2,3,4]])
        ->asarray()
        ->one();
        if(!empty($base)){
            foreach($base as $k=>$v){
                //最后修改人
                $base['username'] = MUserinfo::userInfo($base['updateuserid'])[0]['s_username'];
                //记录类型
                $base['typename'] = '入组登记';
                $base['type']     = 'input';
                //状态   1已完成 ；2未完成；11.未开始
                if($base['rNewpatientdata'][0]['status'] == 4){
                    $base['status']   = 1;
                }else{
                    $base['status']   = 2;
                }
            }
            unset($base['rNewpatientdata']);
        }
        $projectid = $base['u_projectid'];
        //患者所在项目的所有随访
        $projectfollow = UProjectTask::showFollow('',$projectid);
        //随访记录
        $follow    = UFollowPatient::allFollowPatientList($mdid);
        //处理过的随访（不管完没完成）
        $dofollow  = [];
        foreach($follow as $key=>$val){
            $dofollow[] = $val['u_follow_id'];
        }
        //所有随访有没有不在已经随访 里的（不在的就是还没开始随访的）
        foreach($projectfollow as $k=>$v){
            if(!in_array($v['taskid'],$dofollow)){
                $follow[$v['taskid']]['u_follow_id'] = $v['taskid'];
                $follow[$v['taskid']]['month']       = $v['taskmonth'];
                $follow[$v['taskid']]['typename']    = $v['taskname'];
                $follow[$v['taskid']]['type']        = 'follow';
                $follow[$v['taskid']]['status']      = 11;
            }
        }
        $follow    = array_values($follow);
        $follow[]  = $base;
        if(empty($follow[0])){
            return $follow;
        }
        ArrayHelper::multisort($follow,'month',SORT_ASC);
        return $follow;
    }
    /**
     * @todo删除保存的患者
     * 
     */
    public static function rDeletePatient($userid,$mdid){
        //删除基本信息
        UPatientbase::deleteAll(['u_MDID'=>$mdid]);
        RNewpatientdata::deleteAll(['mdid'=>$mdid]);
        return true;
    }
    /**
     * @todo 
     */
    public static function patientids($projectid){
        $results    = UPatientbase::find()
                    ->select('u_patientbase.u_MDID')
                    ->joinWith('rNewpatientdata')
                    ->where(['u_patientbase.u_projectid'=>$projectid,'u_patientbase.u_status'=>0,'r_newpatientdata.status'=>[2,3,4]])
                    ->asarray()
                    ->all();
        $mdid = [];
        foreach($results as $k=>$v){
            $mdid[] = $v['u_MDID'];
        }
        return $mdid;
    }
}
