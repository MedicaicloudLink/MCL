<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "i_invitedata".
 *
 * @property integer $id
 * @property string $userid
 * @property string $tellphone
 * @property string $createtime
 * @property integer $status
 * @property string $registtime
 */
class IInvitedata extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'i_invitedata';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'tellphone'], 'required'],
            [['createtime', 'registtime'], 'safe'],
            [['status'], 'integer'],
            [['userid'], 'string', 'max' => 32],
            [['tellphone'], 'string', 'max' => 255],
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
            'tellphone' => 'Tellphone',
            'createtime' => 'Createtime',
            'status' => 'Status',
            'registtime' => 'Registtime',
        ];
    }
    /**
     * @todo 某个电话号的是否被邀请过
     */
    public static function isMobileInvite($mobile){
        $inviteinfo = IInvitedata::find()
                    ->where(['tellphone'=>$mobile,'status'=>1])
                    ->all();
        return $inviteinfo;
    }
    /**
     * @todo 是否已有邀请
     */
    public static function isInvite($userid,$mobile){
        $inviteinfo = IInvitedata::find()
                    ->where(['userid'=>$userid,'tellphone'=>$mobile,'status'=>1])
                    ->one();
        return $inviteinfo;
    }
    /**
     * @todo 添加邀请
     */
    public static function addInvite($userid,$mobile){
        $inviteinfo = IInvitedata::isInvite($userid,$mobile);
        if(empty($inviteinfo)){
            $model = new IInvitedata();
            $model -> userid     = $userid;
            $model -> tellphone  = $mobile;
            $model -> createtime = date('Y-m-d H:i:s');
            $model -> save();
        }else{
            IInvitedata::updateAll(['createtime'=>date('Y-m-d H:i:s')],['id'=>$inviteinfo['id']]);
        }
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        /**
         * @todo 发送短信
         */
        $SignName     = '梅地卡尔临床数据云';
        $name         = MUserinfo::userInfo($userid)[0]['s_username'];
        if($projectid != ''){
            $projectname  = UProjectdata::getProjectDetailByProjectId($projectid)[0]['u_projectName'];
            $templateid   = 'SMS_99010016';
            $param        = '{"name":"'.$name.'","project":"'.$projectname.'"}';
        }else{
            $templateid   = 'SMS_99100010';
            $param        = '{"name":"'.$name.'"}';
        }
        $path         = Yii::$app->basePath.'/../';
        require_once("".$path."vendor/taobao-sdk/SendSMS.php");
        $sendsms      = new \SendSMS();
        $result       = $sendsms->ToSendSms($mobile,$SignName,$param,$templateid);
        return true;
    }
}
