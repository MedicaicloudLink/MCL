<?php

namespace app\models;

use Yii;
use app\models\UUserProject;
use app\models\MUserinfo;
use app\models\UProjectTask;
use app\models\UUserTask;
use app\models\UForm;

/**
 * This is the model class for table "u_projectdata".
 *
 * @property string $u_projectID
 * @property string $u_projectName
 * @property string $u_templeateID
 * @property string $u_projectMem
 * @property string $u_projecttime
 * @property string $u_projectstate
 */
class UProjectdata extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_projectdata';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_projectID'], 'integer'],
            [['u_projecttime','u_createuserid','u_projectstate'], 'safe'],
            [['u_projectName', 'u_templeateID', 'u_projectMem', 'u_createuserid','formname','formdata'], 'string'],
        ];
    }

    /**
     * @todo 关联用户表
     */
    public function getMUserinfo(){
        return $this->hasMany(MUserinfo::className(), ['s_userid' => 'u_createuserid']);
    }
    /**
     * @todo 创建项目
     * @param userid
     * @param name
     * @param templeateid
     * @param projectmem
     * @return projectid|bool
     */
    public static function createProject($model){
        if($model->save()){
            // 创建用户项目关联
            $userproject = new UUserProject();
            $userproject -> u_projectID  = $model -> u_projectID;
            $userproject -> u_userID     = $model -> u_createuserid;
            $userproject -> u_permission = 1;
            $userproject -> u_createtime = date('Y-m-d H:i:s');
            if($userproject -> save()){
                //创建项目基线任务
                $taskid = UProjectTask::createProjectTask(1, $model -> u_projectID, $model -> u_createuserid);
                //创建成员任务
                UUserTask::createUserTask($model -> u_createuserid, $taskid, $model -> u_createuserid, $model -> u_projectID);
                return $model -> u_projectID;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * @todo 所有进行中项目
     */
    public static function AllProjects(){
        $projects = UProjectdata::find()
            ->where(["u_projectstate" => 1])
            ->asArray()
            ->all();

        return $projects;
    }

    /**
     * @todo 查询项目详细信息
     * @param $projectId 项目ID
     */
    public static function getProjectDetailByProjectId($projectId){
        $project = UProjectdata::find()
            ->select("u_projectdata.*, s_username")
            ->joinWith("mUserinfo")
            ->where(["u_projectID" => $projectId])
            ->asArray()
            ->all();

        foreach($project as $k=>$v){
            unset($project[$k]['mUserinfo']);
            unset($project[$k]['u_createuserid']); // 返回结果移除创建者ID
        }

        return $project;
    }

    /**
     * @todo 终止项目
     * @param $projectId 项目ID
     */
    public static function endProject($projectId){
        $project = UProjectdata::findOne(["u_projectID" => $projectId]);
        if (!empty($project)){
            $project -> u_projectstate = 0;
            if ($project->save()){
                return true;
            }
        }

        return false;
    }
    /**
     * @todo 删除项目
     * @param userid 
     * @param projectid
     */
    public static function deleteProject($userid,$projectid){
        //写入日志 项目详情 谁删的
        $project                = UProjectdata::getProjectDetailByProjectId($projectid);
        if(empty($project)){
            return false;
        }
        $content['projectdata'] = $project[0];
        $content['userid']      = $userid;
        $content['deletetime']  = date('Y-m-d H:i:s');
        $filename               = date('Y-m-d').'-deleteProject.log';
        Commonfun::createLog($filename, $content);
        //删除项目
        if(UProjectdata::deleteAll(['u_projectID'=>$projectid])){
            //项目中成员
            UUserProject::deleteAll(['u_projectID'=>$projectid]);
            //成员任务
            UUserTask::deleteAll(['projectid'=>$projectid]);
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo pc端项目列表
     * @param userid
     */
    public static function pcProjectList($userid){
        //管理员 可以删除
        //是此用户管理的项目
        $projectids = UUserProject::find()
                    ->where(['u_userID'=>$userid,'u_permission'=>1])
                    ->asarray()
                    ->all();
        if(empty($projectids)){
            $projectarr = [];
        }
        foreach ($projectids as $k=>$v){
            $projectarr[] = $v['u_projectID'];
        }
        //公开项目 时间顺序 我的项目
        $result = UProjectdata::find()
                ->where(['in','u_projectID',$projectarr])
                ->orderby(['u_projectstate'=>SORT_ASC,'u_projecttime'=>SORT_DESC])
                ->asarray()
                ->all();
        foreach ($result as $key=>$val){
            if(in_array($val['u_projectID'],$projectarr)){
                $result[$key]['permit'] = 1;  //展示删除
            }else{
                $result[$key]['permit'] = '';    //不展示删除
            }
        }
        return $result;
        
    }
    /**
     * @todo 编辑项目
     * @param projectid  项目id
     * @param name       项目名称
     * @param templateid 模板id
     * @param projectmem 项目介绍
     * @param starttime  开始时间
     * @param endtime    结束时间
     * @param status     0.不公开  1.公开
     * @param formname     表单名称
     * @param formdata     表单数据
     */
    public static function editProject($projectid){
        $model = UProjectdata::findOne(['u_projectID'=>$projectid]);
        $model -> u_projectName   = Yii::$app->getRequest()->getBodyParam('name');
        $model -> u_templeateID   = Yii::$app->getRequest()->getBodyParam('templateid');
        $model -> u_projectMem    = Yii::$app->getRequest()->getBodyParam('projectmem');
        $model -> starttime       = Yii::$app->getRequest()->getBodyParam('starttime');
        $model -> endtime         = Yii::$app->getRequest()->getBodyParam('endtime');
        $model -> u_projectstate  = Yii::$app->getRequest()->getBodyParam('status');
        $model -> u_projecttime   = date('Y-m-d');
        //$forminfo                 = UForm::formInfo(Yii::$app->getRequest()->getBodyParam('templateid'));
        $model -> formname        = Yii::$app->getRequest()->getBodyParam('formname');
        $model -> formdata        = Yii::$app->getRequest()->getBodyParam('formdata');
        if($model->save()){
            return true;
        }
        return false;
    }

}
