<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "p_patient_remark".
 *
 * @property string $id
 * @property string $remarkid
 * @property string $patientid
 * @property string $content
 * @property string $updateuser
 * @property string $updatetime
 * @property string $recordid
 */
class PPatientRemark extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient_remark';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['updatetime'], 'safe'],
            [['remarkid', 'patientid', 'updateuser','recordid'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'remarkid' => 'Remarkid',
            'patientid' => 'Patientid',
            'content' => 'Content',
            'updateuser' => 'Updateuser',
            'updatetime' => 'Updatetime',
            'recordid' => 'Recordid',
        ];
    }
    /**
     * 创建备注
     * @param patientid
     * @param recordid
     * @return bool
     */
    public static function addRemark($patientid,$recordid){
        $_model = new PPatientRemark();
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        $data   = Yii::$app->getRequest()->getBodyParam('remark');
        $arr    = json_decode($data,true);
        foreach ($arr as $k=>$v){
            $model = clone $_model;
            $model ->remarkid   = Commonfun::randpw();
            $model ->patientid  = $patientid;
            $model ->content    = $v['content'];
            $model ->updateuser = $userid;
            $model ->updatetime = date('Y-m-d H:i:s');
            $model ->recordid   = $recordid;
            $model ->save();
        }
        return true;
    }
    /**
     * 备注列表
     * @param recordid
     * @return array
     */
    public static function remarkList($recordid){
        $result = PPatientRemark::find()
                ->select('remarkid,content,updateuser,updatetime')
                ->where(['recordid'=>$recordid])
                ->orderBy(['updatetime'=>SORT_DESC])
                ->asArray()
                ->all();
        foreach($result as $k=>$v){
            $result[$k]['s_username'] = UUserInfo::userInfo($v['updateuser'])['s_username'];
        }
        return $result;
    }
}
