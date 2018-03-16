<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "r_patient_remark".
 *
 * @property integer $id
 * @property string $mdid
 * @property string $remark
 * @property string $userid
 * @property string $createtime
 */
class RPatientRemark extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_patient_remark';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['remark'], 'string'],
            [['createtime','deletetime'], 'safe'],
            [['mdid', 'userid','deleteuserid'], 'string', 'max' => 32],
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
            'remark' => 'Remark',
            'userid' => 'Userid',
            'createtime' => 'Createtime',
        ];
    }
    /**
     * @todo 关联用户表
     */
    public function getMUserinfo(){
        return $this->hasMany(MUserinfo::className(), ['s_userid'=>'userid']);
    }
    /**
     * @todo 创建备注
     */
    public static function createRemark(){
        $model = new RPatientRemark();
        $model -> mdid         = Yii::$app->getRequest()->getBodyParam('mdid');
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
     * @param mdid
     */
    public static function remarkList($mdid){
        $result = RPatientRemark::find()
                ->joinWith('mUserinfo')
                ->select('r_patient_remark.*,m_userinfo.s_username')
                ->where(['r_patient_remark.mdid'=>$mdid])
                ->orderby(['r_patient_remark.createtime'=>SORT_DESC])
                ->asarray()
                ->all();
        foreach($result as $k=>$v){
            if($v['deleteuserid'] != ''){
                unset($result[$k]);
            }
            unset($result[$k]['mUserinfo']);
        }
        $arr    = array_values($result);
        return $arr;
    }
    /**
     * @todo 删除备注
     * @param id
     * @param userid
     */
    public static function deleteRemark($id,$userid){
        $result = RPatientRemark::updateAll(['deleteuserid'=>$userid,'deletetime'=>date('Y-m-d H:i:s')],['id'=>$id]);
        if($result){
            return true;
        }else{
            return false;
        }
    }
}
