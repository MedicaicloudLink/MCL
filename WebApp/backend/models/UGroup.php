<?php

namespace app\models;

use Yii;
use app\models\Commonfun;
/**
 * This is the model class for table "u_group".
 *
 * @property string $u_groupid
 * @property string $u_groupname
 * @property string $u_groupmem
 * @property string $u_groupcreate
 * @property string $u_createtime
 */
class UGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_groupid','u_projectid'], 'required'],
            [['u_createtime'], 'safe'],
            [['u_groupname'], 'string', 'max' => 32],
            [['u_groupcreate'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'u_groupid' => 'U Groupid',
            'u_groupname' => 'U Groupname',
            'u_projectid' => 'U Projectid',
            'u_groupcreate' => 'U Groupcreate',
            'u_createtime' => 'U Createtime',
        ];
    }
    /**
     * @todo 创建组
     * @param groupname 
     * @param projectid
     * @param userid
     * @return groupid
     */
    public static function createGroup(){
        $model = new UGroup();
        $model -> u_groupid     = Commonfun::randpw();
        $model -> u_groupname   = Yii::$app->getRequest()->getBodyParam('groupname');
        $model -> u_projectid   = Yii::$app->getRequest()->getBodyParam('projectid');
        $model -> u_groupcreate = Yii::$app->getRequest()->getBodyParam('userid');
        $model -> u_createtime  = date('Y-m-d H:i:s');
        if($model->save()){
            return $model -> u_groupid;
        }else{
            return false;
        }
    }
    /**
     * @todo 最大的记录id
     * return   string
     */
    public static function maxRecordid(){
        $arr = UGroup::find()
        ->select('u_groupid')
        ->orderBy(['u_groupid'=>SORT_DESC])
        ->limit(1)
        ->asarray()
        ->all();
        if(empty($arr) || $arr[0]['u_groupid']==''){
            $record = 1;
        }else{
            $record = $arr[0]['u_groupid']+1;
        }
        return $record;
    }
    /**
     * @todo 组列表
     */
    public static function groupList(){
        //组信息
        $arr = UGroup::find()
        ->where(['u_groupcreate'=>Yii::$app->getRequest()->getBodyParam('userid'),'u_projectid'=>Yii::$app->getRequest()->getBodyParam('projectid')])
        ->orderBy(['u_createtime'=>SORT_DESC])
        ->asarray()
        ->all();
        //组内人数
        if(!empty($arr)){
            foreach ($arr as $k=>$v){
                $arr[$k]['num'] = UPatientgroup::peopleNum($v['u_groupid']);
            }
        }
        return $arr;
    }
    /**
     * @todo 删除组
     */
    public static function deleteGroup(){
        $groupid        = rtrim(Yii::$app->getRequest()->getBodyParam('groupid'),',');
        $grouparr       = explode(',', $groupid);
        $groupre        = UGroup::deleteAll(['u_groupid'=>$grouparr]);
        $patientgroupre = UPatientgroup::deleteAll(['u_groupid'=>$grouparr]);
        return true;
    }
    /**
     * @todo 修改组
     */
    public static function updateGroup(){
        $groupid    =  Yii::$app->getRequest()->getBodyParam('groupid');
        $groupname  =  Yii::$app->getRequest()->getBodyParam('groupname');
        $model      =  UGroup::findOne(['u_groupid'=>$groupid]);
        $model      -> u_groupname  = $groupname;
        $model      -> u_createtime = date('Y-m-d H:i:s');
        $model      -> save();
        return true;
    }
    /**
     * @todo 搜索组
     * @param userid
     * @param projectid
     * @param groupname
     * @return array
     */
    public static function searchGroup($userid,$projectid,$groupname){
        $arr = UGroup::find()
                ->where(['u_groupcreate'=>$userid,'u_projectid'=>$projectid])
                ->andwhere(['like','u_groupname',$groupname])
                ->orderBy(['u_createtime'=>SORT_DESC])
                ->asarray()
                ->all();
        //组内人数
        if(!empty($arr)){
            foreach ($arr as $k=>$v){
                $arr[$k]['num'] = UPatientgroup::peopleNum($v['u_groupid']);
            }
        }
        return $arr;
    }
    /**
     * @todo 项目内组id
     * @param projectid
     */
    public static function getGroupid($projectid){
        $arr = UGroup::find()
             ->select('u_groupid')
             ->where(['u_projectid'=>$projectid])
             ->asarray()
             ->all();
        $mdid = [];
        if(!empty($arr)){
            foreach($arr as $k=>$v){
                $mdid[] = $v['u_groupid'];
            }
        }
        return $mdid;
    }
}
