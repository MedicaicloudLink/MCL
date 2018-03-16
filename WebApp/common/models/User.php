<?php 
namespace common\models;

class User extends /*\yii\base\Object*/ \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public $authKey;
    public $accessToken;
    /*public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;

    private static $users = [
        '100' => [
            'id' => '100',
            'username' => 'admin',
            'password' => 'admin',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        '101' => [
            'id' => '101',
            'username' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
    ];
*/

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['s_userid', 's_userpassword'], 'required'],
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
            's_update_time' => 'S Update Time',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
        //return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
        /*foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;*/
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
          $user = User::find()
            //->where(['s_username' => $username])
            ->where(['s_userid'=>$username])
            ->asArray()
            ->one();

            if($user){
            return new static($user);
        }

        return null;
        /*foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;*/
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->s_userid;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->s_userpassword === md5(md5($password));
    }
}