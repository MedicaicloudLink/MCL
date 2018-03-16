<?php

namespace app\models;

use Yii;
use app\models\Commonfun;

/**
 * This is the model class for table "r_patient_file".
 *
 * @property integer $id
 * @property string $mdid
 * @property string $url
 * @property string $name
 * @property string $remark
 * @property string $userid
 * @property string $createtime
 * @property string $deleteuserid
 * @property string $deletetime
 */
class RPatientFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_patient_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['remark'], 'string'],
            [['createtime', 'deletetime'], 'safe'],
            [['mdid', 'userid'], 'string', 'max' => 32],
            [['url', 'name', 'deleteuserid'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mdid' => 'Mdid',
            'url' => 'Url',
            'name' => 'Name',
            'remark' => 'Remark',
            'userid' => 'Userid',
            'createtime' => 'Createtime',
            'deleteuserid' => 'Deleteuserid',
            'deletetime' => 'Deletetime',
        ];
    }
    /**
     * @todo 关联用户表
     */
    public function getMUserinfo(){
        return $this->hasMany(MUserinfo::className(), ['s_userid'=>'userid']);
    }
    /**
     * @todo 上传图片
     * @param unknown $uploadFile
     */
    public static function upFile($uploadFile){
        $data      = date("Y", time()) . '/' . date("m", time()) . '/' . date("d", time()) . '/';
        $save_path = Yii::$app->basePath.'/uploads/'.$data;
        //创建相应了、目录
        if (!file_exists($save_path)) {
            mkdir($save_path, 0777, true);
        }
        //保存数据到数据库
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $patientid  = Yii::$app->getRequest()->getBodyParam('patientid');
        //$_model     = new RPatientFile();
        foreach($uploadFile as $k=>$v){
            //保存到本地的文件名
            $filename              = explode(".", $v->name);
            $namef                 = md5(uniqid(rand(), true)) . '.' . end($filename);
            //保存到本地
            $v -> saveAs($save_path . $namef);
            //入库
            //$model                 = clone $_model;
            //文件名
//             $model->name           = $v->name;
//             $model->userid         = $userid;
//             $model->mdid           = $patientid;
//             $model->url            = Commonfun::upUfile($save_path . $namef,$namef,'INPUT-');
//             $model->createtime     = date('Y-m-d H:i:s');
//             $model->save();
            $result[$k]['url']     = Commonfun::upUfile($save_path . $namef,$namef,'INPUT-');
            $result[$k]['fname']   = $v->name;
        }
        $arr = array_values($result);
        return $arr;
    }
    /**
     * @todo 附件列表
     * @param patientid
     */
    public static function getFile($patientid){
        $result = RPatientFile::find()
                ->joinWith('mUserinfo')
                ->select('r_patient_file.id,r_patient_file.url,r_patient_file.name,r_patient_file.remark,r_patient_file.createtime,r_patient_file.userid,r_patient_file.deleteuserid,m_userinfo.s_username')
                ->where(['r_patient_file.mdid'=>$patientid])
                ->orderby(['r_patient_file.createtime'=>SORT_DESC])
                ->asarray()
                ->all();
        foreach($result as $k=>$v){
            if($v['deleteuserid'] != ''){
                unset($result[$k]);
            }
            unset($result[$k]['deleteuserid']);
            unset($result[$k]['mUserinfo']);
        }
        $arr = array_values($result);
        return $arr;
    }
    /**
     * @todo 删除附件
     */
    public static function deleteFile($userid,$id){
        $result = RPatientFile::updateAll(['deleteuserid'=>$userid,'deletetime'=>date('Y-m-d H:i:s')],['id'=>$id]);
        if($result){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 添加文字说明
     */
    public static function addFileNote($userid,$data,$mdid){
        $arr = json_decode($data,true);
        $_model = new RPatientFile();
        foreach($arr as $k=>$v){
            $model = clone $_model;
            $model -> name         = $v['fname'];
            $model -> userid       = $userid;
            $model -> mdid         = $mdid;
            $model -> url          = $v['url'];
            $model -> remark       = $v['note'];
            $model -> createtime   = date('Y-m-d H:i:s');
            $model -> save();
        }
        return true;
    }
}
