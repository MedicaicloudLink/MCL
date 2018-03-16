<?php

namespace app\models;

use Yii;
use common\models\LoginForm;
/**
 * This is the model class for table "u_user".
 *
 * @property string $s_userid
 * @property string $s_userpassword
 * @property string $s_mail
 * @property string $s_last_login_time
 */
class UUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['s_userid', 's_userpassword'], 'required'],
            [['s_last_login_time'], 'safe'],
            [['s_userid'], 'string', 'max' => 11],
            [['s_userpassword', 's_mail'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            's_userid' => 'S Userid',
            's_userpassword' => 'S Userpassword',
            's_mail' => 'S Mail',
            's_last_login_time' => 'S Last Login Time',
        ];
    }
    /**
     * @todo 用户信息
     */
    public static function userInfo($userid){
        $info = UUser::find()
              ->where(['s_userid'=>$userid])
              ->asarray()
              ->one();
        return $info;
    }
    /**
     * @todo 创建用户
     */
    public static function createUser($userid,$data){
        if(empty(UUser::userInfo($userid))){
            $model = new UUser();
            $model -> s_userid          = $userid;
            $model -> s_userpassword    = md5(md5($data['password']));
            $model -> s_mail            = $data['mail'];
            $model -> s_last_login_time = date('Y-m-d H:i:s');
            if($model -> save()){
                return true;
            }else{
                return false;
            }
        }
        return true;
    }
    /**
     * @todo 登录
     */
    public static function login($s_userid,$passwd){
        $model = new LoginForm();
        $model -> username = $s_userid;
        $model -> password = $passwd;
        if ($model->login()) {
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 重置密码
     */
    public static function setPasswd($userid,$password){
        if(UUser::updateAll(['s_userpassword' => md5(md5($password)),'s_last_login_time'=>date('Y-m-d H:i:s')], ['s_userid'=>$userid])){
            return true;
        }
        return false;
    }
    /**
     * 删除用户
     * @param userid
     * @return bool
     */
    public static function delUser($userid){
        $mail = UUser::userInfo($userid)['s_mail'];
        UUserInfo::deleteAll(['s_mail'=>$mail]);
        UUser::deleteAll(['s_mail'=>$mail]);
        UUserAchievement::deleteAll(['s_mail'=>$mail]);

        UUserInfo::deleteAll(['s_userid'=>$userid]);
        UUser::deleteAll(['s_userid'=>$userid]);
        UUserAchievement::deleteAll(['s_userid'=>$userid]);

        QCompany::deleteAll(['createuser'=>$userid]);
        QCompanyUser::deleteAll(['userid'=>$userid]);

        QCompanyUser::deleteAll(['mail'=>$mail]);

        PProjectUser::deleteAll(['userid'=>$userid]);
        PProjectInvite::deleteAll(['userid'=>$userid]);
        PProjectInvite::deleteAll(['touserid'=>$userid]);

        PProjectInvite::deleteAll(['tomail'=>$mail]);

        PProjectDocument::deleteAll(['createuser'=>$userid]);
        PProject::deleteAll(['createuserid'=>$userid]);
        PApplyToProject::deleteAll(['userid'=>$userid]);
        PApplyToProject::deleteAll(['admin'=>$userid]);
        NNewsfeedComment::deleteAll(['userid'=>$userid]);
        NNewsfeed::deleteAll(['userid'=>$userid]);
        EMail::deleteAll(['to_mail'=>$mail]);
        return true;
    }
    public static function upPassword(){
        $userid      = Yii::$app->getRequest()->getBodyParam('userid');
        $oldpassword = Yii::$app->getRequest()->getBodyParam('oldpassword');
        $newpassword = Yii::$app->getRequest()->getBodyParam('newpassword');
        $repassword  = Yii::$app->getRequest()->getBodyParam('repassword');
        //判断旧密码是否正确
        $useroldinfo = UUser::find()
            -> select('s_userpassword')
            -> where(['s_userid'=>$userid])
            -> one();
        //正确的原密码
        $password    = $useroldinfo->s_userpassword;
        //判断输入值是否与原密码一致
        if($password != md5(md5($oldpassword))){
            return 'olderror';
        }
        if($newpassword != $repassword){
            return 'reerror';
        }
        if(UUser::updateAll(['s_userpassword' => md5(md5($newpassword))], ['s_userid'=>$userid])){
            return 'succ';
        }
        return 'succ';
    }
}
