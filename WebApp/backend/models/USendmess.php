<?php

namespace app\models;

use Yii;
use app\models\MUserinfo;

/**
 * This is the model class for table "u_sendmess".
 *
 * @property integer $id
 * @property string $mobile
 * @property string $code
 * @property string $sendtime
 */
class USendmess extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_sendmess';
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
            'mobile' => 'Mobile',
            'code' => 'Code',
            'sendtime' => 'Sendtime',
        ];
    }
    /**
     * 
     */
    public static function checkCode($mobile,$code){
        $sendinfo  = USendmess::find()
            -> where(['mobile' => $mobile])
            -> andwhere('sendtime > :time', [":time" => time() - 300])
            -> orderBy(['id'=>SORT_DESC])
            -> asarray()
            -> all();
        if (empty($sendinfo)) {
            return 'nouse'; #过期
        }
        if ($sendinfo[0]['code'] != $code) {
            return 'err';   #错误
        }
        return 'noerr';
    }
    /**
     * 
     */
    public static function getcode($mobile){
        if (!preg_match('/^1[\d]{10}$/', $mobile)) {
            return 'mobileerror';  #手机号格式不对
        }
        //手机号是否已注册
        $userinfo = MUserinfo::find()
        ->select('s_cellphone')
        ->where(['s_cellphone'=>$mobile])
        ->all();
        if(!empty($userinfo)){
            return 'exist';    #已经注册
        }
        $code  = mt_rand(100000, 999999);
        //@todo   发送验证码
        if(USendmess::sendSms($mobile,$code,1)){
            $model = new USendmess();
            $model -> mobile   = $mobile;
            $model -> code     = $code;
            $model -> sendtime = time();
            if($model->save()){
                return 'succ';
            }
        }else{
                return 'err';
        }
        
    }
    /**
     * @todo 发送验证码具体方法
     * @param type为1（注册的） 2是忘记密码
     */
    public static function sendSms($mobile,$code,$type){
        if($type == 1){
            $SignName     = Yii::$app->params['REGIST_SIGNNAME'];
            $templateid   = Yii::$app->params['REGIST_TEMPLATE_ID'];
        }
        if($type == 2){
            $SignName     = Yii::$app->params['UPPWD_SIGNNAME'];
            $templateid   = Yii::$app->params['UPPWD_TEMPLATE_ID'];
        }
        $param        = '{"code":"'.$code.'","product":"Medicayun"}';
        $path         = Yii::$app->basePath.'/../';
        require_once("".$path."vendor/taobao-sdk/SendSMS.php");
        $sendsms      = new \SendSMS();
        $result       = $sendsms->ToSendSms($mobile,$SignName,$param,$templateid);
        //var_dump($result);exit;
        return true;
    }
    /**
     * @todo 查看是否已经发送
     * @param mobile
     */
    public static function findCode($mobile){
        $result = USendmess::find()
        ->select('sendtime')
        ->where(['mobile'=>$mobile])
        ->orderBy(['sendtime'=>SORT_DESC])
        ->limit(1)
        ->asarray()
        ->all();
        if(empty($result)){
            return true;
        }
        if(time()-$result[0]['sendtime']<60){
            return false;
        }else{
            return true;
        }
    }
    /**
     * @todo 忘记密码发送验证码
     */
    public static function forgetpwGetcode($mobile){
        $code  = mt_rand(100000, 999999);
        //@todo   发送验证码
        if(USendmess::sendSms($mobile,$code,2)){
            $model = new USendmess();
            $model -> mobile   = $mobile;
            $model -> code     = $code;
            $model -> sendtime = time();
            if($model->save()){
                return true;
            }
        }else{
            return false;
        }
    }
}
