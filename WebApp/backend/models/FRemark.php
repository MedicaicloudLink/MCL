<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "f_remark".
 *
 * @property integer $id
 * @property string $recordid
 * @property string $remark
 * @property string $userid
 * @property string $createtime
 * @property string $deleteuserid
 * @property string $deletetime
 */
class FRemark extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'f_remark';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['remark'], 'string'],
            [['createtime', 'deletetime'], 'safe'],
            [['recordid', 'userid', 'deleteuserid'], 'string', 'max' => 32],
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
     * @todo 创键备注
     */
    public static function createRemark(){
        $model = new FRemark();
        $model -> recordid     = Yii::$app->getRequest()->getBodyParam('recordid');
        $model -> remark       = Yii::$app->getRequest()->getBodyParam('remark');
        $model -> userid       = Yii::$app->getRequest()->getBodyParam('userid');
        $model -> createtime   = date('Y-m-d H:i:s');
        if($model -> save()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 备注列表
     */
    public static function remarkList($recordid){
        $result = FRemark::find()
                ->joinWith('mUserinfo')
                ->select('f_remark.*,m_userinfo.s_username')
                ->where(['f_remark.recordid'=>$recordid])
                ->orderby(['f_remark.createtime'=>SORT_DESC])
                ->asarray()
                ->all();
        foreach($result as $k=>$v){
            if($v['deleteuserid'] != ''){
                unset($result[$k]);
            }
            unset($result[$k]['deleteuserid']);
            unset($result[$k]['deletetime']);
            unset($result[$k]['mUserinfo']);
        }
        $arr = array_values($result);
        return $arr;
    }
    /**
     * @todo 删除备注
     */
    public static function deleteRemark($id,$userid){
        $result = FRemark::updateAll(['deleteuserid'=>$userid,'deletetime'=>date('Y-m-d H:i:s')],['id'=>$id]);
        if($result){
            return true;
        }else{
            return false;
        }
    }
}
