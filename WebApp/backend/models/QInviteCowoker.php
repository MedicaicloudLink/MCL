<?php

namespace app\models;

use Yii;
use app\models\MUserinfo;
use app\models\QCompany;
use app\models\EMail;
/**
 * This is the model class for table "q_invite_cowoker".
 *
 * @property integer $id
 * @property string $admin
 * @property string $company_id
 * @property string $cowoker_name
 * @property string $cowoker_mail
 * @property string $invitetime
 */
class QInviteCowoker extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'q_invite_cowoker';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invitetime'], 'safe'],
            [['admin', 'company_id'], 'string', 'max' => 11],
            [['cowoker_name'], 'string', 'max' => 32],
            [['cowoker_mail'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'admin' => 'Admin',
            'company_id' => 'Company ID',
            'cowoker_name' => 'Cowoker Name',
            'cowoker_mail' => 'Cowoker Mail',
            'invitetime' => 'Invitetime',
        ];
    }
    /**
     * @todo 用户被邀请情况
     * @param mail
     */
    public static function inviteInfo($mail){
        $info = QInviteCowoker::find()
              -> where(['cowoker_mail'=>$mail])
              -> orderby(['invitetime'=>SORT_DESC])
              -> limit(1)
              -> asarray()
              -> one();
        if(time()>strtotime($info['invitetime'])+86400*Yii::$app->params['invitetime']){
            $info = [];
        }
        return $info;
    }
    /**
     * @todo 用户被邀请情况
     * @param mail
     */
    public static function inviteInfoByCidAndMail($mail,$companyid){
        $info = QInviteCowoker::find()
        -> where(['cowoker_mail'=>$mail,'company_id'=>$companyid])
        -> one();
        return $info;
    }
    /**
     * @todo 邀请用户
     */
    public static function createInvite($userid,$companyid,$inviteinfo,$type){
        //邮箱是否已经被占用
        $invitearr = json_decode($inviteinfo,true);
        //企业信息
        $company   = QCompany::companyInfo($companyid);
        $userinfo  = MUserinfo::userInfo($userid);
        $sendmail  = Yii::$app->params['loginmail'];
        if($type == 1){
            #企业
            $subject = $company['name'].'发送的梅地卡尔临床云帐户激活邀请';
            $path    = '/mail/company_invite_mail';
        }else{
            #个人
            $subject = '快来一起工作！'.$userinfo[0]['s_username'].'邀请您注册梅地卡尔临床数据云';
            $path    = '/mail/worker_invite_mail';
        }
        $count     = 0;
        $_model    = new QInviteCowoker();
        $_mmodel   = new EMail();
        foreach($invitearr as $k=>$v){
            //if(empty(MUserinfo::mailInfo($v['mail']))){
                //没被发送过邀请的或者同企业的人邀请过，都可以继续发
                //if(empty(QInviteCowoker::inviteInfo($v['mail'])) || !empty(QInviteCowoker::inviteInfoByCidAndMail($v['mail'],$companyid))){
                    $mmodel   = clone $_mmodel;
                    $mmodel   -> id       = Commonfun::randpw();
                    $mmodel   -> to_mail  = $v['mail'];
                    $mmodel   -> sendtime = date('Y-m-d H:i:s');
                    $mmodel   -> offtime  = date('Y-m-d H:i:s',time()+86400*Yii::$app->params['invitetime']);
                    if($type == 1){
                        #企业
                        $content  = ['url'=>"".$company['domain'].".".Yii::$app->params['domainurl']."/company/company?company_id=".$companyid."&mailid=".$mmodel->id."",'name'=>$company['name']];
                    }else{
                        #个人
                        $content  = ['url'=>"".$company['domain'].".".Yii::$app->params['domainurl']."/company/company?company_id=".$companyid."&mailid=".$mmodel->id."",'name'=>$userinfo[0]['s_username']];
                    }
                    if($v['mail'] != ''){
                        if(Commonfun::sendMail($sendmail,$v['mail'],$subject,$path,$content)){
                            $model    = clone $_model;
                            $model    -> admin        = $userid;
                            $model    -> company_id   = $companyid;
                            $model    -> cowoker_name = $v['name'];
                            $model    -> cowoker_mail = $v['mail'];
                            $model    -> invitetime   = date('Y-m-d H:i:s');
                            $model    -> save();
                            $mmodel   -> save();
                            $count++;
                        }
                    }
                //}
            //}
        }
        $result['count'] = $count;
        return $result;
    }
}

