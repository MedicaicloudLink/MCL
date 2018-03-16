<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "r_commit_log".
 *
 * @property integer $id
 * @property string $mdid
 * @property string $userid
 * @property string $remark
 * @property string $createtime
 * @property integer $status
 */
class RCommitLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_commit_log';
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
            [['mdid', 'userid'], 'string', 'max' => 32],
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
            'userid' => 'Userid',
            'remark' => 'Remark',
            'createtime' => 'Createtime',
            'status' => 'Status',
        ];
    }
    /**
     * @todo 添加审核情况
     */
    public static function addLog($mdid,$userid,$remark='',$status){
        //提交人
        $model = new RCommitLog();
        $model -> mdid         = $mdid;
        $model -> userid       = $userid;
        $model -> remark       = $remark;
        $model -> status       = $status;
        $model -> createtime   = date('Y-m-d H:i:s');
        $model -> save();
        return true;
    }
    /**
     * @todo 审核情况
     */
    public static function commitLog($mdid){
        $result = RCommitLog::find()
        ->select('userid,createtime,status')
        ->where(['mdid'=>$mdid])
        ->orderby(['createtime'=>SORT_DESC])
        ->asarray()
        ->all();
        foreach($result as $key=>$val){
            $result[$key]['username'] = MUserinfo::userInfo($val['userid'])[0]['s_username'];
        }
        return $result;
    }
}
