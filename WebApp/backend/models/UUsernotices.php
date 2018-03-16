<?php

namespace app\models;

use Yii;
use app\models\UProjectdata;
use app\models\MUserinfo;
use app\models\UUserProject;
/**
 * This is the model class for table "u_usernotices".
 *
 * @property integer $id
 * @property string $userid
 * @property string $content
 * @property string $createtime
 * @property integer $isread
 * @property integer $type
 * @property integer $status
 */
class UUsernotices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_usernotices';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createtime'], 'safe'],
            [['isread', 'type', 'status', 'logid'], 'integer'],
            [['userid', 'content','exituserid'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'content' => 'Content',
            'createtime' => 'Createtime',
            'isread' => 'Isread',
            'type' => 'Type',
            'status' => 'Status',
            'logid' => 'Logid',
        ];
    }
    /**
     * @todo 关联项目记录表
     */
    public function getUNotice(){
        return $this->hasMany(UNotice::className(), ['id' => 'logid']);
    }
    /**
     * @todo 创建通知
     * @param type 通知类型（1：申请加入项目；2：退出项目 3：处理结果 4.反馈给用户的申请结果）
     * @param userid   1.谁申请加入项目  2.谁退出项目 3.谁处理了申请 
     * @param projectid  （type为1和2时必填）
     * @param logid (type为1和3时必填)
     */
    public static function createUsernotice($type,$userid,$projectid,$logid){
        $_model        = new UUsernotices();
        //用户信息
        $userinfo      = MUserinfo::userInfo($userid);
        $username      = !empty($userinfo) ? $userinfo[0]['s_username'] : '';
        //项目名称
        $projectinfo   = UProjectdata::getProjectDetailByProjectId($projectid);
        $projectname   = !empty($projectinfo) ? $projectinfo[0]['u_projectName'] : '';
        //项目管理员
        $projectadmin  = UUserProject::projectAdmin($projectid);
        if($type == 1){
            //循环入库
            if(!empty($projectadmin)){
                foreach ($projectadmin as $k=>$v){
                    $model               = clone $_model;
                    //添加的内容
                    $model -> content    = $username.'申请加入'.$projectname;
                    $model -> createtime = date('Y-m-d H:i:s');
                    $model -> type       = $type;
                    $model -> logid      = $logid;
                    $model -> userid     = $v;
                    $model -> save();
                }
            }
        }
        if($type == 2){
            //循环入库
            if(!empty($projectadmin)){
                foreach ($projectadmin as $k=>$v){
                    $model               = clone $_model;
                    //添加的内容
                    $model -> content    = $username.'退出项目'.$projectname;
                    $model -> createtime = date('Y-m-d H:i:s');
                    $model -> type       = $type;
                    $model -> userid     = $v;
                    $model -> exituserid = $userid;
                    $model -> save();
                }
            }
        }
        if($type == 3){
            //logid  处理状态，处理了谁
            $loginfo = UNotice::find()->where(['id'=>$logid])->one();
            //处理状态
            if(!empty($loginfo)){
                if($loginfo['status'] == 1){
                    $dostatus = '同意了';
                }else if($loginfo['status'] == 2){
                    $dostatus = '拒绝了';
                }
            }
            //处理了谁
            $touserinfo      = MUserinfo::userInfo($loginfo['userid']);
            $tousername      = !empty($userinfo) ? $touserinfo[0]['s_username'] : '';
            //修改申请的消息
            UUsernotices::updateAll(['content' => $username.$dostatus.$tousername.'加入'.$projectname,'createtime'=>date('Y-m-d H:i:s'),'type'=>$type], ['logid'=>$logid]);
            //反馈申请人的通知
            $usermodel               = clone $_model;
            $usermodel -> content    = $username.$dostatus.'您加入'.$projectname;
            $usermodel -> createtime = date('Y-m-d H:i:s');
            $usermodel -> type       = 4;
            $usermodel -> userid     = $loginfo['userid'];
            $usermodel -> logid      = $logid;
            $usermodel -> save();
        }
        return true;
    }
    /**
     * @todo 通知消息列表
     */
    public static function getList($userid){
        $infolist = UUsernotices::find()
                ->joinwith('uNotice')
                ->where(['u_usernotices.userid'=>$userid])
                ->orderBy(['u_usernotices.createtime'=>SORT_DESC])
                ->asarray()
                ->all();
        if(empty($infolist)){
            $messarr = '';
        }
        foreach($infolist as $k=>$v){
            $messarr[$k]['messageid']  = $v['id'];          #通知id
            $messarr[$k]['content']    = $v['content'];     #通知内容
            $messarr[$k]['type']       = $v['type'];        #1：申请加入项目；2：退出项目 3：处理结果 4:返回给用户的结果
            $messarr[$k]['createtime'] = $v['createtime'];  #创建通知时间
            $messarr[$k]['logid']      = $v['logid'];       #记录id
            $messarr[$k]['status']     = !empty($v['uNotice']) ? $v['uNotice'][0]['status'] : ''; #处理记录的状态 0未处理 1.已同意  2已拒绝
            if($messarr[$k]['type'] == 1){
                //通知来自谁
                $fromuserid = !empty($v['uNotice']) ? $v['uNotice'][0]['userid'] : '';
            }
            if($messarr[$k]['type'] == 2){
                //通知来自谁
                $fromuserid = $v['exituserid'];
            }
            if($messarr[$k]['type'] == 3 || $messarr[$k]['type'] == 4){
                //通知来自谁
                $fromuserid = !empty($v['uNotice']) ? $v['uNotice'][0]['adminid'] : '';
            }
            $userinfo                  = MUserinfo::userInfo($fromuserid);
            $messarr[$k]['fromname']   = $userinfo[0]['s_username'];
            $messarr[$k]['fromimg']    = $userinfo[0]['s_avatar'];
        }
        return $messarr;
    }
}
