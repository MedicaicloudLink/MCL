<?php

namespace app\models;

use Yii;
use app\models\USendmess;
use app\models\UploadForm;
use yii\web\UploadedFile;
use app\models\CUserlinkman;
use app\models\IInvitedata;
use app\models\CUserlinkLog;
use app\models\UMessage;
use common\models\LoginForm;
/**
 * This is the model class for table "m_userinfo".
 *
 * @property integer $s_userid
 * @property string $s_username
 * @property string $s_userpassword
 * @property string $s_formalname
 * @property string $s_userEmail
 * @property string $s_cellphone
 * @property integer $s_sex
 * @property string $s_mybirthday
 * @property string $s_mem
 * @property string $s_highschool
 * @property string $s_startdate
 * @property string $s_enddate
 * @property string $s_education
 * @property string $s_addschool
 * @property string $s_workunti
 * @property string $u_hospital_code
 * @property string $s_department
 * @property string $s_work_startdate
 * @property string $s_work_enddate
 * @property string $s_job
 * @property string $s_joblevel
 * @property string $s_department_mem
 * @property string $s_Publish_worksname
 * @property string $s_publish_date
 * @property string $s_publish_unti
 * @property string $s_worklink
 * @property string $s_newwork
 * @property string $s_Expertise
 * @property string $s_other_table
 * @property string $s_update_time
 */
class MUserinfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'm_userinfo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['s_userid', 's_userpassword'], 'required'],
            [['s_sex','company_role'], 'integer'],
            [['s_mybirthday', 's_startdate', 's_enddate', 's_work_startdate', 's_work_enddate', 's_update_time'], 'safe'],
            [['s_mem', 's_department_mem','company_id'], 'string'],
            [['s_workphone','s_avatar','s_address','s_username', 's_userpassword', 's_formalname', 's_userEmail', 's_cellphone', 's_highschool', 's_education', 's_addschool', 's_workunti', 's_department', 's_job', 's_joblevel', 's_Publish_worksname', 's_publish_date', 's_publish_unti', 's_worklink', 's_newwork', 's_Expertise'], 'string', 'max' => 255],
            [['u_hospital_code'], 'string', 'max' => 64],
            [['s_other_table'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            's_userid' => 'S Userid',
            's_username' => 'S Username',
            's_userpassword' => 'S Userpassword',
            's_formalname' => 'S Formalname',
            's_userEmail' => 'S User Email',
            's_cellphone' => 'S Cellphone',
            's_sex' => 'S Sex',
            's_mybirthday' => 'S Mybirthday',
            's_mem' => 'S Mem',
            's_highschool' => 'S Highschool',
            's_startdate' => 'S Startdate',
            's_enddate' => 'S Enddate',
            's_education' => 'S Education',
            's_addschool' => 'S Addschool',
            's_workunti' => 'S Workunti',
            'u_hospital_code' => 'U Hospital Code',
            's_department' => 'S Department',
            's_work_startdate' => 'S Work Startdate',
            's_work_enddate' => 'S Work Enddate',
            's_job' => 'S Job',
            's_joblevel' => 'S Joblevel',
            's_department_mem' => 'S Department Mem',
            's_Publish_worksname' => 'S  Publish Worksname',
            's_publish_date' => 'S Publish Date',
            's_publish_unti' => 'S Publish Unti',
            's_worklink' => 'S Worklink',
            's_newwork' => 'S Newwork',
            's_Expertise' => 'S  Expertise',
            's_other_table' => 'S Other Table',
            's_update_time' => 'S Update Tim',
        ];
    }
    /**
     * @todo userid获取用户信息
     * @param userid
     */
    public static function userInfo($userid){
        $result = MUserinfo::find()
        ->where(['s_userid'=>$userid])
        ->asarray()
        ->all();
        if(!empty($result)){
            unset($result[0]['s_userpassword']);
        }
        return $result;
    }
    /**
     * @todo 检查用户ID是否存在
     */
    public static function findUserIdIstrue($userId)
    {
        if (MUserinfo::findOne(["s_userid" => $userId])) {
            return true;
        }
        return false;
    }
    
    /**
     * @todo 通过姓名或手机号查找用户信息
     * @param $name
     */
    public static function findUserIdByNameOrTel($nameOrTel)
    {
        $userInfo = MUserinfo::find()
            ->select("s_userid, s_username, s_sex, s_workunti, s_department, s_mem,s_mybirthday,s_cellphone")
//             ->where(['like',"s_username" ,$nameOrTel])
//             ->orWhere(['like','s_cellphone', $nameOrTel])
            ->where(['s_cellphone'=>$nameOrTel])
            ->asArray()
            ->all();

        return $userInfo;
    }

    /**
     * @todo 通过手机号查找用户信息
     * @param $tel
     */
    public static function findUserIdByTel($tel)
    {
        $userInfo = MUserinfo::find()
            ->select("s_userid,s_username,s_sex,s_workunti,s_department,s_mem,s_cellphone")
            ->where(["s_cellphone" => $tel])
            ->asArray()
            ->all();

        return $userInfo;
    }
    /**
     * @todo 通过手机号查找用户信息
     * @param $tel
     */
    public static function isUser($name)
    {
        $userInfo = MUserinfo::find()
                ->select("s_userid as touserid,s_workunti,s_username,s_cellphone")
                ->where(["s_cellphone" => $name])
                ->orwhere(["s_username" => $name])
                ->asArray()
                ->all();
    
        return $userInfo;
    }
    /**
     * @todo 编辑个人资料
     */
    public static function editUserInfo(array $array)
    {
        $userInfo = MUserinfo::findOne(["s_userid" => $array["s_userid"]]);

        if ($userInfo != null) {
            foreach ($array as $key => $val) {
                $userInfo -> $key = $val;
            }
            if ($userInfo->save()) {
                return true;
            }
            /*$userInfo -> s_username             = $array["s_username"];         // 姓名
            $userInfo -> s_userEmail            = $array["s_userEmail"];        // Email(用于验证)
            $userInfo -> s_cellphone            = $array["s_cellphone"];        // 用户电话号码
            $userInfo -> s_sex                  = $array["s_sex"];              // 性别  1：男 2：女
            $userInfo -> s_mybirthday           = $array["s_mybirthday"];       // 出生年月
            $userInfo -> s_mem                  = $array["s_mem"];              // 自我描述
            $userInfo -> s_highschool           = $array["s_highschool"];       // 毕业院校
            $userInfo -> s_startdate            = $array["s_startdate"];        // 开始时间
            $userInfo -> s_enddate              = $array["s_enddate"];          // 结束时间
            $userInfo -> s_education            = $array["s_education"];        // 学历
            $userInfo -> s_addschool            = $array["s_addschool"];        // 添加院校
            $userInfo -> s_workunti             = $array["s_workunti"];         // 工作单位
            $userInfo -> u_hospital_code        = $array["u_hospital_code"];    // 医院ID
            $userInfo -> s_department           = $array["s_department"];       // 工作科室
            $userInfo -> s_work_startdate       = $array["s_work_startdate"];   // 开始时间
            $userInfo -> s_work_enddate         = $array["s_work_enddate"];     // 结束时间
            $userInfo -> s_job                  = $array["s_job"];              // 职务
            $userInfo -> s_joblevel             = $array["s_joblevel"];         // 职称
            $userInfo -> s_department_mem       = $array["s_department_mem"];   // 专业经历
            $userInfo -> s_Publish_worksname    = $array["s_Publish_worksname"];// 作品名称
            $userInfo -> s_publish_date         = $array["s_publish_date"];     // 发表时间
            $userInfo -> s_publish_unti         = $array["s_publish_unti"];     // 发表单位
            $userInfo -> s_worklink             = $array["s_worklink"];         // 作品链接
            $userInfo -> s_newwork              = $array["s_newwork"];          // 添加新作品
            $userInfo -> s_Expertise            = $array["s_Expertise"];        // 擅长领域
            $userInfo -> s_other_table          = $array["s_other_table"];      // 其他诊断的表名
            $userInfo -> s_update_time          = $array["s_update_time"];      // 最后一次更新的时间*/
        }
        return false;
    }

    /**
     * @todo 注册
     * 
     */
    public static function register($userid){
        $mobile    = Yii::$app->getRequest()->getBodyParam('mobile');
        $code      = Yii::$app->getRequest()->getBodyParam('code');
        $password  = Yii::$app->getRequest()->getBodyParam('password');
        if (!preg_match('/^1[\d]{10}$/', $mobile)) {
            return 'mobileerror';
        }
        $checkuser = MUserinfo::find()
         -> where(['s_cellphone' => $mobile]) 
         -> all();
        if (!empty($checkuser)) {
            return 'mobileexist'; #手机号已存在
        }
        $re = USendmess::checkCode($mobile,$code);
        
        if($re == 'err' || $re == 'nouse'){
            return 'err';         #验证码错误
        }
        $model = new MUserinfo();
        //$model -> s_userid       = MUserinfo::maxRecordid();
        $model -> s_userid       = $userid;
        $model -> s_cellphone    = $mobile;
        $model -> s_userpassword = md5(md5($password));
        $model -> s_update_time  = date('Y-m-d H:i:s');
        if($model->save()){
            //查看是否是被邀请过的
            $inviteinfo = IInvitedata::isMobileInvite($mobile);
            if(!empty($inviteinfo)){
                foreach($inviteinfo as $key=>$val){
                    #好友申请记录，申请通知
                    CUserlinkLog::updateLinkLog($val['userid'], $userid);
                    #回馈邀请的表
                    IInvitedata::updateAll(['status'=>2,'registtime'=>date('Y-m-d H:i:s')],['id'=>$val['id']]);
                }
            }
            return $model -> s_userid;
        }else{
            return 'nosucc';
        }
    }
    /**
     * @todo 最大的记录id
     * return   string
     */
    public static function maxRecordid(){
        $arr = MUserinfo::find()
        ->select('s_userid')
        ->orderBy(['(s_userid+0)'=>SORT_DESC])  #field+0 s_userid为varchar类型。默认为字典排序
        ->limit(1)
        ->asarray()
        ->all();
        if(empty($arr) || $arr[0]['s_userid']==''){
            $record = 1;
        }else{
            $record = $arr[0]['s_userid']+1;
        }
        return $record;
    }
    /**
     * @todo 完善用户信息
     */
    public static function addUserinfo($userid){
        $model = MUserinfo::findOne(['s_userid'=>$userid]);
        $model -> s_username    =   Yii::$app->getRequest()->getBodyParam('s_username');
        $model -> s_sex         =   Yii::$app->getRequest()->getBodyParam('s_sex');
        $model -> s_workunti    =   Yii::$app->getRequest()->getBodyParam('s_workunti');
        $model -> s_department  =   Yii::$app->getRequest()->getBodyParam('s_department');
        $model -> s_joblevel    =   Yii::$app->getRequest()->getBodyParam('s_joblevel');
        $model -> s_update_time =   date('Y-m-d H:i:s');
        if($model->save()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 上传照片
     */
    public static function upPicture($uploadFile){
        $filename  = explode(".", $uploadFile->name);
        $data      = date("Y", time()) . '/' . date("m", time()) . '/' . date("d", time()) . '/';
        $save_path = Yii::$app->basePath.'/uploads/'.$data;
        //创建相应了、目录
        if (!file_exists($save_path)) {
            mkdir($save_path, 0777, true);
        }
        $namef         = md5(uniqid(rand(), true)) . '.' . end($filename);
        $savePath      = $save_path . $namef;
        $uploadFile->saveAs($savePath);
        $url = MUserinfo::upUfile($savePath,$namef);
        return $url;
    }
    /**
     * @todo 上传文件到ufile
     * @author hjh
     */
    public static function upUfile($dir,$namef){
        $path = Yii::$app->basePath.'/../';
        require_once("".$path."vendor/uclouds/ucloud/proxy.php");
        //存储空间名
        $bucket = Yii::$app->params['bucket'];
        //上传至存储空间后的文件名称
        $key    = time().$namef;
        //待上传文件的本地路径
        $file   = $dir;
        //该接口适用于0-10MB小文件,更大的文件建议使用分片上传接口
        list($data, $err) = UCloud_PutFile($bucket, $key, $file);
        if ($err) {
            return false;
        }else{
            $url = UCloud_MakePrivateUrl($bucket, $key);
            return $url;
        }
    }
    /**
     * @todo 查询手机号
     * @param userid
     */
    public static function findCellphone($userid){
        $result = MUserinfo::find()
        ->select('s_cellphone')
        ->where(['s_userid'=>$userid])
        ->asarray()
        ->all();
        return $result[0]['s_cellphone'];
    }
    /**
     * @todo 修改用户密码
     * 
     */
    public static function upPassword(){
        $userid      = Yii::$app->getRequest()->getBodyParam('userid');
        $oldpassword = Yii::$app->getRequest()->getBodyParam('oldpassword');
        $newpassword = Yii::$app->getRequest()->getBodyParam('newpassword');
        $repassword  = Yii::$app->getRequest()->getBodyParam('repassword');
        //判断旧密码是否正确
        $useroldinfo = MUserinfo::find()
                     -> select('s_userpassword')
                     -> where(['s_userid'=>$userid])
                     -> one();
        //正确的原密码
        $password    = $useroldinfo -> s_userpassword;
        //判断输入值是否与原密码一致
        if($password != md5(md5($oldpassword))){
            return 'olderror';
        }
        if($newpassword != $repassword){
            return 'reerror';
        }
        if(MUserinfo::updateAll(['s_userpassword' => md5(md5($newpassword))], ['s_userid'=>$userid])){
            return 'succ';
        }
        
    }
    /**
     * @todo 忘记密码
     */
    public static function forgetpwd($mobile,$newpwd){
        if(MUserinfo::updateAll(['s_userpassword' => md5(md5($newpwd)),'s_update_time'=>date('Y-m-d H:i:s')], ['s_cellphone'=>$mobile])){
            return true;
        }
        return false;
    }
    /**
     * @todo 通过手机号查找用户信息
     * @param $mobile
     */
    public static function findUserByTel($userid,$mobile){
        $userInfo   = MUserinfo::find()
                    ->select("s_userid,s_username,s_workunti,s_cellphone")
                    ->where(["s_cellphone" => $mobile])
                    ->asArray()
                    ->one();
        if(!empty($userInfo)){
            if($userInfo['s_userid'] == $userid){
                $userInfo['type'] = 3;#这是你自己
            }else{
                //是否为好友
                $iscontact = CUserlinkman::isContactman($userid, $userInfo['s_userid']);
                if(!empty($iscontact)){
                    $userInfo['type'] = 1;#已经是好友
                }else{
                    $userInfo['type'] = 2;#不是好友
                }
            }
        }else{
            $userInfo['type'] = 4;#不是系统用户
        }
        return $userInfo;
    }
    /**
     * @todo 联系人详情
     */
    public static function contactUserInfo($userid,$touserid){
        $userinfo = MUserinfo::userInfo($touserid);
        foreach($userinfo as $key=>$val){
            $userarr['s_userid']     = $val['s_userid'];
            $userarr['s_username']   = $val['s_username'];
            $userarr['s_workunti']   = $val['s_workunti'];
            $userarr['s_sex']        = $val['s_sex'];
            $userarr['s_mybirthday'] = $val['s_mybirthday'];
            $userarr['s_department'] = $val['s_department'];
            $userarr['s_joblevel']   = $val['s_joblevel'];
            $userarr['s_job']        = $val['s_job'];
            $userarr['s_cellphone']  = $val['s_cellphone'];
            $userarr['s_userEmail']  = $val['s_userEmail'];
            $userarr['s_avatar']     = $val['s_avatar'];
            $projectinfo             = [];
            //参加的项目
            $userprojectinfo         = UUserTask::userProjects($userid);
            $touseridprojectinfo     = UUserTask::userProjects($touserid);
            if(!empty($userprojectinfo) && !empty($touseridprojectinfo)){
                foreach($touseridprojectinfo['result'] as $k=>$v){
                    if(!in_array($k,$userprojectinfo['projectid'])){
                        unset($touseridprojectinfo['result'][$k]);
                    }
                    unset($touseridprojectinfo['projectid']);
                }
                $projectinfo         = array_values($touseridprojectinfo['result']);
            }
            $userarr['project']      = $projectinfo;
        }
        return $userarr;
    }
    /**
     * @todo 添加项目成员时的搜索
     */
    public static function searchProjectUser($userid,$projectid,$mobile){
        $userInfo   = MUserinfo::find()
                    ->select("s_userid,s_username,s_sex,s_workunti,s_cellphone,s_department")
                    ->where(["s_cellphone" => $mobile])
                    ->asArray()
                    ->one();
        if(!empty($userInfo)){
            //是否已经为项目成员
            $projectuser = UUserProject::userInProjectIsExist($userInfo['s_userid'], $projectid);
            if($projectuser){
                $userInfo['type'] = 5;#已经是项目成员
            }else{
                if($userInfo['s_userid'] == $userid){
                    $userInfo['type'] = 3;#这是你自己
                }else{
                    //是否为好友
                    $iscontact = CUserlinkman::isContactman($userid, $userInfo['s_userid']);
                    if(!empty($iscontact)){
                        $userInfo['type'] = 1;#已经是好友
                    }else{
                        $userInfo['type'] = 2;#不是好友
                    }
                }
            }
        }else{
            $userInfo['type'] = 4;#不是系统用户
        }
        return $userInfo;
    }
    /**
     * @todo 邮箱是否存在
     */
    public static function mailInfo($mail){
        $mail = MUserinfo::find()
              ->select('s_userid,company_id')
              ->where(['s_userEmail'=>$mail])
              ->one();
        return $mail;
    }
    /**
     * @todo 创建企业用户（新）
     */
    public static function createUser($userid,$company_id,$data,$role){
        $model = new MUserinfo();
        $model -> s_userid        = $userid;
        $model -> s_username      = $data['username'];
        $model -> s_userEmail     = $data['mail'];
        $model -> s_cellphone     = $data['mobile'];
        $model -> s_department    = $data['department'];
        $model -> s_userpassword  = md5(md5($data['password']));
        $model -> s_update_time   = date('Y-m-d H:i:s');
        $model -> company_id      = $company_id;
        $model -> company_role    = $role;
        if($model->save()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 创建用户（新）
     */
    public static function createUser2($data,$role){
        $model = new MUserinfo();
        $model -> s_userid        = Commonfun::randpw();
        $model -> s_username      = $data['username'];
        $model -> s_userEmail     = $data['mail'];
        $model -> s_userpassword  = md5(md5($data['password']));
        $model -> s_update_time   = date('Y-m-d H:i:s');
        $model -> company_id      = $data['companyid'];
        $model -> company_role    = $role;
        if($model->save()){
            //登录
            $info['token']     = MUserinfo::login($data['username'], $data['password'])['token'];
            $info['mail']      = $data['mail'];
            $info['name']      = $data['username'];
            $info['companyid'] = $data['companyid'];
            $info['userid']    = $model->s_userid;
            $info['type']      = 2;//个人用户
            return $info;
        }else{
            return false;
        }
    }
    /**
     * @todo 重置密码 根据邮箱
     */
    public static function setPasswd($mail,$passwd){
        if(MUserinfo::updateAll(['s_userpassword' => md5(md5($passwd)),'s_update_time'=>date('Y-m-d H:i:s')], ['s_userEmail'=>$mail])){
            return true;
        }
        return false;
    }
    /**
     * @todo 登录
     */
    public static function login($username,$passwd){
        $model = new LoginForm();
        $model -> username = $username;
        $model -> password = $passwd;
        if ($model->login()) {
            $info['userid'] = Yii::$app->user->getId();
            $info['token']  = self::_createToken($info['userid']);
            return info;
        }else{
            return false;
        }
    }
}