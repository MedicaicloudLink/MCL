<?php

namespace app\models;

use Yii;
use app\models\Commonfun;
use app\models\UUser;
use app\models\UUserInfo;
use app\models\QCompanyUser;
/**
 * This is the model class for table "q_company".
 *
 * @property string $id
 * @property string $name
 * @property string $domain
 * @property string $city
 * @property string $createuser
 * @property string $createtime
 * @property string $admin
 * @property string $updatetime
 * @property integer $isagree
 * @property integer $status
 * @property string $confirm_mail
 * @property string $finish_mail
 */
class QCompany extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'q_company';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['createtime', 'updatetime'], 'safe'],
            [['isagree', 'status','isinviteagree'], 'integer'],
            [['id', 'createuser'], 'string', 'max' => 11],
            [['name'], 'string', 'max' => 64],
            [['domain', 'city','logo','website','confirm_mail','finish_mail'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'domain' => 'Domain',
            'city' => 'City',
            'createuser' => 'Createuser',
            'createtime' => 'Createtime',
            'updatetime' => 'Updatetime',
            'isagree' => 'Isagree',
            'status' => 'Status',
        ];
    }
    /**
     * @todo 企业信息
     */
    public static function companyInfo($company_id){
        $info = QCompany::find()
              ->select('id,name,domain,city,isagree,isinviteagree,finish_mail')
              -> where(['id'=>$company_id,'status'=>1])
              -> asarray()
              -> one();
        return $info;
    }
    /**
     * 二级域名信息
     * @var $domain
     * @return array
     */
    public static function domainInfo($domain){
        $info = QCompany::find()
              -> select('*')
              -> where(['lower(domain)'=>$domain])
              -> asarray()
              -> one();
        return $info;
    }
    /**
     * @todo 创建企业
     */
    public static function createCompany($data){
        //创建企业
        $model = new QCompany();
        $model -> id           = Commonfun::randpw();
        $model -> name         = $data['companyname'];
        $model -> domain       = $data['domain'];
        $model -> city         = $data['city'];
        $model -> createtime   = date('Y-m-d H:i:s');
        $model -> updatetime   = date('Y-m-d H:i:s');
        $model -> confirm_mail = '[]';
        $model -> finish_mail  = '[]';
        $model -> status       = 1;
        $createuser            = Commonfun::randpw();
        $model -> createuser   = $createuser;
        //创建用户
        if($model -> save() && UUser::createUser($createuser,$data) && UUserInfo::createUser($createuser,$data) && QCompanyUser::createUser($createuser,$model -> id,$data['mail'],1,2)){
            $info['userid']    = $createuser;
            $info['companyid'] = $model -> id;
            $info['type']      = 1;
            $info['domain']    = $data['domain'].'.'.Yii::$app->params['domainurl'];
            //直接登录
            UUser::login($createuser, $data['password']);
            return $info;
        }else{
            return false;
        }
    }
    /**
     * 编辑企业
     * @param companyid
     * @param typeval
     * @return bool
     */
    public static function editCompany($companyid,$typeval){
        $type = [
            'name',
            'city',
            'isagree',
            'isinviteagree',
            'logo',
            'website'
        ];
        $arr = json_decode($typeval,true);
        if(!empty($arr)){
            foreach($arr as $k=>$v){
                if(!in_array($k,$type)){
                    return false;
                }else{
                    if($k == 'city'){
                        $v = json_encode($v);
                    }
                    QCompany::updateAll([$k=>$v,'updatetime'=>date('Y-m-d H:i:s')],['id'=>$companyid]);
                }
            }
        }else{

        }
        return true;
    }
    /**
     * 加入验证
     * @param mail
     * @param houzhui
     * @param company
     * @return bool
     */
    public static function addConfirmMail($mail,$houzhui,$company){
        $confirmmail    = $company['confirm_mail'];
        $confirmmailarr = json_decode($confirmmail,true);
        if(!empty($confirmmailarr)){
            $flag = 1;#有没有改变
            foreach ($confirmmailarr as $k=>$v){
                if($v['mail'] == $houzhui){
                    $confirmmailarr[$k]['sendtime'] = date('Y-m-d H:i:s');
                    $flag = 2;
                }
            }
            if($flag != 2){
                $count = count($confirmmailarr);
                //直接添加
                $confirmmailarr[$count]['mail']     = $houzhui;
                $confirmmailarr[$count]['sendtime'] = date('Y-m-d H:i:s');
            }
        }else{
            //直接添加
            $confirmmailarr[0]['mail']     = $houzhui;
            $confirmmailarr[0]['sendtime'] = date('Y-m-d H:i:s');
        }
        $confirm = json_encode($confirmmailarr);
        QCompany::updateAll(['confirm_mail'=>$confirm],['id'=>$company['id']]);
        //发送邮件
        $mmodel   = new EMail();
        $mmodel   -> id       = Commonfun::randpw();
        $mmodel   -> to_mail  = $mail;
        $mmodel   -> sendtime = date('Y-m-d H:i:s');
        $mmodel   -> offtime  = "2027-10-10";
        $mmodel   -> save();
        $subject  = '梅地卡尔-请验证您的企业的邮箱域名后缀';
        $path     = '/mail/company_mail';
        $content  = ['url'=>"".$company['domain'].".".Yii::$app->params['domainurl']."/company/companymail?mailid=".$mmodel->id.""];
        Commonfun::sendMail(Yii::$app->params['loginmail'],$mail,$subject,$path,$content);
        return true;
    }
    /**
     * 加入已经完成验证的
     * @param houzhui
     * @param company
     * @return bool
     */
    public static function addFinishMail($houzhui,$company){
        $companyfinish = json_decode($company['finish_mail'],true);
        $count         = count($companyfinish);
        //直接添加
        $companyfinish[$count]['mail']     = $houzhui;
        $finish        = json_encode($companyfinish);
        QCompany::updateAll(['finish_mail'=>$finish],['id'=>$company['id']]);
        //删除验证中的
        $confirmmail    = $company['confirm_mail'];
        $confirmmailarr = json_decode($confirmmail,true);
        if(!empty($confirmmailarr)) {
            foreach ($confirmmailarr as $k => $v) {
                if ($v['mail'] == $houzhui) {
                    unset($confirmmailarr[$k]);
                }
            }
        }
        $confirmmailarr = array_values($confirmmailarr);
        $confirm        = json_encode($confirmmailarr);
        QCompany::updateAll(['confirm_mail'=>$confirm],['id'=>$company['id']]);
        return true;
    }
    /**
     * 删除企业域名邮箱
     * @param mailsuffix
     * @param company
     * @param type(1.认证中 2.已认证)
     * @return bool
     */
    public static function delMail($mailsuffix,$company,$type){
        if($type == 1){
            $confirmmail    = $company['confirm_mail'];
            $confirmmailarr = json_decode($confirmmail,true);
            if(!empty($confirmmailarr)) {
                foreach ($confirmmailarr as $k => $v) {
                    if ($v['mail'] == $mailsuffix) {
                        unset($confirmmailarr[$k]);
                    }
                }
            }
            $confirmmailarr = array_values($confirmmailarr);
            $confirm        = json_encode($confirmmailarr);
            QCompany::updateAll(['confirm_mail'=>$confirm],['id'=>$company['id']]);
        }
        if($type == 2){
            $finishmail     = $company['finish_mail'];
            $finishmailarr  = json_decode($finishmail,true);
            if(!empty($finishmailarr)) {
                foreach ($finishmailarr as $k => $v) {
                    if ($v['mail'] == $mailsuffix) {
                        unset($finishmailarr[$k]);
                    }
                }
            }
            $finishmailarr = array_values($finishmailarr);
            $finish        = json_encode($finishmailarr);
            QCompany::updateAll(['finish_mail'=>$finish],['id'=>$company['id']]);
        }
        return true;
    }
}
