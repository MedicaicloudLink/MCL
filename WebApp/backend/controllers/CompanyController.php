<?php

namespace backend\controllers;

use Yii;
use app\models\EMail;
use app\models\QCompanyUser;
use app\models\QCompany;
use app\models\UUserInfo;
use app\models\Commonfun;
use app\models\UploadForm;
use yii\web\UploadedFile;
use \PHPExcel;
use \PHPExcel_IOFactory;
use app\models\UUserLogin;
class CompanyController extends BaseController
{
    /**
     * 点击链接进入创建企业页面
     * @param mailid
     * @param company_id
     * @return string
     */
    public function actionCompany(){
        $mailid       = Yii::$app->request->get('mailid');
        $company_id   = Yii::$app->request->get('company_id');
        $userid       = Yii::$app->request->get('userid');
        //连接有效性
        $mailinfo     = EMail::mail($mailid);
        if($mailinfo){
            $mail     = $mailinfo['to_mail'];
            $userinfo = QCompanyUser::userStatus($mail);
            //此邮箱是否已被注册  1.未激活，2.正常 3.禁用 4.删除
            if(!empty($userinfo) && $userinfo['status'] != 4 && $userinfo['status'] != 1){
                if($userinfo['status'] == 2){
                    #展示登录页
                    //邮箱是哪个企业的
                    $company     = QCompany::companyInfo($userinfo['companyid']);
                    //企业名称
                    $url         = $company['domain'].'.'.Yii::$app->params['domainurl'];
                    echo "<script>location.href='http://".$url."/login/loginform'</script>";
                }else{
                    echo "此账户被禁用";
                }
            }else{
                //没被注册
                //邀请链接进入
                if($company_id!=''){
                    $companyinfo = QCompany::companyInfo($company_id);
                    // "展示创建密码页，返回企业信息";
                    $data['companyid']   = $companyinfo['id'];
                    $data['companyname'] = $companyinfo['name'];
                    $data['mail']        = $mail;
                    $data['userid']      = $userid;
                    return $this->renderPartial('/login/registerPersonal',$data);
                }else{
                    //不是邀请链接进入
                    #展示创建企业页面
                    return $this->renderPartial('registerCompany',['mail'=>$mail]);
                }
            }
        }
        return $this->renderPartial('/login/page404');
    }
    /**
     *
     * 展示创建企业页
     */
    public function actionRegistcompany(){
        $mail = Yii::$app->request->get('mail');
        return $this->renderPartial('registerCompany',['mail'=>$mail]);
    }
    /**
     * 创建企业
     * @param mail
     * @param username
     * @param password
     * @param companyname
     * @param domain
     * @param city
     * @param mobile
     * @param department
     * @return string        1050-1100
     */
    public function actionCreatecompany()
    {
        self::checkParamIsEmpty(
                [
                    'mail'        => '邮箱不能为空',
                    'username'    => '用户名不能为空',
                    'password'    => '密码不能为空',
                    'companyname' => '企业名称不能为空',
                    'domain'      => '二级域名不能为空',
                    'city'        => '城市不能为空',
                    'mobile'      => '手机号不能为空',
                    'department'  => '部门不能为空',
                ],1000
        );
        $data['mail']         = Yii::$app->getRequest()->getBodyParam('mail');
        $data['username']     = Yii::$app->getRequest()->getBodyParam('username');
        $data['password']     = Yii::$app->getRequest()->getBodyParam('password');
        $data['companyname']  = Yii::$app->getRequest()->getBodyParam('companyname');
        $data['domain']       = Yii::$app->getRequest()->getBodyParam('domain');
        $data['city']         = Yii::$app->getRequest()->getBodyParam('city');
        $data['mobile']       = Yii::$app->getRequest()->getBodyParam('mobile');
        $data['department']   = Yii::$app->getRequest()->getBodyParam('department');
        $data['domain']       = strtolower($data['domain']);
        //此邮箱是否已被注册
        $userinfo = QCompanyUser::userStatus($data['mail']);
        if(!empty($userinfo) && $userinfo['status'] != 4 && $userinfo['status'] != 1){
            return $this->Error('此邮箱已被注册','',1050);
        }
        //验证手机号格式
        if (!preg_match('/^1[\d]{10}$/', $data['mobile'])) {
            return $this->Error('手机号格式不对','',1052);
        }
        //二级域名是否被占用
        if(!empty(QCompany::domainInfo($data['domain']))){
            return $this->Error('二级域名被占用','',1053);
        }
        if(in_array($data['domain'],Yii::$app->params['mydomain'])){
            return $this->Error('二级域名被占用','',1053);
        }
        $result = QCompany::createCompany($data);
        if($result){
            $result['token'] = self::_createToken(Yii::$app->user->getId());
            //添加到登录表
            UUserLogin::addLogin(Yii::$app->user->getId(),$result['token']);
            return $this->Rsuccess('创建成功',$result,1054);
        }else{
            return $this->Error('创建失败','',1055);
        }
    }
    /**
     * 邀请同事
     * @param userid
     * @param companyid
     * @param inviteinfo(json)
     * @param type 1:企业，2：普通同事
     * @return string
     */
    public function actionInvitewoker(){
        //$inviteinfo = '[{"name": "小黑","mail": "jiahui_huo@medicayun.cn"},{"name": "小白","mail": "937274669@qq.com"}]';
        self::checkParamIsEmpty(
                [
                    'userid'       => '用户id不能为空',
                    'companyid'    => '企业id不能为空',
                    'inviteinfo'   => '邀请信息不能为空',
                    'type'         => '类型不能为空',
                ],1000
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $companyid  = Yii::$app->getRequest()->getBodyParam('companyid');
        $inviteinfo = Yii::$app->getRequest()->getBodyParam('inviteinfo');
        $type       = Yii::$app->getRequest()->getBodyParam('type');
        $result     = QCompanyUser::createInvite($userid,$companyid,$inviteinfo,$type);
        if($result){
            $result['errcount'] = ($result['count']-$result['succcount']);
            return $this->Rsuccess('邀请成功',$result);
        }else{
            return $this->Error('邀请失败');
        }
    }
    /**
     * 重新发送邀请
     * @param userid
     * @param touserid
     * @param mail
     * @return string
     */
    public function actionReinvite(){
        //企业是否存在
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        self::checkParamIsEmpty(
            [
                'userid'       => '用户id不能为空',
                'touserid'     => '被邀请人id不能为空',
                'mail'         => '邮箱不能为空'
            ],1000
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $touserid   = Yii::$app->getRequest()->getBodyParam('touserid');
        $mail       = Yii::$app->getRequest()->getBodyParam('mail');
        $result     = QCompanyUser::reInvite($userid,$touserid,$company,$mail,1);
        if($result){
            return $this->Rsuccess('邀请成功');
        }else{
            return $this->Error('邀请失败');
        }
    }
    /**
     * 邀请同事excel
     * @param userid
     * @param companyid
     * @param UploadForm[file]
     * @param type 1:企业，2：普通同事
     * @return string
     */
    public function actionInvitewokerexcel(){
        self::checkParamIsEmpty(
            [
                'userid'       => '用户id不能为空',
                'companyid'    => '企业id不能为空',
                'type'         => '类型不能为空',
            ],1000
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $companyid  = Yii::$app->getRequest()->getBodyParam('companyid');
        $type       = Yii::$app->getRequest()->getBodyParam('type');
        $model      = new UploadForm();
        $uploadFile = UploadedFile::getInstances($model, 'file');
        if ($uploadFile == null || $uploadFile[0]->type!='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            return $this->Error('请上传excel');
        } else {
            $errnum       = 0;#没有发送邮箱的个数
            $excelFile    = Commonfun::upFile($uploadFile, 'invite-excel','invite');
            $phpexcel     = new PHPExcel;
            $excelReader  = PHPExcel_IOFactory::createReader('Excel2007');
            $phpexcel     = $excelReader->load($excelFile)->getSheet(0);//载入文件并获取第一个sheet
            $total_line   = $phpexcel->getHighestRow();
            for ($row = 2; $row <= $total_line; $row++) {
                if(trim($phpexcel->getCell('A'.$row) -> getValue() == '') || trim($phpexcel->getCell('B'.$row) -> getValue()) == ''){
                    $errnum++;
                }else{
                    if(Commonfun::isMail(trim($phpexcel->getCell('B'.$row) -> getValue()))){
                        $data[$row-2]['name']       = trim($phpexcel->getCell('A'.$row) -> getValue());
                        $data[$row-2]['mail']       = trim($phpexcel->getCell('B'.$row) -> getValue());
                        $data[$row-2]['phone']      = trim($phpexcel->getCell('C'.$row) -> getValue());
                        $data[$row-2]['department'] = trim($phpexcel->getCell('D'.$row) -> getValue());
                        $data[$row-2]['job']        = trim($phpexcel->getCell('E'.$row) -> getValue());
                    }else{
                        $errnum++;
                    }
                }
            }
            if(empty($data)){
                return $this->Error('请上传有效的excel');
            }
            $result = QCompanyUser::createInvite($userid, $companyid, $data, $type,'excel');
            if ($result) {
                $result['errcount'] = $errnum+($result['count']-$result['succcount']);
                $result['count']    = $result['count']+$errnum;
                return $this->Rsuccess('邀请成功', $result);
            } else {
                return $this->Error('邀请失败');
            }
        }
    }
    /**
     * 创建用户
     * @param companyid
     * @param mail
     * @param name
     * @param password
     * @param userid
     * @return string
     */
    public function actionCreateuser(){
        self::checkParamIsEmpty(
                [
                    'mail'       => '邮箱不能为空',
                    'companyid'  => '企业id不能为空',
                    'name'       => '姓名不能为空',
                    'password'   => '密码不能为空',
                    'userid'     => '用户id不能为空'
                ],1000
        );
        $data['mail']      = Yii::$app->getRequest()->getBodyParam('mail');
        $data['companyid'] = Yii::$app->getRequest()->getBodyParam('companyid');
        $data['username']  = Yii::$app->getRequest()->getBodyParam('name');
        $data['password']  = Yii::$app->getRequest()->getBodyParam('password');
        $data['userid']    = Yii::$app->getRequest()->getBodyParam('userid');
        //此邮箱是否已被注册
        $userinfo = QCompanyUser::userInfo($data['userid']);
        if(!empty($userinfo) && $userinfo['status'] != 4 && $userinfo['status'] != 1){
            return $this->Error('此邮箱已被注册','',1111);
        }
        $result = QCompanyUser::createUser2($data, 2);
        if($result){
            $result['token'] = self::_createToken(Yii::$app->user->getId());
            //添加到登录表
            UUserLogin::addLogin(Yii::$app->user->getId(),$result['token']);
            return $this->Rsuccess('用户信息：',$result);
        }else{
            return $this->Error('创建失败');
        }
    }
    /**
     * 二级域名是否已被注册
     * @param domain
     * @return array
     */
    public function actionDomainstate(){
        self::checkParamIsEmpty(
                [
                    'domain'       => '域名不能为空',
                ],1000
        );
        $domain = Yii::$app->getRequest()->getBodyParam('domain');
        $domain = strtolower($domain);
        //二级域名是否被占用
        if(!empty(QCompany::domainInfo($domain))){
            return $this->Error('二级域名被占用','',1053);
        }elseif(in_array($domain,Yii::$app->params['mydomain'])){
            return $this->Error('二级域名被占用','',1053);
        }else{
            return $this->Rsuccess('可以用');
        }
    }
    /**
     * 邀请朋友展示页
     * @param userid
     * @param type   1企业。2为个人
     * @return string
     */
    public function actionInvitefriends(){
        $userid  = Yii::$app->request->get('userid');
        $type    = Yii::$app->request->get('type');
        if($userid == '' || $type == ''){
            return $this->Error('用户或类型不能为空');
        }
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }else{
            $userinfo            = UUserInfo::userInfo($userid);
            if(empty($userinfo)){
                return $this->Error('用户不存在');
            }else{
                $data['username'] = $userinfo['s_username'];
            }
            $data['companyid']   = $company['id'];
            $data['companyname'] = $company['name'];
            $data['userid']      = $userid;
            $data['type']        = $type;
            return $this->renderPartial('inviteFriends',$data);
        }
    }
    /**
     * 企业信息
     * @param userid
     * @return string
     */
    public function actionCompanyinfo(){
        self::checkParamIsEmpty(
            [
                'userid' => '用户id不能为空'
            ]
        );
        $this->checkAuth(Yii::$app->getRequest()->getBodyParam('userid'));
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        return $this->Rsuccess('企业信息',$company);
    }
    /**
     * 编辑企业信息
     * @param userid
     * @param type_value {"name":'w'}
     */
    public function actionEditcompany(){
        self::checkParamIsEmpty(
            [
                'userid'        => '用户id不能为空',
                'type_value'    => '修改类型不能为空'
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        $typeval = Yii::$app->getRequest()->getBodyParam('type_value');
        $this->checkAuth($userid);
        //用户是否为企业的管理员
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        $isadmin = QCompanyUser::userPermission($userid,$company['id']);
        if($isadmin['permission'] != 1){
            return $this->Error('没有权限修改','','4011');
        }
        $result  = QCompany::editCompany($company['id'],$typeval);
        if($result){
            return $this->Rsuccess('编辑成功');
        }else{
            return $this->Error('类型键不对','','4010');
        }
    }
    /**
     * 验证企业内的邮箱
     * @param userid
     * @param mail
     * @return string
     */
    public function actionConfirmmail(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户id不能为空',
                'mail'      => '邮箱不能为空'
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        $mail    = Yii::$app->getRequest()->getBodyParam('mail');
        $this->checkAuth($userid);
        //企业信息
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        //用户是否为企业管理员
        $isadmin = QCompanyUser::userPermission($userid,$company['id']);
        if($isadmin['permission'] != 1){
            return $this->Error('没有权限','','4011');
        }
        //邮箱后缀名
        $mailarr = explode('@',$mail);
        $houzhui = $mailarr[1];
        //邮箱是否已经通过验证
        $finisharr     = json_decode($company['finish_mail'],true);
        $finish_mail   = [];
        if(!empty($finisharr)){
            foreach ($finisharr as $k=>$v){
                $finish_mail[] = $v['mail'];
            }
        }
        if(in_array($houzhui,$finish_mail)){
            return $this->Error('此域名已经通过验证','',4012);
        }
        //加入验证
        QCompany::addConfirmMail($mail,$houzhui,$company);
        return $this->Rsuccess('ok');
    }
    /**
     * 验证邮箱链接
     * @param mailid
     * @return string
     */
    public function actionCompanymail(){
        $mailid  = Yii::$app->request->get('mailid');
        //企业信息
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company) || $mailid == ''){
            return $this->renderPartial('/login/page404');
        }
        //连接有效性
        $mailinfo = EMail::mail($mailid);
        if(!$mailinfo){
            return $this->renderPartial('/login/page404');
        }else{
            //邮箱后缀名
            $mailarr = explode('@',$mailinfo['to_mail']);
            $houzhui = $mailarr[1];
            //邮箱是否已经通过验证
            $finisharr     = json_decode($company['finish_mail'],true);
            $finish_mail   = [];
            if(!empty($finisharr)){
                foreach ($finisharr as $k=>$v){
                    $finish_mail[] = $v['mail'];
                }
            }
            if(!in_array($houzhui,$finish_mail)){
                //添加到已完成验证的字段里
                QCompany::addFinishMail($houzhui,$company);
            }
            //已经在验证里
            //echo "已经验证完成";
            echo "<script>location.href='http://".$domain.".medicayun.com.cn/user/home#/manage/domain'</script>";exit;
        }
    }
    /**
     * 删除验证中邮箱域名
     * @param userid
     * @param mailsuffix
     * @return string
     */
    public function actionDelconfirm(){
        self::checkParamIsEmpty(
            [
                'userid'     => '用户id不能为空',
                'mailsuffix' => '邮箱不能为空'
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        $mail    = Yii::$app->getRequest()->getBodyParam('mailsuffix');
        $this->checkAuth($userid);
        //企业信息
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        //用户是否为企业管理员
        $isadmin = QCompanyUser::userPermission($userid,$company['id']);
        if($isadmin['permission'] != 1){
            return $this->Error('没有权限','','4011');
        }
        QCompany::delMail($mail,$company,1);
        return $this->Rsuccess('已经删除');
    }
    /**
     * 删除已经验证邮箱域名
     */
    public function actionDelfinish(){
        self::checkParamIsEmpty(
            [
                'userid'     => '用户id不能为空',
                'mailsuffix' => '邮箱不能为空'
            ]
        );
        $userid  = Yii::$app->getRequest()->getBodyParam('userid');
        $mail    = Yii::$app->getRequest()->getBodyParam('mailsuffix');
        $this->checkAuth($userid);
        //企业信息
        $domain  = $this->getDomain();
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->renderPartial('/login/page404');
        }
        //用户是否为企业管理员
        $isadmin = QCompanyUser::userPermission($userid,$company['id']);
        if($isadmin['permission'] != 1){
            return $this->Error('没有权限','','4011');
        }
        QCompany::delMail($mail,$company,2);
        return $this->Rsuccess('已经删除');
    }
    /**
     * 二级域名是否可用
     * @parma domain
     * @return string
     */
    public function actionDomain(){
        self::checkParamIsEmpty(
            [
                'domain'  => '二级域名不能为空'
            ]
        );
        $domain  = Yii::$app->getRequest()->getBodyParam('domain');
        $domain  = strtolower($domain);
        //企业信息
        $company = QCompany::domainInfo($domain);
        if(empty($company)){
            return $this->Error('不存在');
        }else{
            return $this->Rsuccess('企业信息',$company);
        }
    }
}
?>