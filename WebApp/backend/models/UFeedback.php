<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "u_feedback".
 *
 * @property integer $id
 * @property string $userid
 * @property string $content
 * @property string $createtime
 * @property string $url
 */
class UFeedback extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_feedback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['createtime'], 'safe'],
            [['userid','url'], 'string', 'max' => 255],
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
            'content' => 'Content',
            'url' => 'Url',
            'createtime' => 'Createtime',
        ];
    }
    /**
     * 添加反馈
     * @param userid
     * @param content
     * @param url
     * @return bool
     */
    public static function addFeedback($userid,$content,$url){
        $model = new UFeedback();
        $model -> userid     = $userid;
        $model -> content    = $content;
        $model -> url        = $url;
        $model -> createtime = date('Y-m-d H:i:s');
        $model -> save();
        $userinfo = UUserInfo::userInfo($userid);
        //发邮件
        $subject = '系统反馈';
        $path    = '/mail/feedback';
        $content = ['url'=>$url,'content'=>$content,'name'=>$userinfo['s_username'],'mail'=>$userinfo['s_mail'],'time'=>$model -> createtime];
        Commonfun::sendMail('support@medicayun.cn','support@medicayun.cn',$subject,$path,$content);

        return true;
    }
}
