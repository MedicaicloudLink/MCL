<?php
namespace backend\controllers;

use Yii;
use common\models\LoginForm;
use app\models\QCompanyUser;
use backend\controllers\BaseController;
use app\models\Commonfun;
use app\models\QCompany;
use app\models\EMail;
use app\models\UUser;
use app\models\UUserLogin;
/**
 * login controller
 */
class LoginController extends BaseController
{
    /**
     * @todo 展示登录页面
     */
    public function actionLoginform(){
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('page404');
        }
        return $this->renderPartial('login',['companyname'=>$company['name']]);
    }

    /**
     * @todo 忘记密码
     * 
     */
    public function actionForgetpassword(){
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('page404');
        }
        return $this->renderPartial('forgetpw',['companyname'=>$company['name']]);
    }

    /**
     * @todo 展示忘记密码修改密码页
     * 
     */
    public function actionDisplayforgetpwuppwd(){
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('page404');
        }
        return $this->renderPartial('forgetpwinfo',['companyname'=>$company['name']]);
    }

    /**
     * @todo 邮箱情况  
     * @param mail  邮箱
     * @return string 1000-1050
     */
    public function actionMailstate(){
        self::checkParamIsEmpty(
                [
                    'mail'    => '邮箱不能为空'
                ],1000
        );
        $mail    = Yii::$app->getRequest()->getBodyParam('mail');
        //验证邮箱
        if(!Commonfun::isMail($mail)){
            return $this->Error('邮箱格式错误','',1001);
        }
        //是否注册过 1.未激活，2.正常 3.禁用 4.删除
        $userinfo = QCompanyUser::userStatusInAllCompany($mail);
        $ustatus  = $userinfo['status'];
        #已注册（禁用，正常）
        if(!empty($ustatus)){
            if(in_array(2,$ustatus)){
                #正常
                $companyinfo           = QCompany::companyInfo($userinfo['companyid']);
                $companyinfo['domain'] = $companyinfo['domain'].'.'.Yii::$app->params['domainurl'];
                return $this->Rsuccess('注册过，公司信息：',$companyinfo,1005);
            }else{
                #禁用
                return $this->Rsuccess('注册过，但被禁用了','',1007);
            }
        }else{
            #未注册(可能未激活或已删除或新的)
            //1.是否点击链接进来        company_id=''&mailid=''
            $company_id    = Yii::$app->request->get('company_id');
            $mailid        = Yii::$app->request->get('mailid');
            $userid        = Yii::$app->request->get('userid');
            if($company_id == '' && $mailid == ''){
                return $this->Rsuccess('没被邀请，创建企业','',1004);
            }else{
                #点击链接进入
                //链接是否有效
                if(!EMail::mailInfo($mailid)){
                    return $this->Error('链接无效','',1003);
                }
                $companyinfo = QCompany::companyInfo($company_id);
                $companyinfo['domain'] = $companyinfo['domain'].'.'.Yii::$app->params['domainurl'];
                $companyinfo['userid'] = $userid;
                return $this->Rsuccess('未注册，进入创建密码页',$companyinfo,1002);
            }
        }
    }
    /**
     * @todo 发送注册链接至邮箱   xxxx.com/zhuce/d/?mail=''&id=''
     * @param mail
     * @return string
     */
    public function actionSendregistmail(){
        self::checkParamIsEmpty(
                [
                    'mail'    => '邮箱不能为空'
                ],1000
        );
        $mail   = Yii::$app->getRequest()->getBodyParam('mail');
        $result = EMail::sendMail($mail);
        return $this->Rsuccess('发送成功');
    }
    /**
     * @todo 展示登录邮箱输入页
     * @return string
     */
    public function actionLoginmailinput(){
        return $this->renderPartial('loginMailInput');
    }
    /**
     * @todo 展示发送邮箱
     * @return string
     */
    public function actionLoginsendmail(){
        return $this->renderPartial('loginSendMail');
    }
    /**
     * @todo 展示注册个人页
     * @return string
     */
    public function actionRegisterpersonal(){
        $mail   = Yii::$app->request->get('mail');
        $userid = Yii::$app->request->get('userid');
        if($mail == '' || $userid == ''){
            return $this->Error('邮箱不能为空');
        }
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('page404');
        }
        $data['companyid']   = $company['id'];
        $data['companyname'] = $company['name'];
        $data['mail']        = $mail;
        $data['userid']      = $userid;
        return $this->renderPartial('registerPersonal',$data);
    }
    /**
     * @todo 忘记密码邮箱状态
     * @param mail
     * @return string
     */
    public function actionForgetpasswdmail(){
        self::checkParamIsEmpty(
                [
                    'mail'    => '邮箱不能为空'
                ],1000
        );
        $domain   = $this->getDomain();
        //域名企业信息
        $company  = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('page404');exit;
        }
        $mail     = Yii::$app->getRequest()->getBodyParam('mail');
        //用户信息 (用户是否属于这个企业)
        $userinfo = QCompanyUser::userStatus($mail);
        if(empty($userinfo) || $userinfo['status'] == 4 || $userinfo['status'] == 1 || $company['id'] != $userinfo['companyid']){
            return $this->Error('不存在这邮箱');
        }else{
            return $this->Rsuccess('继续',$mail);
        }
    }
    /**
     * @todo 忘记密码发送邮箱验证
     * @param mail
     */
    public function actionSendforgetpasswd(){
        self::checkParamIsEmpty(
                [
                    'mail'    => '邮箱不能为空'
                ],1000
        );
        $domain = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('page404');
        }
        $mail   = Yii::$app->getRequest()->getBodyParam('mail');
        EMail::sendForgetMail($mail,$domain);
        return $this->Rsuccess('发送成功',$mail);
    }
    /**
     * @todo 忘记密码页
     * @return string
     */
    public function actionResetpw(){
        $mailid = Yii::$app->request->get('mailid');
        $domain = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('page404');
        }else{
            //连接有效性
            if(!EMail::mailInfo($mailid)){
                #展示连接无效页
                return $this->renderPartial('page404');
            }else{
                return $this->renderPartial('resetpw',['companyname'=>$company['name'],'mailid'=>$mailid]);
            }
        }
    }
    /**
     * @todo 重置密码
     * @param mailid     邮箱id
     * @param password   密码  
     * @param repassword 第二次输入的密码
     * @return 1300-1310
     */
    public function actionSetpasswd(){
        self::checkParamIsEmpty(
                [
                    'mailid'     => '邮箱id不能为空',
                    'password'   => '密码不能为空',
                    'repassword' => '第二次输入的密码不能为空'
                ],1000
        );
        $mailid   = Yii::$app->getRequest()->getBodyParam('mailid');
        $passwd   = Yii::$app->getRequest()->getBodyParam('password');
        $repasswd = Yii::$app->getRequest()->getBodyParam('repassword');
        if($passwd != $repasswd){
            return $this->Error('两次密码输入不一致','',1301);
        }
        //根据id去查邮箱
        $mailinfo = EMail::mail($mailid);
        if($mailinfo){
            $mail = $mailinfo['to_mail'];
            //此邮箱是否已经注册
            $userinfo = QCompanyUser::userStatus($mail);
            if(empty($userinfo) || $userinfo['status'] == 4 || $userinfo['status'] == 1){
                return $this->Error('还没有注册','',1300);
            }else{
                if(UUser::setPasswd($userinfo['userid'],$passwd)){
                    return $this->Rsuccess('重置密码成功',$mail);
                }else{
                    return $this->Error('重置失败');
                }
            }
        }else{
            return $this->Error('重置失败，信息有误');
        }
        
    }
    /**
     * @todo 企业内邮箱登录
     * @param mail
     * @param password
     * @return string     1200-1210
     */
    public function actionEmaillogin(){
        self::checkParamIsEmpty(
                [
                    'mail'      => '邮箱不能为空',
                    'password'  => '密码不能为空'
                ],1000
        );
        $domain = $this->getDomain();
        //此邮箱是否属于该企业
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('page404');
        }
        $mail   = Yii::$app->getRequest()->getBodyParam('mail');
        $passwd = Yii::$app->getRequest()->getBodyParam('password');
        //此邮箱是否已经注册
        $userinfo = QCompanyUser::mailCompanyStatus($mail,$company['id']);
        if(empty($userinfo)){
            return $this->Error('还没有注册','',1200);
        }else{
            if($userinfo['status'] == 3){
                return $this->Error('禁用了','',1204);
            }
            if($userinfo['companyid'] != $company['id']){
                return $this->Error('这个企业没有这人','',1201);
            }else{
                $result = UUser::login($userinfo['userid'],$passwd);
                if ($result) {
                    $info['userid'] = Yii::$app->user->getId();
                    $info['token']  = self::_createToken($info['userid']);
                    //添加到登录表
                    UUserLogin::addLogin($info['userid'],$info['token']);
                    return $this->Rsuccess('登录成功',$info);
                }else{
                    return $this->Error('登录失败','',1202);
                }
            }

        }
    }
    /**
     * 展示域名填写页
     */
    public function actionDomain(){
        return $this->renderPartial('domainVerify');
    }
    /**
     * 用户协议
     */
    public function actionPrivacy(){
        return $this->renderPartial('privacy');
    }
    /**
     * 用户协议
     */
    public function actionUseterms(){
        return $this->renderPartial('useTerms');
    }

}
