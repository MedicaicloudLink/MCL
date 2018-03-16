<?php

namespace app\models;

use Yii;
use app\models\NNewsfeed;
use app\models\Commonfun;
/**
 * This is the model class for table "n_newsfeed_comment".
 *
 * @property string $commentid
 * @property string $feedid
 * @property string $userid
 * @property string $createtime
 * @property string $content
 */
class NNewsfeedComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'n_newsfeed_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['commentid'], 'required'],
            [['createtime','content'], 'safe'],
            [['commentid', 'feedid', 'userid'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'commentid' => 'Commentid',
            'feedid' => 'Feedid',
            'userid' => 'Userid',
            'content' => 'Content',
            'createtime' => 'Createtime',
        ];
    }
    /**
     * 关联用户基本信息表
     */
    public function getUUserInfo(){
        return $this->hasMany(UUserInfo::className(), ['s_userid'  => 'userid']);
    }
    /**
     * 添加评论
     * @param userid
     * @param feedid
     * @param $content
     * @return bool
     */
    public static function addComment($userid,$feedid,$content){
        //feed是否存在
        $feed = NNewsfeed::feedInfo($feedid);
        if(empty($feed) || $feed['is_comment'] != 1){
            return false;
        }
        $model = new NNewsfeedComment();
        $model -> commentid  = Commonfun::randpw();
        $model -> feedid     = $feedid;
        $model -> userid     = $userid;
        $model -> content    = $content;
        $model -> createtime = date('Y-m-d H:i:s');
        if($model->save()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 至多5条评论显示
     * @param feedid
     * @return array
     */
    public static function feedComment($feedid){
        $result = NNewsfeedComment::find()
                ->select('n_newsfeed_comment.*,s_username,s_avatar')
                ->joinWith('uUserInfo')
                ->where(['feedid'=>$feedid])
                ->orderBy(['createtime'=>SORT_DESC])
                ->limit(5)
                ->asArray()
                ->all();
        if(!empty($result)){
            foreach($result as $k=>$v){
                unset($result[$k]['uUserInfo']);
            }
        }
        $count = NNewsfeedComment::find()
                ->select('commentid')
                ->where(['feedid'=>$feedid])
                ->count();
        $data['data']  = $result;
        $data['count'] = $count;
        return $data;
    }
    /**
     * 评论列表
     * @param userid
     * @param $feedid
     * @param $sort
     * @param $count
     * @param $since_id
     * @return array
     */
    public static function commentList($userid,$sort,$count,$since_id,$feedid){
        if($sort == 'before' && $since_id != ""){
            $order = '<';
            $order_by    = 'DESC';
        }else if($sort == 'after' && $since_id != "") {
            $order = '>';
            $order_by = 'ASC';
        }
        if($since_id == ""){
            $id = NNewsfeedComment::find()
                ->select("max(id) as maxid")
                ->where(['feedid'=>$feedid])
                ->asarray()
                ->one();
            $since_id = $id['maxid'];
            $order    = '<=';
            $order_by = 'DESC';
        }
        $result = NNewsfeedComment::find()
            ->select('n_newsfeed_comment.*,s_username,s_avatar')
            ->joinWith('uUserInfo')
            ->where(['feedid'=>$feedid])
            ->andWhere([$order, 'id', $since_id])
            ->orderBy('createtime '.$order_by.'')
            ->limit($count)
            ->asArray()
            ->all();
        if(!empty($result)){
            foreach($result as $k=>$v){
                unset($result[$k]['uUserInfo']);
            }
        }
        $data['data']  = $result;
        return $data;
    }
    /**
     * commentinfo
     * @param commentid
     * @return array
     */
    public static function commentInfo($commentid){
        $result = NNewsfeedComment::find()
                ->select('id')
                ->where(['commentid'=>$commentid])
                ->asarray()
                ->one();
        return $result;
    }
}
