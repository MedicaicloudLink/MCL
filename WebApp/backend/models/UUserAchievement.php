<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "u_user_achievement".
 *
 * @property string $s_userid
 * @property string $s_mail
 * @property string $s_update_time
 * @property string $s_qualification
 * @property string $s_honor
 * @property string $s_works
 * @property string $s_organization
 * @property string $s_language
 * @property string $s_education
 * @property string $s_professional
 */
class UUserAchievement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_user_achievement';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['s_userid'], 'required'],
            [['s_update_time'], 'safe'],
            [['s_qualification', 's_honor', 's_works', 's_organization', 's_language', 's_education', 's_professional'], 'string'],
            [['s_userid'], 'string', 'max' => 11],
            [['s_mail'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            's_userid' => 'S Userid',
            's_mail' => 'S Mail',
            's_update_time' => 'S Update Time',
            's_qualification' => 'S Qualification',
            's_honor' => 'S Honor',
            's_works' => 'S Works',
            's_organization' => 'S Organization',
            's_language' => 'S Language',
            's_education' => 'S Education',
            's_professional' => 'S Professional',
        ];
    }
    /**
     * 用户成就
     * @param touserid
     * @return array
     */
    public static function achievementInfo($touserid){
        $result = UUserAchievement::find()
                ->where(['s_userid'=>$touserid])
                ->asarray()
                ->all();
        return $result;
    }
    /**
     * 编辑成就
     * @param type
     * @param value
     * @return bool
     */
    public static function editAchievement($user,$type,$value){
        $achievement = UUserAchievement::achievementInfo($user);
        if(empty($achievement)){
            $model = new UUserAchievement();
            $model -> s_userid     = $user;
            $model ->$type         = $value;
            $model ->s_update_time = date('Y-m-d H:i:s');
        }else{
            $model = UUserAchievement::findOne(['s_userid'=>$user]);
            $model ->$type         = $value;
            $model ->s_update_time = date('Y-m-d H:i:s');
        }
        if($model ->save()){
            return true;
        }else{
            return false;
        }
    }
}
