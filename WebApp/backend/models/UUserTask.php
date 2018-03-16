<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\MUserinfo;
use app\models\ROpentimeProject;
use app\models\RNewpatientdata;
use app\models\FOpentimeProject;
/**
 * This is the model class for table "u_user_task".
 *
 * @property integer $id
 * @property string $userid
 * @property string $taskid
 * @property integer $projectid
 * @property string $admin
 * @property string $createtime
 */
class UUserTask extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_user_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['projectid','type','status'], 'integer'],
            [['createtime','updatetime'], 'safe'],
            [['userid', 'taskid', 'admin'], 'string', 'max' => 32],
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
            'taskid' => 'Taskid',
            'projectid' => 'Projectid',
            'admin' => 'Admin',
            'createtime' => 'Createtime',
        ];
    }
    /**
     * @todo 关联任务表
     */
    public function getUProjectTask(){
        return $this->hasMany(UProjectTask::className(), ['taskid'=>'taskid']);
    }
    /**
     * @todo 关联项目表
     */
    public function getUProjectdata(){
        return $this->hasMany(UProjectdata::className(), ['u_projectID'=>'projectid']);
    }
    /**
     * @todo 成员是否已经有此任务
     * @param userid
     * @param taskid
     */
    public static function isTask($userid,$taskid){
        $result = UUserTask::find()
                ->where(['userid'=>$userid,'taskid'=>$taskid])
                ->asarray()
                ->all();
        if(empty($result)){
            return true;
        }
        return false;
    }
    /**
     * @todo 创建成员任务
     * @param userid
     * @param taskid
     * @param admin
     * @param projectid
     */
    public static function createUserTask($userid,$taskid,$admin,$projectid){
        if(UUserTask::isTask($userid, $taskid)){
            $model = new UUserTask();
            $model -> userid     = $userid;
            $model -> taskid     = $taskid;
            $model -> admin      = $admin;
            $model -> projectid  = $projectid;
            $model -> createtime = date('Y-m-d H:i:s');
            $model -> updatetime = date('Y-m-d H:i:s');
            $model -> type       = explode('_',$taskid)[1];
            $model ->save();
        }else{
            UUserTask::updateAll(['status'=>1,'updatetime'=>date('Y-m-d H:i:s'),'admin'=>$admin],['userid'=>$userid,'taskid'=>$taskid]);
        }
        return true;
    }
    /**
     * @TODO 用户在项目中的任务
     * @param projectid
     * @param userid
     */
    public static function usertask($projectid,$userid){
        $result = UUserTask::find()
                ->joinWith('uProjectTask')
                ->where(['u_user_task.projectid'=>$projectid,'u_user_task.userid'=>$userid,'u_user_task.status'=>1,'u_project_task.status'=>1])
                ->asarray()
                ->all();
        if(empty($result)){
            $task['note'] = '';
                $task['id']   = [];
        }else{
            foreach ($result as $k=>$v){
                $taskarr[] = $v['uProjectTask'][0]['taskname'];
                $taskid[]  = $v['uProjectTask'][0]['taskid'];
            }
            $task['note'] = implode('、', $taskarr);
            $task['id']   = $taskid;
        }
        return $task;
    }
    /**
     * @todo 删除用户在项目中的任务
     * @param userid
     * @param projectid
     */
    public static function deleteTask($userid,$projectid){
        //用户是否在此项目中有任务
        if(UUserTask::usertask($projectid, $userid) != ''){
            //删除
            UUserTask::updateAll(['status'=>2,'updatetime'=>date('Y-m-d H:i:s')],['projectid' => $projectid, 'userid' => $userid]);
        }
        return true;
    }
    /**
     * @todo 删除有某个任务的所有项
     */
    public static function deleteAllTask($taskid){
        UUserTask::updateAll(['status'=>2,'updatetime'=>date('Y-m-d H:i:s')],['taskid' => $taskid]);
        return true;
    } 
    /**
     * @todo 患者登记-有基线任务的项目
     * @param userid
     */
    public static function inputProjectList($userid){
        //有基线任务的项目
        $projectidarr = UUserTask::find()
                      ->select('u_user_task.projectid,u_user_task.type,u_user_task.status,u_projectdata.u_projectName,u_projectdata.endtime,u_projectdata.u_createuserid')
                      ->joinWith('uProjectdata')
                      ->where(['u_user_task.userid'=>$userid,'u_user_task.type'=>1])
                      ->asarray()
                      ->all();
        if(empty($projectidarr)){
            $projectidarr   = [];
        }else{
            foreach($projectidarr as $k=>$v){
                unset($projectidarr[$k]['uProjectdata']);
                //没有被移除并且项目没过期
                if($v['status'] == 1 && time() < strtotime($v['endtime'])){
                    $projectidarr[$k]['type']  = 1;    #进行中的项目
                }else{
                    $projectidarr[$k]['type']  = 2;    #已结束项目
                }
                //管理员名字
                $projectidarr[$k]['adminname'] = MUserinfo::userInfo($v['u_createuserid'])[0]['s_username'];
                //用户上次打开项目时间
                $projectidarr[$k]['opentime']  = ROpentimeProject::openTime($userid, $v['projectid']);
                //已提交病例数
                $projectidarr[$k]['commitnum'] = RNewpatientdata::commitNum($userid, $v['projectid']);
                //已保存病例数（所在分中心）
                $projectidarr[$k]['savenum']   = RNewpatientdata::saveNum($userid,$v['projectid']);
                unset($projectidarr[$k]['status']);
                unset($projectidarr[$k]['endtime']);
                unset($projectidarr[$k]['u_createuserid']);
            }
        }
        ArrayHelper::multisort($projectidarr,'opentime',SORT_DESC);
        return $projectidarr;
    }
    /**
     * @todo 随访的已参加项目列表
     * @param userid
     */
    public static function followProjectList($userid){
        //有随访任务的项目
        $projectidarr = UUserTask::find()
                      ->select('u_user_task.projectid,u_user_task.type,u_user_task.status,u_projectdata.u_projectName,u_projectdata.endtime,u_projectdata.u_createuserid')
                      ->joinWith('uProjectdata')
                      ->where(['u_user_task.userid'=>$userid,'u_user_task.type'=>2])
                      ->asarray()
                      ->all();
        if(empty($projectidarr)){
            $result   = [];
        }else{
            foreach($projectidarr as $k=>$v){
                unset($projectidarr[$k]['uProjectdata']);
                //没有被移除并且项目没过期
                if($v['status'] == 1 && time() < strtotime($v['endtime'])){
                    $result[$v['projectid']]['type']  = 1;    #进行中的项目
                }else{
                    $result[$v['projectid']]['type']  = 2;    #已结束项目
                }
                //项目名称
                $result[$v['projectid']]['projectname'] = $v['u_projectName'];
                //项目id
                $result[$v['projectid']]['projectid']   = $v['projectid'];
                //管理员名字
                //$result[$v['projectid']]['adminname']   = MUserinfo::userInfo($v['u_createuserid'])[0]['s_username'];
                //用户上次打开项目时间
                $result[$v['projectid']]['opentime']    = FOpentimeProject::openTime($userid, $v['projectid']);
                //应随访数
                $result[$v['projectid']]['shouldnum']   = UPatientbase::shouldNum($userid, $v['projectid']);
                //已提交随访病例数
                $result[$v['projectid']]['commitnum']   = UFollowPatient::followStatusNum($v['projectid'],'commit');
                //已保存随访病例数（
                $result[$v['projectid']]['savenum']     = UFollowPatient::followStatusNum($v['projectid'],'save');
                
            }
        }
        $arr = array_values($result);
        ArrayHelper::multisort($arr,'opentime',SORT_DESC);
        return $arr;
    }
    /**
     * @todo 用户参加的项目
     */
    public static function userProjects($userid){
        //有随访任务的项目
        $projectidarr = UUserTask::find()
        ->select('u_user_task.projectid,u_user_task.type,u_user_task.status,u_projectdata.u_projectName,u_projectdata.endtime,u_projectdata.u_createuserid')
        ->joinWith('uProjectdata')
        ->where(['u_user_task.userid'=>$userid])
        ->asarray()
        ->all();
        $projectid = [];
        if(empty($projectidarr)){
            $result   = [];
        }else{
            foreach($projectidarr as $k=>$v){
                $projectid[] = $v['projectid'];
                unset($projectidarr[$k]['uProjectdata']);
                //没有被移除并且项目没过期
                if($v['status'] == 1 && time() < strtotime($v['endtime'])){
                    $result[$v['projectid']]['type']  = 1;    #进行中的项目
                }else{
                    $result[$v['projectid']]['type']  = 2;    #已结束项目
                }
                //项目名称
                $result[$v['projectid']]['projectname'] = $v['u_projectName'];
                //项目id
                $result[$v['projectid']]['projectid']   = $v['projectid'];
                //管理员名字
                $result[$v['projectid']]['adminname']   = MUserinfo::userInfo($v['u_createuserid'])[0]['s_username'];
            }
        }
        $data['result']     = $result;
        $data['projectid']  = $projectid;
        return $data;
    }
}
