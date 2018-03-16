<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "u_telemplate".
 *
 * @property integer $u_templateid
 * @property string $u_templatename
 * @property string $u_templatedata
 * @property integer $u_projectid
 * @property string $u_createuser
 * @property string $u_createtime
 */
class UTelemplate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_telemplate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_templatedata'], 'string'],
            [['u_projectid'], 'integer'],
            [['u_createtime'], 'safe'],
            [['u_templatename', 'u_createuser'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'u_templateid' => 'U Templateid',
            'u_templatename' => 'U Templatename',
            'u_templatedata' => 'U Templatedata',
            'u_projectid' => 'U Projectid',
            'u_createuser' => 'U Createuser',
            'u_createtime' => 'U Createtime',
        ];
    }
    /**
     * @todo 项目下模板
     * 
     */
    public static function findTemp($projectid){
        $result = UTelemplate::find()
        ->where(['u_projectid'=>$projectid])
        ->asarray()
        ->all();
        return $result;
    }
    /**
     * @todo 创建模板
     */
    public static function createTemplate(){
        $model = new UTelemplate();
        $model -> u_templatename = Yii::$app->getRequest()->getBodyParam('templatename');
        $model -> u_templatedata = Yii::$app->getRequest()->getBodyParam('templatedata');
        $model -> u_projectid    = Yii::$app->getRequest()->getBodyParam('projectid');
        $model -> u_createuser   = Yii::$app->getRequest()->getBodyParam('createuser');
        $model -> u_createtime   = date('Y-m-d H:i:s');
        if($model->save()){
            return $model->u_templateid;
        }else{
            return false;
        }
        
    }
    /**
     * @todo 模板名称
     * @param templateid
     */
    public static function getTempname($templateid){
        $tempinfo = UTelemplate::find()
                  ->where(['u_templateid'=>$templateid])
                  ->asarray()
                  ->one();
        return $tempinfo['u_templatename'];
    }
}
