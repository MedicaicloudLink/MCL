<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "p_patient_file".
 *
 * @property string $id
 * @property string $fileid
 * @property string $name
 * @property string $fileurl
 * @property integer $type
 * @property double $size
 * @property string $patientid
 * @property string $updateuser
 * @property string $updatetime
 * @property string $recordid
 */
class PPatientFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient_file';
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
            [['type'], 'integer'],
            [['size'], 'number'],
            [['updatetime'], 'safe'],
            [['fileid', 'patientid', 'updateuser','recordid'], 'string', 'max' => 11],
            [['fileurl','name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fileid' => 'Fileid',
            'name' => 'Name',
            'fileurl' => 'Fileurl',
            'type' => 'Type',
            'size' => 'Size',
            'patientid' => 'Patientid',
            'updateuser' => 'Updateuser',
            'updatetime' => 'Updatetime',
            'recordid'  => 'Recordid',
        ];
    }
    /**
     * 创建附件
     * @param patientid
     * @param recordid
     * @return bool
     */
    public static function addFile($patientid,$recordid){
        $data   = Yii::$app->getRequest()->getBodyParam('fileinfo');
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        $arr    = json_decode($data,true);
        $_model = new PPatientFile();
        foreach ($arr as $k=>$v){
            $model = clone $_model;
            $model ->fileid     = Commonfun::randpw();
            $model ->name       = $v['filename'];
            $model ->fileurl    = $v['fileurl'];
            $model ->type       = $v['type'];
            $model ->size       = $v['size'];
            $model ->patientid  = $patientid;
            $model ->recordid   = $recordid;
            $model ->updateuser = $userid;
            $model ->updatetime = date('Y-m-d H:i:s');
            $model ->save();
        }
        return true;
    }
    /**
     * 附件列表
     * @param recordid
     * @return array
     */
    public static function fileList($recordid){
        $result = PPatientFile::find()
                ->select('fileid,name as filename,fileurl,type,size,updateuser,updatetime')
                ->where(['recordid'=>$recordid])
                ->orderBy(['updatetime'=>SORT_DESC])
                ->asArray()
                ->all();
        foreach($result as $k=>$v){
            $result[$k]['s_username'] = UUserInfo::userInfo($v['updateuser'])['s_username'];
        }
        return $result;
    }
    /**
     * 附件详情
     * @Param fileid
     * @return array
     */
    public static function fileInfo($fileid){
        $result = PPatientFile::find()
                ->where(['fileid'=>$fileid])
                ->asArray()
                ->one();
        return $result;
    }
}
