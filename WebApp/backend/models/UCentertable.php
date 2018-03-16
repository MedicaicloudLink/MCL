<?php

namespace app\models;

use Yii;
use app\models\UCenterUser;
/**
 * This is the model class for table "u_centertable".
 *
 * @property string $u_centerID
 * @property string $u_centernum
 * @property string $u_centername
 * @property string $u_centerlead
 * @property string $u_centeradr
 * @property string $u_centerphone
 * @property string $u_centeremail
 * @property string $u_centerzipcode
 * @property integer $u_projectid
 * @property string $u_centertime
 * @property string $u_centercreateuser
 */
class UCentertable extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_centertable';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_centerID'], 'required'],
            [['u_projectid','status'], 'integer'],
            [['u_centertime'], 'safe'],
            [['u_centerID', 'u_centernum', 'u_centername', 'u_centerlead', 'u_centeradr', 'u_centerphone', 'u_centeremail', 'u_centerzipcode', 'u_centercreateuser'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'u_centerID' => 'U Center ID',
            'u_centernum' => 'U Centernum',
            'u_centername' => 'U Centername',
            'u_centerlead' => 'U Centerlead',
            'u_centeradr' => 'U Centeradr',
            'u_centerphone' => 'U Centerphone',
            'u_centeremail' => 'U Centeremail',
            'u_centerzipcode' => 'U Centerzipcode',
            'u_projectid' => 'U Projectid',
            'u_centertime' => 'U Centertime',
            'u_centercreateuser' => 'U Centercreateuser',
        ];
    }
    /**
     * @todo 关联用户表
     */
    public function getMUserinfo(){
        return $this->hasMany(MUserinfo::className(), ['s_userid' => 'u_centercreateuser']);
    }
    /**
     * @todo 中心列表
     */
    public static function centerList($projectid){
        $result = UCentertable::find()
        ->where(['status'=>1,'u_projectid'=>$projectid])
        ->asarray()
        ->all();
        if(empty($result)){
            $result = '';
        }else{
            foreach($result as $key=>$val){
                $result[$key]['count']   = count(UCenterUser::findMemberListByCenterId($val['u_centerID']));
                $result[$key]['userarr'] = UCenterUser::findMemberListByCenterId($val['u_centerID']);
            }
        }
        return $result;
    }
    /**
     * @todo 中心详情
     * @param centerid
     */
    public static function centerDetail($centerid){
        $result = UCentertable::find()
        ->where(['u_centerID'=>$centerid])
        ->asarray()
        ->all();
        if(empty($result)){
            $result = '';
        }else{
            foreach($result as $key=>$val){
                $result[$key]['count']   = count(UCenterUser::findMemberListByCenterId($val['u_centerID']));
                $result[$key]['userarr'] = UCenterUser::findMemberListByCenterId($val['u_centerID']);
            }
        }
        return $result;
    }
    /**
     * @todo 项目对应中心详情列表
     * @param $centerIdArray 协作中心ID集合
     */
    public static function findCentersByCenterIds($centerIdArray){
        foreach ($centerIdArray as $key => $val) {
            $centerIds[] = $val["u_centerid"];
        }
    
        $centerList = UCentertable::find()
        ->select("u_centerID, u_centername, u_centermem, u_centeradr, u_centerphone, u_centertime, s_username, u_centercreateuser ")
        ->joinWith("mUserinfo")
        ->where(["u_centerID" => $centerIds])
        ->asArray()
        ->all();
    
        foreach ($centerIdArray as $key => $val) {
            unset($centerList[$key]["mUserinfo"]);
            unset($centerList[$key]["u_centercreateuser"]);
        }
        return $centerList;
    }
    
    /**
     * @todo 创建中心
     * @param $projectId
     * @param $centerModel
     * @return bool
     */
    public static function createCenter($centerModel, $projectId) {
    
        if($centerModel->save()) {
            //顺便把创建人加入中心
            if(UCenterUser::addMemberForCenter($centerModel ->u_centerID, $centerModel->u_centercreateuser, 1,$projectId)){
                return true;
            }else{
                return false;
            }
        } else {
            $isHave = UCentertable::findOne(['u_centerID'=>$centerModel ->u_centerID]);
            if ($isHave) {
                return "isHave";
            }
        }
        return false;
    }
    
    /**
     * @todo 删除中心
     * @param $centerId
     * @return bool
     */
    public static function delCenter($centerId) {
        if(UCentertable::updateAll(['status'=>2,'u_centertime'=>date('Y-m-d')],['u_centerID'=>$centerId])) {
            UCenterUser::removeMemberForCenter($centerId,[]);
            return true;
        }
        return false;
    }
    
    /**
     * @todo 修改分中心
     * @param array $centerMsgs
     * @return bool
     */
    public static function updateCenterByCenterId(){
        $centerid  = Yii::$app->getRequest()->getBodyParam('centerid');
        $model     = UCentertable::findOne(["u_centerID" => $centerid]);
        if (empty($model)){
            return false;
        }
        $model->u_centernum        = Yii::$app->getRequest()->getBodyParam("centernum");
        $model->u_centername       = Yii::$app->getRequest()->getBodyParam("centername");
        $model->u_centerlead       = Yii::$app->getRequest()->getBodyParam("centerlead");
        $model->u_centeradr        = Yii::$app->getRequest()->getBodyParam("centeradr");
        $model->u_centerphone      = Yii::$app->getRequest()->getBodyParam("centerphone");
        $model->u_centeremail      = Yii::$app->getRequest()->getBodyParam("centeremail");
        $model->u_centerzipcode    = Yii::$app->getRequest()->getBodyParam("centerzipcode");
        $model->u_centercreateuser = Yii::$app->getRequest()->getBodyParam("userid");
        $model->u_centertime       = date("Y-m-d");
        if ($model->save()){
            return true;
        }
        return false;
    }
    /**
     * @todo 中心名称
     * @param centerid
     */
    public static function getCentername($centerid){
        $centerinfo = UCentertable::find()
                    ->where(['u_centerID'=>$centerid])
                    ->asarray()
                    ->one();
        return $centerinfo;
    }
}
