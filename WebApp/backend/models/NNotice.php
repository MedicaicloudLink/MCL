<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "n_notice".
 *
 * @property integer $id
 * @property string $senduserid
 * @property string $touserid
 * @property string $createtime
 * @property integer $type
 * @property string $projectid
 * @property integer $isread
 */
class NNotice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'n_notice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createtime'], 'safe'],
            [['type', 'isread'], 'integer'],
            [['senduserid', 'touserid', 'projectid'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'senduserid' => 'Senduserid',
            'touserid' => 'Touserid',
            'createtime' => 'Createtime',
            'type' => 'Type',
            'projectid' => 'Projectid',
            'isread' => 'Isread',
        ];
    }
    /**
     * 关联用户表
     */
    public function getUUserInfo(){
        return $this->hasMany(UUserInfo::className(), ['s_userid'  => 'senduserid']);
    }
    /**
     * 关联项目表
     */
    public function getPProject(){
        return $this->hasMany(PProject::className(), ['projectid'  => 'projectid']);
    }
    /**
     * 加入通知
     * @param senduserid
     * @param touserid
     * @param type
     * @param projectid
     * @return bool
     */
    public static function addNotice($senduserid,$touserid,$type,$projectid=''){
        $model = new NNotice();
        $model -> senduserid = $senduserid;
        $model -> touserid   = $touserid;
        $model -> type       = $type;
        $model -> projectid  = empty($projectid) ? "" : $projectid;
        $model -> isread     = 1;
        $model -> createtime = date('Y-m-d H:i:s');
        if($model->save()){
            //Commonfun::sendMess($touserid,$model->id);
            return true;
        }else{
            return false;
        }
    }
    /**
     * 通知列表
     * @param userid
     * @param flag(bar,all)
     * @param page
     * @return array
     */
    public static function noticeList($userid,$flag,$page=''){
        //导航栏10条通知
        if($flag == 'bar'){
            $result = NNotice::find()
                    ->joinWith('uUserInfo')
                    ->joinWith('pProject')
                    ->select('n_notice.*,u_user_info.s_username,p_project.projectname')
                    ->where(['touserid'=>$userid])
                    ->orderBy(['isread'=>SORT_ASC,'n_notice.createtime'=>SORT_DESC])
                    ->limit(10)
                    ->asarray()
                    ->all();
            foreach($result as $k=>$v){
                unset($result[$k]['uUserInfo']);
                unset($result[$k]['pProject']);
            }
        }else{
            //全部通知
            $start  = $page-1 <= 0 ? 0 : ($page-1) * 30;
            $data   = NNotice::find()
                    ->joinWith('uUserInfo')
                    ->joinWith('pProject')
                    ->select('n_notice.*,u_user_info.s_username,u_user_info.s_avatar,p_project.projectname')
                    ->where(['touserid'=>$userid])
                    ->orderBy(['isread'=>SORT_ASC,'n_notice.createtime'=>SORT_DESC])
                    ->offset($start)
                    ->limit(30)
                    ->asarray()
                    ->all();
            foreach($data as $k=>$v){
                unset($data[$k]['uUserInfo']);
                unset($data[$k]['pProject']);
            }
            $count  = NNotice::find()
                    ->select('id')
                    ->where(['touserid'=>$userid])
                    ->count();
            $result['data']  = $data;
            $result['count'] = $count;
        }
        return $result;
    }
    /**
     * 通知是否为某个人
     * @param userid
     * @param noticeid
     * @return array
     */
    public static function userNotice($userid,$noticeid){
        $result = NNotice::find()
                ->where(['id'=>$noticeid,'touserid'=>$userid])
                ->asArray()
                ->one();
        return $result;
    }
    /**
     * 删除通知
     * @param $userid
     * @param $noticeid
     * @return bool
     */
    public static function delNotice($userid,$noticeid){
        //通知是否为这个人的
        $usernotice = NNotice::userNotice($userid,$noticeid);
        if(empty($usernotice)){
            return false;
        }else{
            NNotice::deleteAll(['id'=>$noticeid]);
            return true;
        }
    }
    /**
     * 设置已读
     * @param userid
     * @param type
     * @param noticeid
     * @return bool
     */
    public static function setNotice($userid,$type,$noticeid){
        if($type == 'all'){
            //用户全部已读
            NNotice::updateAll(['isread'=>2],['touserid'=>$userid]);
        }else{
            $notice = explode(',',$noticeid);
            NNotice::updateAll(['isread'=>2],['touserid'=>$userid,'id'=>$notice]);
        }
        return true;
    }
}
