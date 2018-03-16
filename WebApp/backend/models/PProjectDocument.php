<?php

namespace app\models;

use app\models\PProjectUser;
use app\models\Commonfun;
/**
 * This is the model class for table "p_project_document".
 *
 * @property string $document_id
 * @property string $document_name
 * @property double $document_size
 * @property string $document_mem
 * @property string $createtime
 * @property string $createuser
 * @property integer $isadmin
 * @property string $projectid
 * @property string $document_url
 */
class PProjectDocument extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'p_project_document';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['document_id'], 'required'],
            [['document_size'], 'number'],
            [['document_mem','document_url'], 'string'],
            [['createtime'], 'safe'],
            [['isadmin'], 'integer'],
            [['document_id', 'createuser','projectid'], 'string', 'max' => 11],
            [['document_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'document_id' => 'Document ID',
            'document_name' => 'Document Name',
            'document_size' => 'Document Size',
            'document_mem' => 'Document Mem',
            'createtime' => 'Createtime',
            'createuser' => 'Createuser',
            'isadmin' => 'Isadmin',
            'projectid' => 'Projectid',
            'document_url' => 'Document Url'
        ];
    }
    /**
     * 关联用户基本信息表
     */
    public function getUUserInfo(){
        return $this->hasMany(UUserInfo::className(), ['s_userid'  => 'createuser']);
    }
    /**
     * 添加文件信息
     * @param $userid
     * @param $data
     * @param $projectid
     * @return bool
     */
    public static function addFileInfo($userid,$data,$projectid){
        //用户是否为项目管理员
        $isadmin = PProjectUser::userInfo($userid,$projectid);
        #不属于企业员工
        if(empty($isadmin)){
            return false;
        }
        $arr = json_decode($data,true);
        $_model = new PProjectDocument();
        foreach($arr as $k=>$v){
            $model = clone $_model;
            $model -> document_id    = Commonfun::randpw();
            $model -> document_name  = $v['name'];
            $model -> document_url   = $v['url'];
            $model -> document_size  = $v['size'];
            $model -> document_mem   = $v['mem'];
            $model -> createtime     = date('Y-m-d H:i:s');
            $model -> createuser     = $userid;
            $model -> isadmin        = $isadmin['permission'];
            $model -> projectid      = $projectid;
            $model -> save();
        }
        return true;
    }
    /**
     * 附件列表
     * @param userid
     * @param projectid
     * @param page
     * @param type(all:全部；my:我的;admin:管理员的)
     * @return array
     */
    public static function fileList($userid,$projectid,$page,$type){
        if($type == 'my'){
            $where = ['createuser'=>$userid,'projectid'=>$projectid];
        }
        if($type == 'admin'){
            $where = ['isadmin'=>1,'projectid'=>$projectid];
        }
        if($type == 'all'){
            $where = ['projectid'=>$projectid];
        }
        $start  = $page-1 <= 0 ? 0 : ($page-1) * 10;
        $data   = PProjectDocument::find()
                ->joinWith('uUserInfo')
                ->select('p_project_document.*,u_user_info.s_username')
                ->where($where)
                ->offset($start)
                ->limit(10)
                ->orderBy(['createtime'=>SORT_DESC])
                ->asarray()
                ->all();
        foreach($data as $k=>$v){
            unset($data[$k]['uUserInfo']);
        }
        $count  = PProjectDocument::find()
                ->select('document_id')
                ->where($where)
                ->count();
        $result['data']  = $data;
        $result['count'] = $count;
        return $result;
    }
    /**
     * 用户上传的某个文件
     * @param userid
     * @param documentid
     * @return array
     */
    public static function documentidUser($userid,$documentid){
        $result = PProjectDocument::find()
                ->select('document_id')
                ->where(['createuser'=>$userid,'document_id'=>$documentid])
                ->asarray()
                ->one();
        return $result;
    }
    /**
     * 删除文件
     * @param userid
     * @param projectid
     * @param documentid
     * @return bool
     */
    public static function delFile($userid,$projectid,$documentid){
        $userfile = PProjectDocument::documentidUser($userid,$documentid);
        //用户是否上传过这个文件
        if(empty($userfile)){
            //用户是否为项目管理员
            $isadmin = PProjectUser::userInfo($userid,$projectid);
            if(!empty($isadmin) && $isadmin['permission'] == 1){
                //删除吧
                PProjectDocument::deleteAll(['document_id'=>$documentid]);
            }else{
                return false;
            }
        }else{
            //删除吧
            PProjectDocument::deleteAll(['document_id'=>$documentid]);
        }
        return true;
    }
    /**
     * 文档信息
     * @param documentid
     * @return array
     */
    public static function fileInfo($documentid){
        $result = PProjectDocument::find()
                ->where(['document_id'=>$documentid])
                ->asarray()
                ->one();
        return $result;
    }
}
