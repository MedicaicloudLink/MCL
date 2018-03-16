<?php

namespace app\models;

use Yii;
use app\models\Commonfun;
use app\models\QCompanyUser;
use app\models\PProjectUser;
use app\models\PProject;
use app\models\QCompany;
use app\models\NNewsfeedComment;
/**
 * This is the model class for table "n_newsfeed".
 *
 * @property string $feedid
 * @property string $userid
 * @property string $content
 * @property string $upload_url
 * @property string $createtime
 * @property integer $is_comment
 * @property string $companyid
 * @property string $projectid
 * @property integer $status
 */
class NNewsfeed extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'n_newsfeed';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['feedid', 'userid'], 'required'],
            [['content', 'upload_url'], 'string'],
            [['createtime'], 'safe'],
            [['is_comment','status'], 'integer'],
            [['feedid', 'userid', 'companyid', 'projectid'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'feedid' => 'Feedid',
            'userid' => 'Userid',
            'content' => 'Content',
            'upload_url' => 'Upload Url',
            'createtime' => 'Createtime',
            'is_comment' => 'Is Comment',
            'companyid' => 'Companyid',
            'projectid' => 'Projectid',
            'status' => 'Status',
        ];
    }
    /**
     * 关联用户基本信息表
     */
    public function getUUserInfo(){
        return $this->hasMany(UUserInfo::className(), ['s_userid'  => 'userid']);
    }
    /**
     * 发布feed
     * @param $userid
     * @param $content
     * @param $upload_url
     * @param $companyid
     * @param $projectid
     * @param status
     * @return bool
     */
    public static function addFeed($userid,$content,$upload_url,$companyid,$projectid,$status=''){
        $model = new NNewsfeed();
        if($status != ''){
            $model -> feedid     = $status;
        }else{
            $model -> feedid     = Commonfun::randpw();
        }
        $model -> userid     = $userid;
        $model -> content    = $content;
        $model -> upload_url = $upload_url;
        $model -> createtime = date('Y-m-d H:i:s');
        $model -> is_comment = 1;
        $model -> companyid  = $companyid;
        $model -> projectid  = !empty($projectid)?$projectid:"";
        $model -> status     = !empty($status)?2:1;
        if($model->save()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * feed内容
     * @param feedid
     * @return array
     */
    public static function feedInfo($feedid){
        $result = NNewsfeed::find()
                ->select('id,feedid,is_comment,userid')
                ->where(['feedid'=>$feedid])
                ->asArray()
                ->one();
        return $result;
    }
    /**
     * 删除feed
     * @param userid
     * @param feedid
     * @param companyid
     * @param projectid
     * @return bool
     */
    public static function delFeed($userid,$feedid,$companyid,$projectid){
        $userfeed = NNewsfeed::feedInfo($feedid);
        if(!empty($userfeed) && $userfeed['userid'] == $userid){
            //自己创建的，可以删除
            #删除feed
            NNewsfeed::deleteAll(['feedid'=>$feedid]);
            #删除feed下的评论
            NNewsfeedComment::deleteAll(['feedid'=>$feedid]);
        }else{
            $companyadmin = QCompanyUser::userPermission($userid,$companyid);
            //用户是项目里的feed
            if($projectid == ''){
                //是否为企业管理员
                if(!empty($companyadmin) && $companyadmin['permission'] == 1){
                    //是企业管理员，可以删除
                    #删除feed
                    NNewsfeed::deleteAll(['feedid'=>$feedid]);
                    #删除feed下的评论
                    NNewsfeedComment::deleteAll(['feedid'=>$feedid]);
                }else{
                    return false;
                }
            }else{
                $projectadmin = PProjectUser::userInfo($userid,$projectid);
                //是否为项目管理员或者企业管理员
                if((!empty($projectadmin) && $projectadmin['permission'] == 1) || (!empty($companyadmin) && $companyadmin['permission'] == 1)){
                    //是企业管理员或者企业管理员，可以删除
                    #删除feed
                    NNewsfeed::deleteAll(['feedid'=>$feedid]);
                    #删除feed下的评论
                    NNewsfeedComment::deleteAll(['feedid'=>$feedid]);
                }else{
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * 消息列表
     * @param userid
     * @param sort
     * @param $count
     * @param $since_id
     * @param $companyid
     * @param projectid
     * @return array
     */
    public static function feedList($userid,$sort,$count,$since_id,$companyid,$projectid){
        if($sort == 'before' && $since_id != ""){
            $order = '<';
            $order_by    = 'DESC';
        }else if($sort == 'after' && $since_id != "") {
            $order = '>';
            $order_by = 'ASC';
        }
        if($since_id == ""){
            //是否为项目内部
            if($projectid == ''){
                //不是项目里
                //用户在企业中参加的项目
                $projectidarr = PProjectUser::userConpanyProject($userid,$companyid);
                $where        = ['and',['projectid'=>$projectidarr],['companyid'=>$companyid],['status'=>1]];
                $orwhere      = ['and',['projectid'=>''],['companyid'=>$companyid],['status'=>1]];
            }else{
                //是项目里
                $where        = ['and',['projectid'=>$projectid],['status'=>1]];
                $orwhere      = [];
            }
            $id = NNewsfeed::find()
                ->select("max(id) as maxid")
                ->where($where)
                ->orWhere($orwhere)
                ->asarray()
                ->one();
            $since_id = $id['maxid'];
            $order    = '<=';
            $order_by = 'DESC';
        }
        //是否为项目内部
        if($projectid == ''){
            //不是项目里
            //用户在企业中参加的项目
            $projectidarr = PProjectUser::userConpanyProject($userid,$companyid);
            $where        = ['and',['projectid'=>$projectidarr],['companyid'=>$companyid],[$order, 'id', $since_id],['status'=>1]];
            $orwhere      = ['and',['projectid'=>''],['companyid'=>$companyid],[$order, 'id', $since_id],['status'=>1]];
        }else{
            //是项目里
            //项目详情 是否已归档
            $projectstatus = PProject::projectByid($projectid)['status'];
            if($projectstatus != 1){
                return false;
            }
            $where         = ['and',['projectid'=>$projectid],[$order, 'id', $since_id],['status'=>1]];
            $orwhere       = [];
        }
        $result = NNewsfeed::find()
                ->select('n_newsfeed.*,s_username,s_avatar')
                ->joinWith('uUserInfo')
                ->where($where)
                ->orWhere($orwhere)
                ->orderBy('createtime '.$order_by.'')
                ->limit($count)
                ->asArray()
                ->all();
        foreach ($result as $k=>$v){
            unset($result[$k]['uUserInfo']);
            //企业名称
            $result[$k]['companyname']     = QCompany::companyInfo($companyid)['name'];
            if($v['projectid'] == ''){
                //是企业内发布的
                $result[$k]['type']        = 'company';
            }else{
                //是项目内发布的
                $result[$k]['type']        = 'project';
                $result[$k]['projectname'] = PProject::projectByid($v['projectid'])['projectname'];
            }
            if($v['is_comment'] == 1){
                //评论
                $result[$k]['comment']     = NNewsfeedComment::feedComment($v['feedid']);
            }
        }
        $data['data']  = $result;
        return $data;
    }
    /**
     * 关闭/开启评论
     * @param userid
     * @param feedid
     * @param $companyid
     * @param $status
     * @param $projectid
     * @return bool
     */
    public static function doFeedComment($userid,$feedid,$companyid,$status,$projectid){
        $userfeed = NNewsfeed::feedInfo($feedid);
        if(!empty($userfeed) && $userfeed['userid'] == $userid){
            //自己创建的，可以设置
            #设置吧
            NNewsfeed::updateAll(['is_comment'=>$status],['feedid'=>$feedid]);
        }else{
            $companyadmin = QCompanyUser::userPermission($userid,$companyid);
            //用户是项目里的feed
            if($projectid == ''){
                //是否为企业管理员
                if(!empty($companyadmin) && $companyadmin['permission'] == 1){
                    //是企业管理员，可以设置
                    NNewsfeed::updateAll(['is_comment'=>$status],['feedid'=>$feedid]);
                }else{
                    return false;
                }
            }else{
                $projectadmin = PProjectUser::userInfo($userid,$projectid);
                //是否为项目管理员或者企业管理员
                if((!empty($projectadmin) && $projectadmin['permission'] == 1) || (!empty($companyadmin) && $companyadmin['permission'] == 1)){
                    //是企业管理员或者企业管理员，可以设置
                    NNewsfeed::updateAll(['is_comment'=>$status],['feedid'=>$feedid]);
                }else{
                    return false;
                }
            }
        }
        return true;
    }
    /**
     * 个人消息日志
     * @param userid
     * @param touserid
     * @param $sort
     * @param $count
     * @param $since_id
     * @param companyid
     * @return array
     */
    public static function userFeedlist($userid,$touserid,$sort,$count,$since_id,$companyid){
        if($sort == 'before' && $since_id != ""){
            $order = '<';
            $order_by    = 'DESC';
        }else if($sort == 'after' && $since_id != "") {
            $order = '>';
            $order_by = 'ASC';
        }
        if($since_id == ""){
            //是否为自己看自己
            if($userid == $touserid){
                //自己看自己的
                $where = ['userid'=>$touserid,'companyid'=>$companyid,'status'=>1];
            }else{
                //看别人的
                //在这个企业参加的共同项目
                #-访问者参加的项目（没有屏蔽项目动态的）
                $userproject   = PProjectUser::userConpanyProject($userid,$companyid);
                #-被访问者参加的项目（没有屏蔽项目动态的）
                $touserproject = PProjectUser::userConpanyProject($touserid,$companyid);
                #-参加的公共项目
                $publicproject = array_intersect ( $userproject, $touserproject );
                if(empty($publicproject)){
                    $publicproject = '';
                }
                $where         = ['projectid'=>$publicproject,'companyid'=>$companyid,'userid'=>$touserid,'status'=>1];
            }
            $id = NNewsfeed::find()
                ->select("max(id) as maxid")
                ->where($where)
                ->asarray()
                ->one();
            $since_id = $id['maxid'];
            $order    = '<=';
            $order_by = 'DESC';
        }
        //是否为自己看自己
        if($userid == $touserid){
            //自己看自己的
            $where = ['userid'=>$touserid,'companyid'=>$companyid,'status'=>1];
        }else{
            //看别人的
            //在这个企业参加的共同项目
            #-访问者参加的项目（没有屏蔽项目动态的）
            $userproject   = PProjectUser::userConpanyProject($userid,$companyid);
            #-被访问者参加的项目（没有屏蔽项目动态的）
            $touserproject = PProjectUser::userConpanyProject($touserid,$companyid);
            #-参加的公共项目
            $publicproject = array_intersect ( $userproject, $touserproject );
            if(empty($publicproject)){
                $publicproject = '';
            }
            $where         = ['projectid'=>$publicproject,'companyid'=>$companyid,'userid'=>$touserid,'status'=>1];
        }
        $result = NNewsfeed::find()
                ->select('n_newsfeed.*,s_username,s_avatar')
                ->joinWith('uUserInfo')
                ->where($where)
                ->andWhere([$order, 'id', $since_id])
                ->orderBy('createtime '.$order_by.'')
                ->limit($count)
                ->asArray()
                ->all();
        foreach ($result as $k=>$v){
            unset($result[$k]['uUserInfo']);
            //企业名称
            $result[$k]['companyname']     = QCompany::companyInfo($companyid)['name'];
            if($v['projectid'] == ''){
                //是企业内发布的
                $result[$k]['type']        = 'company';
            }else{
                //是项目内发布的
                $result[$k]['type']        = 'project';
                $result[$k]['projectname'] = PProject::projectByid($v['projectid'])['projectname'];
            }
            if($v['is_comment'] == 1){
                //评论
                $result[$k]['comment']     = NNewsfeedComment::feedComment($v['feedid']);
            }
        }
        $data['data']  = $result;
        return $data;
    }
    /**
     * 单个feed详情
     * @param feedid
     * @param companyid
     * @return array
     */
    public static function feedById($feedid,$companyid){
        $result = NNewsfeed::find()
                ->select('n_newsfeed.*,s_username,s_avatar')
                ->joinWith('uUserInfo')
                ->where(['feedid'=>$feedid])
                ->asArray()
                ->one();
        unset($result['uUserInfo']);
        //企业名称
        $result['companyname'] = QCompany::companyInfo($companyid)['name'];
        if($result['projectid'] == ''){
            //是企业内发布的
            $result['type'] = 'company';
        }else{
            //是项目内发布的
            $result['type']        = 'project';
            $result['projectname'] = PProject::projectByid($result['projectid'])['projectname'];
        }
        if($result['is_comment'] == 1){
            //评论
            $result['comment']     = NNewsfeedComment::feedComment($result['feedid']);
        }
        return $result;
    }
}
