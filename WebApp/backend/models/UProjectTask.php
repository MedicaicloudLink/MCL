<?php

namespace app\models;

use Yii;
use app\models\Commonfun;
use app\models\UUserTask;
use app\models\UTelemplate;
use app\models\UForm;
/**
 * This is the model class for table "u_project_task".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $projectid
 * @property string $taskid
 * @property string $admin
 * @property string $createtime
 * @property string $taskname
 * @property string $taskformid
 * @property integer $taskmonth
 * @property string $taskcontent
 */
class UProjectTask extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_project_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'projectid', 'taskmonth','status'], 'integer'],
            [['createtime','updatetime'], 'safe'],
            [['taskid', 'admin', 'taskname', 'taskformid','formname','formdata'], 'string'],
            [['taskcontent'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'projectid' => 'Projectid',
            'taskid' => 'Taskid',
            'admin' => 'Admin',
            'createtime' => 'Createtime',
            'taskname' => 'Taskname',
            'taskformid' => 'Taskformid',
            'taskmonth' => 'Taskmonth',
            'taskcontent' => 'Taskcontent',
        ];
    }
    /**
     * @todo 创建项目任务
     * @param type 1.基线  2.随访
     * @param projectid 
     * @param admin
     */
    public static function createProjectTask($type,$projectid,$admin,$follow=''){
        $model = new UProjectTask();
        $model -> type         = $type;
        $model -> projectid    = $projectid;
        $model -> admin        = $admin;
        $model -> createtime   = date('Y-m-d H:i:s');
        $model -> updatetime   = date('Y-m-d H:i:s');
        $model -> taskid       = Commonfun::randpw().'_'.$type;
        if($type == 1){
            $model -> taskname = '患者登记';
            $model -> save();
            return $model -> taskid;
        }else{
            $model -> taskname     = $follow['taskname'];
            $model -> taskformid   = $follow['taskformid'];
            $model -> taskmonth    = $follow['taskmonth'];
            $model -> taskcontent  = $follow['taskcontent'];
            $forminfo              = UForm::formInfo($follow['taskformid']);
            $model -> formname     = $forminfo['name'];
            $model -> formdata     = $forminfo['sourcedata'];
            if($model -> save()){
                //创建人也有此任务
                if(UUserTask::createUserTask($admin, $model -> taskid, $admin, $projectid)){
                    return true;
                }
                return false;
            }
            return false;
        }
        
    }
    /**
     * @todo 随访列表
     * @param userid
     * @param projectid
     */
    public static function showFollow($userid = '',$projectid){
        //暂时不用设管理员
        //用户是否为项目管理员
        //$admin = UUserProject::getPermitInProject($userid,$projectid);
        $admin = 1;
        if($admin == 1){
            $where['projectid'] = $projectid;
            $where['status']    = 1;
            $where['type']      = 2;
        }
        if($admin == 2){
            $where['projectid'] = $projectid;
            $where['status']    = 1;
            $where['admin']     = $userid;
            $where['type']      = 2;
        }
        $result = UProjectTask::find()
                ->where($where)
                ->orderBy(['taskmonth'=>SORT_ASC])
                ->asarray()
                ->all();
        return $result;
    }
    /**
     * @todo 删除随访
     * @param projectid
     * @param taskid
     */
    public static function deleteFollow($userid,$projectid,$taskid){
        if(UProjectTask::updateAll(['status'=>2,'updatetime'=>date('Y-m-d H:i:s')],['projectid'=>$projectid,'taskid'=>$taskid])){
            //用户任务
            UUserTask::deleteAllTask($taskid);
            return true;
        }
        return false;
    }
    /**
     * @todo 编辑随访
     * @param projectid
     * @param taskid
     */
    public static function editFollow(){
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $taskid    = Yii::$app->getRequest()->getBodyParam('taskid');
        $model = UProjectTask::findOne(['projectid'=>$projectid,'taskid'=>$taskid]);
        $model -> updatetime  = date('Y-m-d H:i:s');
        $model -> taskname    = Yii::$app->getRequest()->getBodyParam('taskname');
        $model -> taskformid  = Yii::$app->getRequest()->getBodyParam('taskformid');
        $model -> taskmonth   = Yii::$app->getRequest()->getBodyParam('taskmonth');
        $model -> taskcontent = Yii::$app->getRequest()->getBodyParam('taskcontent');
        //$forminfo             = UForm::formInfo(Yii::$app->getRequest()->getBodyParam('taskformid'));
        $model -> formname    = Yii::$app->getRequest()->getBodyParam('formname');
        $model -> formdata    = Yii::$app->getRequest()->getBodyParam('formdata');
        if($model->save()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 任务列表
     * @param projectid
     */
    public static function getTasklist($projectid){
        $result = UProjectTask::find()
                -> select('taskid,taskname,type')
                -> where(['projectid'=>$projectid,'status'=>1])
                -> asarray()
                -> all();
        return $result;
    }
    /**
     * @todo 项目基线任务id
     * @param projectid
     */
    public static function writeTaskid($projectid){
        $result = UProjectTask::find()
        -> select('taskid')
        -> where(['projectid'=>$projectid,'type'=>1])
        -> asarray()
        -> one();
        return $result['taskid'];
    }
    /**
     * @todo 最近一次随访
     * @param userid
     * @param projectid
     */
    public static function lastFollow($projectid,$userid){
        //用户在项目中的角色
        $admin = UUserProject::getPermitInProject($userid,$projectid);
        if($admin == 1){
            $where['projectid'] = $projectid;
            $where['status']    = 1;
            $where['type']      = 2;
        }
        if($admin == 2){
            $where['projectid'] = $projectid;
            $where['status']    = 1;
            $where['admin']     = $userid;
            $where['type']      = 2;
        }
        $result = UProjectTask::find()
        ->select('taskmonth')
        ->where($where)
        ->orderBy(['taskmonth'=>SORT_ASC])
        ->limit(1)
        ->asarray()
        ->one();
        return $result['taskmonth'];
    }
    /**
     * @todo 项目随访任务的的信息
     * @param taskid
     */
    public static function followInfo($taskid){
        $result = UProjectTask::find()
        -> where(['taskid'=>$taskid])
        -> asarray()
        -> one();
        return $result;
    }
}
