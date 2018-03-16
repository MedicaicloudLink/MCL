<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "f_form".
 *
 * @property string $id
 * @property string $createuser
 * @property string $createtime
 * @property string $sourcedata
 * @property integer $status
 * @property integer $projectid
 */
class FForm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'f_form';
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
            [['id'], 'required'],
            [[ 'status'], 'integer'],
            [['createtime'], 'safe'],
            [['sourcedata'], 'string'],
            [['id','createuser'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'createuser' => 'Createuser',
            'createtime' => 'Createtime',
            'sourcedata' => 'Sourcedata',
            'status' => 'Status',
        ];
    }
    /**
     * 创建表单
     * @param data
     * @return bool
     */
    public static function addForm($data){
        //项目内最近一条的状态 如果是保存，就修改上一条
        $lastform = FForm::lastInfo($data['projectid']);
        if(!empty($lastform) && $lastform['status'] == 2){
            $model = FForm::findOne(['id'=>$lastform['id']]);
            $model -> createtime = date('Y-m-d H:i:s');
            $model -> createuser = $data['userid'];
            $model -> sourcedata = $data['sourcedata'];
            $model -> status     = $data['status'];
        }else{
            $model = new FForm();
            $model -> id         = Commonfun::randpw();
            $model -> createuser = $data['userid'];
            $model -> createtime = date('Y-m-d H:i:s');
            $model -> projectid  = $data['projectid'];
            $model -> sourcedata = $data['sourcedata'];
            $model -> status     = $data['status'];
        }
        if($model -> save()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 最近一条表单信息
     * @param projectid
     * @return array
     */
    public static function lastInfo($projectid){
        $result = FForm::find()
                ->select('id,createtime,sourcedata,status')
                ->where(['projectid'=>$projectid])
                ->orderBy(['createtime'=>SORT_DESC])
                ->limit(1)
                ->asArray()
                ->one();
        return $result;
    }
    /**
     * 最近一条发布的表单信息
     * @param projectid
     * @return array
     */
    public static function lastPublishInfo($projectid){
        $result = FForm::find()
            ->select('id,createtime,sourcedata')
            ->where(['projectid'=>$projectid,'status'=>1])
            ->orderBy(['createtime'=>SORT_DESC])
            ->limit(1)
            ->asArray()
            ->one();
        return $result;
    }
    /**
     * 表单详情
     * @param formid
     * @return array
     */
    public static function formInfo($formid){
        $result = FForm::find()
                ->select('sourcedata')
                ->where(['id'=>$formid])
                ->asArray()
                ->one();
        return $result;
    }
    /**
     * 项目中所有发布的表单
     * @param projectid
     * @return array
     */
    public static function allPublish($projectid){
        $result = FForm::find()
                ->select('id,sourcedata')
                ->where(['projectid'=>$projectid,'status'=>1])
                ->orderBy(['createtime'=>SORT_DESC])
                ->asArray()
                ->all();
        return $result;
    }
    /**
     * 获取表单sourcedata中title
     * @param projectid
     * @param userid
     * @return array
     */
    public static function getTitle($projectid,$userid){
        $formjson = FForm::lastPublishInfo($projectid)['sourcedata'];
        $form     = json_decode($formjson,true);
        $title    = [];
        $key      = 0;
        foreach($form['form'] as $k=>$v){
            foreach($v['children'] as $k1=>$v1){
                if($v1['type'] != 'CAPTION'){
                    $title[$key]['title'] = $v1['title'];
                    $title[$key]['type']  = $v1['type'];
                    $key++;
                }
            }
        }
        $count = count($title);
        $title[$count]['title'] = '更新时间';
        $title[$count]['type']  = 'updateTime';
        $projectadmin = PProjectUser::userInfo($userid, $projectid);
        if (!empty($projectadmin) && $projectadmin['permission'] == 1) {
            $num = count($title);
            $title[$num]['title'] = '更新人';
            $title[$num]['type']  = 'updateuser';
        }
        //$result = Yii::$app->db->createCommand("select * from (select id,json_array_elements(json_array_elements(sourcedata->'form')#>'{children}')#>>'{type}' as type,json_array_elements(json_array_elements(sourcedata->'form')#>'{children}')#>>'{title}' as title from f_form ) t where id='".$form['id']."' and  type::varchar != 'CAPTION'")->queryAll();
        //print_r($result);exit;
        return $title;
    }
    /**
     * 表单历史版本
     * @param projectid
     * @return array
     */
    public static function formCommits($projectid){
        $result = FForm::find()
                ->select('id,createuser,createtime')
                ->where(['projectid'=>$projectid,'status'=>1])
                ->orderBy(['createtime'=>SORT_DESC])
                ->asArray()
                ->all();
        foreach ($result as $k=>$v){
            $result[$k]['username'] = UUserInfo::userInfo($v['createuser'])['s_username'];
        }
        return $result;
    }
}

