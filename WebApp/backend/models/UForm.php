<?php

namespace app\models;

use Yii;
use app\models\Commonfun;
/**
 * This is the model class for table "u_form".
 *
 * @property string $formid
 * @property string $userid
 * @property string $share_userid
 * @property string $create_userid
 * @property string $update_userid
 * @property string $create_time
 * @property string $update_time
 * @property string $name
 * @property string $sourcedata
 * @property integer $state
 */
class UForm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_form';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['formid'], 'required'],
            [['create_time', 'update_time'], 'safe'],
            [['sourcedata'], 'string'],
            [['state'], 'integer'],
            [['formid', 'userid', 'share_userid', 'create_userid', 'update_userid', 'name'], 'string', 'max' => 225],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'formid' => 'Formid',
            'userid' => 'Userid',
            'share_userid' => 'Share Userid',
            'create_userid' => 'Create Userid',
            'update_userid' => 'Update Userid',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'name' => 'Name',
            'sourcedata' => 'Sourcedata',
            'state' => 'State',
        ];
    }
    /**
     * @todo 关联用户表
     */
    public function getMUserinfo(){
        return $this->hasMany(MUserinfo::className(), ['s_userid' => 'share_userid']);
    }
    /**
     * @todo 创建表单
     * @param userid
     * @param name
     * @param sourcedata
     */
    public static function createForm($userid,$name,$sourcedata){
        $model = new UForm();
        $model -> formid            = Commonfun::randpw();
        $model -> userid            = $userid;
        $model -> create_userid     = $userid;
        $model -> create_time       = date('Y-m-d H:i:s');
        $model -> update_time       = date('Y-m-d H:i:s');
        $model -> name              = $name;
        $model -> sourcedata        = $sourcedata;
        $model -> state             = 1;
        if($model->save()){
            return $model -> formid;
        }
        return false;
    }
    /**
     * @todo 表单信息
     * @param formid
     */
    public static function formInfo($formid){
        $forminfo = UForm::find()
        ->where(['formid'=>$formid])
        ->asarray()
        ->one();
        return $forminfo;
    }
    /**
     * @todo 分享表单
     * @param share_userid
     * @param userid
     * @param formid
     */
    public static function shareForm($formid,$share_userid,$userid){
        //表单信息
        $forminfo = UForm::formInfo($formid);
        $model    = new UForm();
        $model    -> formid             = Commonfun::randpw();
        $model    -> userid             = $userid;
        $model    -> share_userid       = $share_userid;
        $model    -> create_userid      = $forminfo['create_userid'];
        $model    -> name               = $forminfo['name'];
        $model    -> sourcedata         = $forminfo['sourcedata'];
        $model    -> create_time        = date('Y-m-d H:i:s');
        $model    -> update_time        = date('Y-m-d H:i:s');
        $model    -> state              = 1;
        if($model -> save()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 删除表单
     * @param userid
     * @param formid
     */
    public static function deleteForm($userid,$formid,$state){
        //权限
        $forminfo   = UForm::formInfo($formid);
        if($userid != $forminfo['userid']){
            return false;
        }
        $model      =  UForm::findOne(['formid'=>$formid]);
        $model      -> state         = $state;
        $model      -> update_userid = $userid;
        $model      -> update_time   = date('Y-m-d H:i:s');
        if($model -> save()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 编辑表单
     * @param update_userid
     * @param formid
     * @param name
     * @param sourcedata
     */
    public static function editForm($update_userid,$formid){
        //权限
        $forminfo   = UForm::formInfo($formid);
        if($update_userid != $forminfo['userid']){
            return false;
        }
        $model      =  UForm::findOne(['formid'=>$formid]);
        $model      -> name          = Yii::$app->getRequest()->getBodyParam('name');
        $model      -> sourcedata    = Yii::$app->getRequest()->getBodyParam('sourcedata');
        $model      -> state         = 1;
        $model      -> update_userid = $update_userid;
        $model      -> update_time   = date('Y-m-d H:i:s');
        if($model -> save()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 表单列表
     * @param userid
     */
    public static function formList($userid){
        $type  = Yii::$app->getRequest()->getBodyParam('type');
        if($type == 'publish'){
            $where = ['u_form.state'=>[2],'u_form.userid'=>$userid];
        }else{
            $where = ['u_form.state'=>[1,2],'u_form.userid'=>$userid];
        }
        $formlist = UForm::find()
                  ->joinWith('mUserinfo')
                  ->select('u_form.formid,u_form.name,u_form.share_userid,u_form.update_time,u_form.state,u_form.sourcedata,m_userinfo.s_username,m_userinfo.s_avatar')
                  ->where($where)
                  ->orderBy(['u_form.update_time'=>SORT_DESC])
                  ->asarray()
                  ->all();
        foreach ($formlist as $k=>$v){
            unset($formlist[$k]['mUserinfo']);
        }
        return $formlist;
    }
}
