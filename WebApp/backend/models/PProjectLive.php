<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "p_project_live".
 *
 * @property string $id
 * @property string $liveid
 * @property string $title
 * @property string $content
 * @property string $userid
 * @property string $projectid
 * @property string $createtime
 * @property string $finishtime
 * @property integer $status
 */
class PProjectLive extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'p_project_live';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['createtime', 'finishtime'], 'safe'],
            [['status'], 'integer'],
            [['liveid', 'userid', 'projectid'], 'string', 'max' => 11],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'liveid' => 'Liveid',
            'title' => 'Title',
            'content' => 'Content',
            'userid' => 'Userid',
            'projectid' => 'Projectid',
            'createtime' => 'Createtime',
            'finishtime' => 'Finishtime',
            'status' => 'Status',
        ];
    }
    /**
     * 创建直播
     * @param $userid
     * @param $projectid
     * @param $title
     * @param $content
     * @param companyid
     * @return array
     */
    public static function addLive($userid,$projectid,$title,$content,$companyid){
        //是否有已经开始的
        $islive = PProjectLive::myInfo($userid,$projectid);
        if(empty($islive)){
            $model = new PProjectLive();
            $model ->title      = $title;
            $model ->content    = $content;
            $model ->userid     = $userid;
            $model ->projectid  = $projectid;
            $model ->createtime = date('Y-m-d H:i:s');
            $model ->status     = 4;
            $model ->liveid     = Commonfun::randpw();
            if($model->save()){
                $result['liveid'] = $model->liveid;
                //创建一个feed吧
                NNewsfeed::addFeed($userid,$title.'---'.$content,'',$companyid,$projectid,$result['liveid']);
                return $result;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }
    /**
     * 直播信息
     * @param $liveid
     * @return array
     */
    public static function liveInfo($liveid){
        $result = PProjectLive::find()
                ->select('userid,title,content,projectid,createtime')
                ->where(['liveid'=>$liveid,'status'=>[1,4]])
                ->asarray()
                ->one();
        if(empty($result)){
            return false;
        }else{
            $userinfo = UUserInfo::userInfo($result['userid']);
            $result['name']   = $userinfo['s_username'];
            $result['avatar'] = $userinfo['s_avatar'];
            $project  = PProject::projectByid($result['projectid']);
            $result['projectname'] = $project['projectname'];
            return $result;
        }
    }
    /**
     * 结束直播 正在录制
     * @param $liveid
     * @return bool
     */
    public static function endLive($liveid){
        $result = PProjectLive::updateAll(['status'=>2,'finishtime'=>date('Y-m-d H:i:s')],['liveid'=>$liveid]);
        if($result){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 结束录制
     * @param params
     * @param liveid
     * @return bool
     */
    public static function endRecord($params,$liveid){
        $result = PProjectLive::updateAll(['status'=>3,'finishtime'=>date('Y-m-d H:i:s'),'record_content'=>$params],['liveid'=>$liveid]);
        $content = '{"type":"live","content":'.$params.'}';
        NNewsfeed::updateAll(['status'=>1,'upload_url'=>$content],['feedid'=>$liveid]);
        if($result){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 开始直播
     * @param liveid
     * @return bool
     */
    public static function startLive($liveid){
        PProjectLive::updateAll(['status'=>1],['liveid'=>$liveid]);
        return true;
    }
    /**
     * 直播列表
     * @param projectid
     * @return array
     */
    public static function liveList($projectid){
        $result = PProjectLive::find()
                ->select('liveid,userid,title,content,createtime')
                ->where(['projectid'=>$projectid,'status'=>1])
                ->orderBy(['createtime'=>SORT_DESC])
                ->asarray()
                ->all();
        if(!empty($result)){
            $project  = PProject::projectByid($projectid);
            foreach ($result as $k=>$v){
                $userinfo = UUserInfo::userInfo($v['userid']);
                $result[$k]['name']        = $userinfo['s_username'];
                $result[$k]['avatar']      = $userinfo['s_avatar'];
                $result[$k]['projectname'] = $project['projectname'];
            }
        }
        return $result;
    }
    /**
     * 自己的直播
     * @param userid
     * @param projectid
     * @return array
     */
    public static function myInfo($userid,$projectid){
        $result = PProjectLive::find()
                ->select('liveid,userid,title,content,createtime,status')
                ->where(['userid'=>$userid,'projectid'=>$projectid,'status'=>[1,4]])
                ->orderBy(['id'=>SORT_DESC])
                ->limit(1)
                ->asarray()
                ->one();
        return $result;
    }
}
