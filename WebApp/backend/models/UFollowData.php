<?php

namespace app\models;

use phpDocumentor\Reflection\Types\String_;
use Yii;
use backend\controllers\BaseController;

/**
 * This is the model class for table "u_follow_data".
 *
 * @property string $u_follow_id
 * @property string $u_follow_name
 * @property string $u_follow_mem
 * @property string $u_follow_start
 * @property string $u_follow_end
 * @property string $u_follow_project
 * @property string $u_follow_setting
 * @property string $u_createuser
 * @property string $u_createtime
 * @property string $u_updateuser
 * @property string $u_updatetime
 * @property integer $u_status
 */
class UFollowData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_follow_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_follow_id'], 'required'],
            [['u_follow_start', 'u_follow_end', 'u_createtime', 'u_updatetime', 'u_status'], 'safe'],
            [['u_status'], 'integer'],
            [['u_follow_id', 'u_follow_name', 'u_follow_project', 'u_follow_setting', 'u_createuser', 'u_updateuser'], 'string', 'max' => 32],
            [['u_follow_mem'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'u_follow_id' => 'U Follow ID',
            'u_follow_name' => 'U Follow Name',
            'u_follow_mem' => 'U Follow Mem',
            'u_follow_start' => 'U Follow Start',
            'u_follow_end' => 'U Follow End',
            'u_follow_project' => 'U Follow Project',
            'u_follow_setting' => 'U Follow Setting',
            'u_createuser' => 'U Createuser',
            'u_createtime' => 'U Createtime',
            'u_updateuser' => 'U Updateuser',
            'u_updatetime' => 'U Updatetime',
            'u_status' => 'U Status',
        ];
    }


    /**
     * @todo 随访属于项目
     * @param $followId
     * @param $projectId
     */
    public static function isInProjectId($followId, $projectId)
    {
        if (UFollowData::findOne(["u_follow_id" => $followId, "u_follow_project" => $projectId])) {
            return true;
        }
        return false;
    }

    /**
     * @todo 终止随访计划
     * @param $userId
     * @param $followId
     */
    public static function CloseFollowById($userId, $followId)
    {
        $follow = UFollowData::findOne(["u_follow_id" => $followId]);
        if ($follow) {
            $follow -> u_updateuser = $userId;
            $follow -> u_updatetime = date("Y-m-d H:i:s");
            $follow -> u_status     = 2;

            if ($follow -> save()) {
                return true;
            }
        }
        return false;
    }
}
