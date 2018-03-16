<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "u_project_center".
 *
 * @property string $u_centerid
 * @property string $u_projectid
 */
class UProjectCenter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_project_center';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_centerid', 'u_projectid'], 'required'],
            [['u_centerid', 'u_projectid'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'u_centerid' => 'U Centerid',
            'u_projectid' => 'U Projectid',
        ];
    }
    /**
     * @todo 关联项目表
     */
    public function getUProjectdata(){
        return $this->hasMany(UProjectdata::className(), ['u_projectID' => 'u_projectid']);
    }
    /**
     * @todo 关联中心表
     */
    public function getUCentertable(){
        return $this->hasMany(UProjectdata::className(), ['u_centerID' => 'u_centerid']);
    }

    /**
     * @todo 获取项目的中心列表项目ID
     * @param $projectId
     */
    public static function getCenterListInProjectByProjectId($projectId) {
        // 中心ID列表
        $centerIdList = UProjectCenter::find()
            ->select("u_centerid")
            ->where(["u_projectid" => $projectId])
            ->asArray()
            ->all();

        if (empty($centerIdList)){
            return "";
        }
        return $centerIdList;
    }

    /**
     * @todo 删除中心
     * @param $projectId
     * @param $centerId
     * @return bool
     */
    public static function delProjectCenter($projectId, $centerId) {
        if (!UProjectCenter::findAll(['u_projectid'=>$projectId, 'u_centerid'=>$centerId])){
            /* 中心不存在或已被删除 */
            return true;
        }
        if (UProjectCenter::deleteAll(['u_projectid'=>$projectId, 'u_centerid'=>$centerId])){
            return true;
        }

        return false;
    }
}
