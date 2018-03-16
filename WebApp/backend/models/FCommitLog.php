<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "f_commit_log".
 *
 * @property integer $id
 * @property string $taskid
 * @property string $mdid
 * @property string $userid
 * @property string $remark
 * @property string $createtime
 * @property integer $status
 */
class FCommitLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'f_commit_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['remark'], 'string'],
            [['createtime'], 'safe'],
            [['status'], 'integer'],
            [['taskid', 'mdid', 'userid'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'taskid' => 'Taskid',
            'mdid' => 'Mdid',
            'userid' => 'Userid',
            'remark' => 'Remark',
            'createtime' => 'Createtime',
            'status' => 'Status',
        ];
    }
    /**
     * @todo 添加日志
     */
    public static function addLog($mdid,$taskid,$userid,$status,$remark=''){
        $model = new FCommitLog();
        $model -> userid = $userid;
        $model -> mdid   = $mdid;
        $model -> taskid = $taskid;
        $model -> remark = $remark;
        $model -> status = $status;
        $model -> createtime = date('Y-m-d H:i:s');
        $model -> save();
        return true;
    }
    /**
     * @todo 审核情况
     */
    public static function commitLog($mdid,$taskid){
        $result = FCommitLog::find()
        ->select('userid,createtime,status')
        ->where(['mdid'=>$mdid,'taskid'=>$taskid])
        ->orderby(['createtime'=>SORT_DESC])
        ->asarray()
        ->all();
        foreach($result as $key=>$val){
            $result[$key]['username'] = MUserinfo::userInfo($val['userid'])[0]['s_username'];
        }
        return $result;
    }
}
