<?php

namespace app\models;

use Yii;
use app\models\Commonfun;

/**
 * This is the model class for table "f_file".
 *
 * @property integer $id
 * @property string $recordid
 * @property string $url
 * @property string $name
 * @property string $remark
 * @property string $userid
 * @property string $createtime
 * @property string $deleteuserid
 * @property string $deletetime
 */
class FFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'f_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createtime', 'deletetime'], 'safe'],
            [['recordid', 'userid', 'deleteuserid'], 'string', 'max' => 32],
            [['url', 'name', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'recordid' => 'Recordid',
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
//         $userid     = Yii::$app->getRequest()->getBodyParam('userid');
//         $recordid   = Yii::$app->getRequest()->getBodyParam('recordid');
        //$_model     = new FFile();
        foreach($uploadFile as $k=>$v){
            //保存到本地的文件名
            $filename              = explode(".", $v->name);
            $namef                 = md5(uniqid(rand(), true)) . '.' . end($filename);
            //保存到本地
            $v -> saveAs($save_path . $namef);
            //入库
//             $model                 = clone $_model;
//             //文件名
//             $model->name           = $v->name;
//             $model->userid         = $userid;
//             $model->recordid       = $recordid;
//             $model->url            = Commonfun::upUfile($save_path . $namef,$namef,'FOLLOW-');
//             $model->createtime     = date('Y-m-d H:i:s');
//             $model->save();
            $result[$k]['url']     = Commonfun::upUfile($save_path . $namef,$namef,'FOLLOW-');
            $result[$k]['fname']   = $v->name;
        }
        $arr    = array_values($result);
        return $arr;
    }
    /**
     * @todo 添加文字说明
     */
    public static function addFileNote($userid,$data,$recordid){
        $arr = json_decode($data,true);
        $_model = new FFile();
        foreach($arr as $k=>$v){
            $model = clone $_model;
            $model -> name         = $v['fname'];
            $model -> userid       = $userid;
            $model -> recordid     = $recordid;
            $model -> url          = $v['url'];
            $model -> remark       = $v['note'];
            $model -> createtime   = date('Y-m-d H:i:s');
            $model -> save();
        }
        return true;
    }
    /**
     * @todo 附件列表
     * @param recordid
     */
    public static function getFile($recordid){
        $result = FFile::find()
        ->joinWith('mUserinfo')
        ->select('f_file.id,f_file.name,f_file.remark,f_file.createtime,f_file.userid,f_file.deleteuserid,f_file.url,m_userinfo.s_username')
        ->where(['f_file.recordid'=>$recordid])
        ->orderby(['f_file.createtime'=>SORT_DESC])
        ->asarray()
        ->all();
        foreach($result as $k=>$v){
            if($v['deleteuserid'] != ''){
                unset($result[$k]);
            }
            unset($result[$k]['deleteuserid']);
            unset($result[$k]['mUserinfo']);
        }
        $arr    = array_values($result);
        return $arr;
    }
    /**
     * @todo 删除附件
     */
    public static function deleteFile($userid,$id){
        $result = FFile::updateAll(['deleteuserid'=>$userid,'deletetime'=>date('Y-m-d H:i:s')],['id'=>$id]);
        if($result){
            return true;
        }else{
            return false;
        }
    }
}
