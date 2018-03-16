<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "e_mail".
 *
 * @property string $id
 * @property string $to_mail
 * @property string $sendtime
 * @property string $offtime
 */
class EMail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'e_mail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['sendtime', 'offtime'], 'safe'],
            [['id'], 'string', 'max' => 11],
            [['to_mail'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'to_mail' => 'To Mail',
            'sendtime' => 'Sendtime',
            'offtime' => 'Offtime',
        ];
    }
    /**
     * @todo 是否可用
     * @param mailid
     */
    public static function mailInfo($mailid){
        $info = EMail::find()
              ->select('offtime,to_mail')
              ->where(['id'=>$mailid])
              ->asarray()
              ->one();
        if(!empty($info) && date('Y-m-d H:i:s')<$info['offtime']){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 邮箱信息
     * @param mailid
     */
    public static function mail($mailid){
        $info = EMail::find()
        ->select('offtime,to_mail')
        ->where(['id'=>$mailid])
        ->asarray()
        ->one();
        if(!empty($info) && date('Y-m-d H:i:s')<$info['offtime']){
            return $info;
        }else{
            return false;
        }
    }
    /**
     * @todo 登录验证创建
     */
    public static function sendMail($mail){
        $sendmail  = Yii::$app->params['loginmail'];
        $subject   = '欢迎使用梅地卡尔-完成您的邮箱验证';
        $path      = '/mail/regist_mail';
        $mmodel   = new EMail();
        $mmodel   -> id       = Commonfun::randpw();
        $mmodel   -> to_mail  = $mail;
        $mmodel   -> sendtime = date('Y-m-d H:i:s');
        $mmodel   -> offtime  = date('Y-m-d H:i:s',time()+86400*Yii::$app->params['registtime']);
        $mmodel   -> save();
        $content  = ['url'=>"www.".Yii::$app->params['domainurl']."/company/company?mailid=".$mmodel->id.""];
        Commonfun::sendMail($sendmail,$mail,$subject,$path,$content);
        return true;
    }
    /**
     * @todo 忘记密码创建
     */
    public static function sendForgetMail($mail,$domain){
        $sendmail  = Yii::$app->params['loginmail'];
        $subject   = '忘记密码验证';
        $path      = '/mail/setpwd';
        $mmodel   = new EMail();
        $mmodel   -> id       = Commonfun::randpw();
        $mmodel   -> to_mail  = $mail;
        $mmodel   -> sendtime = date('Y-m-d H:i:s');
        $mmodel   -> offtime  = date('Y-m-d H:i:s',time()+3600);
        $content  = ['url'=>"".$domain.".".Yii::$app->params['domainurl']."/login/resetpw?mailid=".$mmodel->id.""];
        if(Commonfun::sendMail($sendmail,$mail,$subject,$path,$content)){
            $mmodel   -> save();
        }
        return true;
    }
}
