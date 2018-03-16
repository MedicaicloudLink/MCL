<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "u_user_info".
 *
 * @property string $s_userid
 * @property string $s_username
 * @property string $s_mail
 * @property integer $s_mail_access
 * @property string $s_cellphone
 * @property integer $s_cellphone_access
 * @property string $s_address
 * @property integer $s_address_access
 * @property string $s_department
 * @property string $s_job
 * @property integer $s_sex
 * @property string $s_avatar
 * @property string $s_mybirthday
 * @property string $s_competent
 * @property integer $s_competent_access
 * @property string $s_subordinate
 * @property integer $s_subordinate_access
 * @property string $s_update_time
 */
class UUserInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_user_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['s_userid'], 'required'],
            [['s_mail_access', 's_cellphone_access', 's_address_access', 's_sex', 's_competent_access', 's_subordinate_access'], 'integer'],
            [['s_mybirthday', 's_update_time'], 'safe'],
            [['s_subordinate'], 'string'],
            [['s_userid'], 'string', 'max' => 11],
            [['s_username', 's_cellphone', 's_competent'], 'string', 'max' => 32],
            [['s_mail', 's_address', 's_department', 's_job', 's_avatar'], 'string', 'max' => 255],
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
            's_mail' => 'S Mail',
            's_mail_access' => 'S Mail Access',
            's_cellphone' => 'S Cellphone',
            's_cellphone_access' => 'S Cellphone Access',
            's_address' => 'S Address',
            's_address_access' => 'S Address Access',
            's_department' => 'S Department',
            's_job' => 'S Job',
            's_sex' => 'S Sex',
            's_avatar' => 'S Avatar',
            's_mybirthday' => 'S Mybirthday',
            's_competent' => 'S Competent',
            's_competent_access' => 'S Competent Access',
            's_subordinate' => 'S Subordinate',
            's_subordinate_access' => 'S Subordinate Access',
            's_update_time' => 'S Update Time',
        ];
    }
    /**
     * 关联企业用户表
     */
    public function getQCompanyUser(){
        return $this->hasMany(QCompanyUser::className(), ['userid'  => 's_userid']);
    }
    /**
     * @todo 用户信息
     */
    public static function userInfo($userid){
        $result = UUserInfo::find()
                ->where(['s_userid'=>$userid])
                ->asarray()
                ->one();
        return $result;
    }
    /**
     * @todo 创建用户
     */
    public static function createUser($userid,$data){
        if(empty(UUserInfo::userInfo($userid))){
            $model = new UUserInfo();
            $model -> s_userid      = $userid;
            $model -> s_mail        = $data['mail'];
            $model -> s_username    = $data['username'];
            $model -> s_cellphone   = isset($data['mobile']) ? $data['mobile'] : '';
            $model -> s_department  = isset($data['department']) ? $data['department'] : '';
            $model -> s_update_time = date('Y-m-d H:i:s');
            if($model -> save()){
                return true;
            }else{
                return false;
            }
        }else{
            UUserInfo::updateAll(['s_username'=>$data['username'],'s_update_time'=>date('Y-m-d H:i:s')],['s_userid'=>$userid]);
            return true;
        }
    }
    /**
     * 搜索用户
     * @param search
     * @param $companyid
     * @return array
     */
    public static function searchUser($search,$companyid){
        $result = UUserInfo::find()
                ->joinWith('qCompanyUser')
                ->select('s_userid,s_username,s_avatar,s_mail')
                ->where(['like','s_username',$search])
                ->andWhere(['status'=>2,'companyid'=>$companyid])
                ->limit(7)
                ->asArray()
                ->all();
        foreach($result as $k=>$v){
            unset($result[$k]['qCompanyUser']);
        }
        return $result;
    }
    /**
     * 修改用户基本信息
     *
     */
    public static function editUserinfo($userid,$typeval){
        $type = [
                    's_username',
                    's_mail_access',
                    's_cellphone',
                    's_cellphone_access',
                    's_address',
                    's_address_access',
                    's_department',
                    's_job',
                    's_competent',
                    's_subordinate',
                    's_sex',
                    's_mybirthday',
                    's_avatar'
            ];
        $arr = json_decode($typeval,true);
        if(!empty($arr)){
            foreach($arr as $k=>$v){
                if(!in_array($k,$type)){
                    return false;
                }else{
                    if($k == 's_competent' || $k == 's_subordinate'){
                        $v = json_encode($v);
                    }
                    UUserInfo::updateAll([$k=>$v,'s_update_time'=>date('Y-m-d H:i:s')],['s_userid'=>$userid]);
                }
            }
        }
        return true;
    }
	/**
	 * 查看别人基本信息
     * @param $userid
     * @param $touserid
     * @return array
	 */
	public static function toUserinfo($userid,$touserid){
        $result = UUserInfo::userInfo($touserid);
        //是否为联系人
        $contact = ContactPerson::contactInfo($userid,$touserid);
        if(empty($contact)){
            $result['iscontact'] = 2;
        }else{
            $result['iscontact'] = 1;
        }
        if($userid == $touserid){
            return $result;
        }
		//用户和被访问者的关系
		#用户所在企业
		$usercompany   = QCompanyUser::userInfo($userid);
		#被访问者所在企业
		$tousercompany = QCompanyUser::userInfo($touserid);
		#被访问用户所在项目
		$touserproject = PProjectUser::userProject($touserid);
		#访问者所在项目
        $userproject   = PProjectUser::userProject($userid);
		if($usercompany['companyid'] == $tousercompany['companyid']){
		    $ctype = 1;#是同一个企业
        }else{
		    $ctype = 2;#不是同一个企业
        }
        if(empty($touserproject)){
		    $ptype = 3;#没有共同的项目
        }
        if(empty($userproject)){
            $ptype = 3;#没有共的项目
        }
        if(!empty($touserproject) && !empty($userproject)){
            $pubpid = array_intersect($touserproject,$userproject);
            if(empty($pubpid)){
                $ptype = 3;#没有公共的项目
            }else{
                $ptype = 4;#有公共的项目
            }
        }
        if($ctype == 1){
            return $result;
        }
        //不是企业内部成员
        unset($result['s_competent']);
        unset($result['s_subordinate']);
		if($ctype != 1){
            if($result['s_mail_access'] == 1){
                unset($result['s_mail']);
                //unset($result['s_mail_access']);
            }
            if($result['s_cellphone_access'] == 1){
                unset($result['s_cellphone']);
                //unset($result['s_cellphone_access']);
            }
            if($result['s_address_access'] == 1){
                unset($result['s_address']);
                //unset($result['s_address_access']);
            }
        }
        //有公共项目
        if($ptype == 4){
            if($result['s_mail_access'] == 1){
                unset($result['s_mail']);
                //unset($result['s_mail_access']);
            }
            if($result['s_cellphone_access'] == 1){
                unset($result['s_cellphone']);
                //unset($result['s_cellphone_access']);
            }
            if($result['s_address_access'] == 1){
                unset($result['s_address']);
                //unset($result['s_address_access']);
            }
        }
        //没有公共项目
        if($ptype == 3){
            if($result['s_mail_access'] == 2){
                unset($result['s_mail']);
                //unset($result['s_mail_access']);
            }
            if($result['s_cellphone_access'] == 2){
                unset($result['s_cellphone']);
                //unset($result['s_cellphone_access']);
            }
            if($result['s_address_access'] == 2){
                unset($result['s_address']);
                //unset($result['s_address_access']);
            }
        }
        return $result;
	}
}
