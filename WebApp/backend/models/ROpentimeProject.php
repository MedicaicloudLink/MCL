<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "r_opentime_project".
 *
 * @property integer $id
 * @property string $userid
 * @property integer $projectid
 * @property string $createtime
 */
class ROpentimeProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_opentime_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['projectid'], 'integer'],
            [['createtime'], 'safe'],
            [['userid'], 'string', 'max' => 32],
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
            'projectid' => 'Projectid',
            'createtime' => 'Createtime',
        ];
    }
    /**
     * @todo 用户打开项目时间
     * @param userid
     * @param projectid
     */
    public static function openTime($userid,$projectid){
        $openinfo = ROpentimeProject::find()
                  ->select('createtime')
                  ->where(['userid'=>$userid,'projectid'=>$projectid])
                  ->asarray()
                  ->one();
        if(empty($openinfo)){
            $opentime = '';
        }
        $opentime = $openinfo['createtime'];
        return $opentime;
    }
    /**
     * @todo 记录打开项目时间
     * @param userid
     * @param projectid
     */
    public static function openProject($userid,$projectid){
        //查看是否有
        if(ROpentimeProject::openTime($userid, $projectid) == ''){
            //创建一条新数据
            $model = new ROpentimeProject();
            $model -> userid     = $userid;
            $model -> projectid  = $projectid;
            $model -> createtime = date('Y-m-d H:i:s');
            if($model->save()){
                return true;
            }else{
                return false;
            }
        }else{
            //修改
            $result = ROpentimeProject::updateAll(['createtime'=>date('Y-m-d H:i:s')],['userid'=>$userid,'projectid'=>$projectid]);
            if($result){
                return true;
            }else{
                return false;
            }
        }
    }
}
