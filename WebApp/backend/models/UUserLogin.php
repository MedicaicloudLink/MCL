<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "u_user_login".
 *
 * @property integer $id
 * @property string $userid
 * @property string $token
 * @property string $createtime
 */
class UUserLogin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_user_login';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['createtime'], 'safe'],
            [['token', 'userid'], 'string', 'max' => 255],
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
            'token' => 'Token',
            'createtime' => 'Createtime',
        ];
    }
    /**
     * 用户token
     * @param userid
     * @param token
     * @return array
     */
    public static function userLogin($userid,$token){
        $arr = UUserLogin::find()
            ->where(['userid'=>$userid,'token'=>$token])
            ->asarray()
            ->one();
        return $arr;
    }
    /**
     * 是否存在登录信息
     * @param userid
     * @return array
     */
    public static function loginInfo($userid){
        $arr = UUserLogin::find()
             ->where(['userid'=>$userid])
             ->asarray()
             ->one();
        return $arr;
    }
    /**
     * 登录的时候，如果不存在，存进去；如果已经存在，覆盖
     * @param userid
     * @param token
     * @return bool
     */
    public static function addLogin($userid,$token){
        $info = UUserLogin::loginInfo($userid);
        if(empty($info)){
            $model = new UUserLogin();
            $model ->userid     = $userid;
            $model ->token      = $token;
            $model ->createtime = date('Y-m-d H:i:s');
            $model ->save();
        }else{
            UUserLogin::updateAll(['token'=>$token,'createtime'=>date('Y-m-d H:i:s')],['id'=>$info['id']]);
        }
        return true;
    }
    /**
     * 删除
     * @param userid
     * @return bool
     */
    public static function deleteUser($userid){
        UUserLogin::deleteAll(['userid'=>$userid]);
        return true;
    }
}
