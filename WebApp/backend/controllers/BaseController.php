<?php

namespace backend\controllers;

header("Content-type:text/html;charset=utf-8");

use Yii;
use yii\web\Controller;
use yii\log\FileTarget;
use yii\Org\Aes;
use app\models\UUserLogin;
/**
 * 基类
 * @author 梅地卡尔医疗
 *
 */
class BaseController extends Controller {
    /**
     * 默认action每次运行功能都会执行
     * 请求日志
     * 判断用户是否登录
     */
    public $enableCsrfValidation = false;

    /* header中解析出的userId */
    protected $userId = "";

    public function beforeAction($action) {
        $params           = Yii::$app->getRequest()->getBodyParams();
        $paramsstr        = '';
        if(!empty($params)){
            foreach($params as $k=>$v){
                $paramsstr.=$k.'='.$v.'&';
            }
            $paramsstr    = trim($paramsstr, '&');
        }
        
        $time             = microtime(true);
        $log              = new FileTarget();
        $filename         = date('Y-m-d').'.log';
        $log->logFile     = Yii::$app->getRuntimePath() . '/logs/'.$filename; //文件名自定义
        $content          = 'Request-url:'.Yii::$app->request->hostInfo.Yii::$app->request->getUrl().";Request-params：".$paramsstr.';';
        $log->messages[]  = [$content,1,'application',$time];
        $log->export();
//         //判断是否登录
//         if (Yii::$app->user->isGuest && ($action->id != 'login') && ($action->id != 'a')) {
//             //return $this->redirect(['login/a'])->send();
//             $this->Error('请先登录');
//         }
        $actionarr       = ['exportcontact','recordnotify','getstatus','exportpatient','bind','privacy','useterms','registcompany','domain','companymail','home','downfile','displayforgetpwuppwd','forgetpassword','loginform','registerpersonal','invitefriends','resetpw','domainstate','setpasswd','emaillogin','forgetpasswd','sendforgetpasswd','forgetpasswdmail','createuser','sendregistmail','registercompany','company','loginsendmail','loginmailinput','invitewoker','createcompany','mailstate'];
        foreach($actionarr as $k=>$v){
            if($action->id == $v){
                return true;
            }
        }
        if(parent::beforeAction($action)){
            $header         = Yii::$app->getRequest()->getHeaders();
            $authinfo       = self::_checkToken($header['token']);
            $this->userId = $authinfo["userid"];
            return true;
        }else{
            return false;
        }
        
    }
    /**
     * 身份验证
     * @param userid
     * @return string
     */
    public function checkAuth($userid) {
        if($this->userId != $userid){
            echo json_encode(['code' => 'err', 'message' => '请正确登录']);exit;
        }
        $header  = Yii::$app->getRequest()->getHeaders();
        $userarr = UUserLogin::userLogin($userid,$header['token']);
        if(empty($userarr)){
            echo json_encode(['code' => 'err', 'message' => '你被挤下线了']);exit;
        }
        return true;
    }
    /**
     * AES加密
     * @param string $str 加密的串
     * @return string
     */
    protected static function aesEncode($str) {
        $aes = new Aes(Yii::$app->params['AES_KEY']);
        return $aes -> encrypt($str);
    }
    /**
     * AES解密
     * @param $str(解密的串)
     * @return string
     */
    protected static function aesDecode($str) {
        $aes = new Aes(Yii::$app->params['AES_KEY']);
        return $aes -> decrypt($str);
    }
    
    /**
     * 生成Token
     * @param $userId
     * @return mixed
     */
    protected static function _createToken($userId) {
        return self::aesEncode($userId . '_' . time() . '_' . Yii::$app->params['TOKEN_DISTURB_CODE']);
    }
    
    /**
     * 验证token合法性
     * @param $token
     * @return mixed
     */
    protected static function _checkToken($token) {
        $accessToken = urldecode($token);
        if (empty($accessToken)) {
            echo json_encode(['code' => 'err', 'message' => '请登录']);exit;
        }
        $auth = self::analysisToken($accessToken);
        if ('-2' == $auth) {
            //验证码错误
            echo json_encode(['code' => 'err', 'message' => '请登录']);exit;
        }
        if ('-1' == $auth) {
            //超过一周未登录，token过期
            echo json_encode(['code' => 'err', 'message' => '请登录']);exit;
        }
        
        return $auth;
    }
    
    /**
     * 分析token
     * @param $token
     * @return array|int|null
     */
    protected static function analysisToken($token) {
        //解密
        $str    = self::aesDecode($token);
        $arr    = explode('_', $str);
        if(count($arr) > 2){
            $arrstr = explode('$',$arr[2]);
            $arrstr = $arrstr[0].'$';
        }
        //验证码错误
        if (count($arr) < 3 || Yii::$app->params['TOKEN_DISTURB_CODE'] !== $arrstr) {
            return -2;
        }
        //验证码过期，一周失效
        if ($arr[1] < time() - Yii::$app->params['TOKEN_EXPIRES_IN']) {
            return -1;
        }
        //24小时续期
        //println('当前服务器时间：'.$this->time,false);
        //println('时间差：'$this->time-$arr[1],false);
        //print_r(C('TOKEN_REFRESH_TIME'));die;
        if (time() - $arr[1] > Yii::$app->params['TOKEN_REFRESH_TIME']) {
            return array('userid' => $arr[0], 'mktime' => $arr[1], 'refresh_token' => self::_createToken($arr[0]));
        }
        return array('userid' => $arr[0], 'mktime' => $arr[1]);
    }
    /**
     * @todo 成功
     */
    public function Rsuccess($mess = '', $data = array(),$code = '') {
        if (!$data) {
            $data = array();
        }
        $data = array('code' => 'succ', 'message' => $mess, 'data' => $data,'status'=>$code);
        $data = json_encode($data);
        $data = str_replace('null', '""', $data);
        echo $data;
        exit ;
    }
    /**
     * @todo 失败
     */
    public function Error($mess = '', $data = array(),$code = '') {
        if (!$data) {
            $data = array();
        }
        $data = array('code' => 'err', 'message' => $mess, 'data' => $data,'status'=>$code);
        $data = json_encode($data);
        $data = str_replace('null', '""', $data);
        echo $data;
        exit ;
    }
    /**
     * @todo 验证参数为空提示
     * @param 	array $param
     * @return	bool
     */
    protected static function checkParamIsEmpty(array $param,$status = '') {
        if (!empty($param) && is_array($param)) {
            foreach ($param as $key => $val) {
                if (!(Yii::$app->getRequest()->getBodyParam($key))) {#验证参数是否为空
                    echo json_encode(['code' => 'err', 'message' => $val,'status'=>$status]);exit;
                }
            }
        }
        return true;
    }

    /**
     * @todo 格式化打印数组
     * @param $array
     */
    public function fmtPrint_r($array) {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }
    /**
     * 获取域名
     */
    public function getDomain(){
        //获取域名信息
        $url     = Yii::$app->request->hostInfo;
        $urlinfo = parse_url($url);
        $urlarr  = explode('.', $urlinfo['host']);
        $urlarr  = array_reverse($urlarr);
        //二级域名
        if(!isset($urlarr[2])){
            $domain  = '';
        }else{
            $domain  = $urlarr[2];
        }
//         if(!isset($urlarr[3])){
//             $domain  = '';
//         }else{
//             $domain  = $urlarr[3];
//         }
        return $domain;
    }
}
