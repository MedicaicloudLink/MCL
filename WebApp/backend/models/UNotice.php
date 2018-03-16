<?php

namespace app\models;

use Yii;
use app\models\UUserProject;
use Faker\Test\Provider\UuidTest;
use app\models\UProjectdata;
use app\models\UUsernotices;

/**
 * This is the model class for table "u_notice".
 *
 * @property integer $id
 * @property string $userid
 * @property string $projectid
 * @property integer $status
 * @property string $createtime
 * @property string $updatetime
 * @property string $adminid
 */
class UNotice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_notice';
    }

    /**
     * @inheritdoc
     */
    

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'projectid' => 'Projectid',
            'status' => 'Status',
            'createtime' => 'Createtime',
            'updatetime' => 'Updatetime',
            'adminid' => 'Adminid',
        ];
    }
    /**
     * @todo 关联用户表
     */
     public function getMUserinfo(){
        return $this->hasMany(MUserinfo::className(), ['s_userid' => 'userid']);
    }
    /**
     * @todo 关联项目表
     */
    public function getUProjectdata(){
        return $this->hasMany(UProjectdata::className(), ['u_projectID' => 'projectid']);
    }
    /**
     * @todo  getlist
     */
    public static function getList($userid){
        //用户具有超级管理员的项目
        $projectid = UUserProject::find()
        ->select('u_projectID')
        ->where(['u_userID'=>$userid,'u_permission'=>1])
        ->asarray()
        ->all();
        if(!empty($projectid)){
            foreach ($projectid as $k=>$v){
                $projectidarr[] = $v['u_projectID']; #项目id
            }
            $result = UNotice::find()
            ->select('u_notice.*,m_userinfo.s_username,u_projectdata.u_projectName')
            ->joinWith('mUserinfo')
            ->joinWith('uProjectdata')
            ->where(['u_notice.projectid'=>$projectidarr])
            ->orderBy(['u_notice.createtime'=>SORT_DESC])
            ->asarray()
            ->all();
            foreach($result as $key=>$val){
                unset($result[$key]['uProjectdata']);
                unset($result[$key]['mUserinfo']);
            }
        }else{
            $result = '';
        }
        return $result;
    }
    
    /**
     * @todo 处理接口
     * @param userid
     * @param noticeid
     * @param status
     */
    public static function handleNotice(){
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $noticeid  = Yii::$app->getRequest()->getBodyParam('noticeid');
        $status    = Yii::$app->getRequest()->getBodyParam('status');
        //是否为项目管理员
        $groupid   = UNotice::find()
        ->select('projectid')
        ->where(['id'=>$noticeid])
        ->asarray()
        ->all();
        if(empty($groupid)){
            return false; #没有此条通知
        }
        foreach($groupid as $k=>$v){
            $grouparr = $v['projectid'];
        }
        //项目管理员
        $arr       = UUserProject::find()
        ->select('u_userID')
        ->where(['u_projectID'=>$grouparr,'u_permission'=>1])
        ->asarray()
        ->all();
        foreach($arr as $key=>$val){
            $userarr[] = $val['u_userID'];
        }
        if(!in_array($userid,$userarr)){
            return false;
        }
        $model  = UNotice::findOne(['id'=>$noticeid]);
        $model  -> status       = $status;
        $model  -> adminid      = $userid;
        $model  -> updatetime   = date('Y-m-d H:i:s');
        if($model->save()){
            /**@todo 同步通知消息表*/
            UUsernotices::createUsernotice(3, $userid, $model->projectid, $noticeid);
            if($status == 1){
                //是否已经存在
                if(UUserProject::userInProjectIsExist($model->userid,$model->projectid)){
                    return false;
                }else{
                    //同意           是项目中一员
                    if(UUserProject::addMemberForProject($model->projectid,$model->userid,'N',$userid)){
                        return true;
                    }else{
                        return false;
                    }
                }
            }
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 添加申请加入项目记录
     * @param userid
     * @param projectid
     */
    public static function addNotice($userid,$projectid){
        //判断项目是否存在
        $project = UProjectdata::find()->where(['u_projectID'=>$projectid])->all();
        if(empty($project)){
            return 'noproject';
        }
        //判断是否已经在项目中
        $arr = UUserProject::find()
        ->where(['u_userID'=>$userid,'u_projectID'=>$projectid])
        ->all();
        if(!empty($arr)){
            return 'exist';
        }
        $model = new UNotice();
        $model -> userid      = $userid;
        $model -> projectid   = $projectid;
        $model -> status      = 0;
        $model -> createtime  = date('Y-m-d H:i:s');
        if($model->save()){
            /** @todo 创建通知*/
            UUsernotices::createUsernotice(1, $userid, $projectid, $model->id);
            return 'ok';
        }else{
            return 'no';
        }
    }
    /**
     * @todo 申请加入项目列表
     * @param userid
     * @param projectid
     */
    public static function applyList($projectid,$userid){
        //判断用户是不是项目中管理员
        $role = UUserProject::find()
        ->where(['u_projectID'=>$projectid,'u_userID'=>$userid,'u_permission'=>1])
        ->all();
        if(empty($role)){
            $result = '';
        }
        $result = UNotice::find()
        ->select('u_notice.*,m_userinfo.s_username,m_userinfo.s_sex,m_userinfo.s_mybirthday,m_userinfo.s_workunti,')
        ->joinWith('mUserinfo')
        ->where(['u_notice.projectid'=>$projectid,'u_notice.status'=>0])
        ->orderBy(['u_notice.createtime'=>SORT_DESC])
        ->asarray()
        ->all();
        foreach($result as $key=>$val){
            unset($result[$key]['mUserinfo']);
        }
        return $result;
    }
}
