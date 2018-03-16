<?php

namespace app\models;

use Yii;
use app\models\QCompanyUser;
use app\models\PProjectUser;
use app\models\Commonfun;
use app\models\UUserInfo;
use app\models\PApplyToProject;
/**
 * This is the model class for table "p_project".
 *
 * @property string $projectid
 * @property string $projectname
 * @property string $projectmem
 * @property integer $access_type
 * @property integer $status
 * @property string $createtime
 * @property string $updatetime
 * @property string $createuserid
 * @property string $companyid
 * @property integer $noproject_access_admin
 * @property integer $noproject_access_alluser
 * @property integer $addfeed_access
 * @property string $updateuserid
 */
class PProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'p_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['projectid'], 'required'],
            [['projectmem'], 'string'],
            [['access_type', 'status','noproject_access_admin','noproject_access_alluser','addfeed_access'], 'integer'],
            [['createtime', 'updatetime'], 'safe'],
            [['projectid', 'createuserid', 'companyid','updateuserid'], 'string', 'max' => 11],
            [['projectname'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'projectid' => 'Projectid',
            'projectname' => 'Projectname',
            'projectmem' => 'Projectmem',
            'access_type' => 'Access Type',
            'status' => 'Status',
            'createtime' => 'Createtime',
            'updatetime' => 'Updatetime',
            'createuserid' => 'Createuserid',
            'companyid' => 'Companyid',
            'noproject_access_admin'  => 'Noproject Access Admin',
            'noproject_access_alluser'  => 'Noproject Access Alluser',
            'addfeed_access'  => 'Addfeed Access',
        ];
    }

    /**
     * @todo 创建项目
     */
    public static function createProject($userid,$name,$accesstype,$companyid,$mem=''){
        //用户是否为企业管理员
        $permission     = QCompanyUser::userPermission($userid,$companyid);
        #type 1:创建成功。2：没资格3。创建失败
        $result['type'] = 1;
        if(empty($permission) || $permission['permission']!=1){
            #用户没资格创建项目
            $result['type'] = 2;
            return $result;
        }else{
            #创建项目
            $model = new PProject();
            $model -> projectid     = Commonfun::randpw();
            $model -> projectname   = $name;
            $model -> projectmem    = $mem;
            $model -> access_type   = $accesstype;
            $model -> status        = 1;
            $model -> createtime    = date('Y-m-d H:i:s');
            $model -> updatetime    = date('Y-m-d H:i:s');
            $model -> createuserid  = $userid;
            $model -> companyid     = $companyid;
            if($model ->save()){
//                #添加项目成员(这个企业的管理员会成为项目的管理员)
//                $admins = QCompanyUser::companyAdmins($companyid);
//                foreach($admins as $k=>$v){
                    PProjectUser::addUser($userid,$model->projectid,1,$companyid);
                //}
                $result['projectid'] = $model->projectid;
                return $result;
            }else{
                return $result['type'] = 3;
            }
        }
    }
    /**
     * 管理员项目列表
     * @param userid
     * @param companyid
     * @param page
     * @param type
     * @param search
     * @return string
     */
    public static function adminProjectList($userid,$companyid,$page,$type,$search){
        $result  = ['data'=>[],'count'=>0];
        //是否为企业管理员
        $isadmin = QCompanyUser::userPermission($userid,$companyid);
        if(empty($isadmin) || $isadmin['permission'] != 1){
            #没有这个人，或者这个人不是管理员
            return $result;
        }
        $start   = $page-1 <= 0 ? 0 : ($page-1) * 10;
        #全部项目
        if($type == 'all'){
            $status = 1;
        }
        #归档的项目
        if($type == 'closed'){
            $status = 2;
        }
        #已删除的项目
        if($type == 'delete'){
            $status = 3;
        }
        $dataob = PProject::find()
                ->select('projectid,projectname,access_type,status,createtime')
                ->where(['companyid'=>$companyid,'status'=>$status]);
        if($search != ''){
            $dataob ->andWhere(['like','projectname',$search]);
        }
        $data   = $dataob->offset($start)
                ->limit(10)
                ->orderby(['createtime'=>SORT_DESC])
                ->asarray()
                ->all();
        //项目管理员
        foreach ($data as $k=>$v){
            $data[$k]['admin'] = PProjectUser::adminInfo($v['projectid']);
        }
        $result['data']  = $data;
        $countob =PProject::find()
                ->select('projectid')
                ->where(['companyid'=>$companyid,'status'=>$status]);
        if($search != ''){
            $countob ->andWhere(['like','projectname',$search]);
        }
        $result['count'] = $countob ->count();
        return $result;
    }
    /**
     * 项目详情
     * @param userid
     * @param projectid
     * @return array
     */
    public static function projectInfo($userid,$projectid){
        //更新访问时间
        PProjectUser::updateAll(['access_time'=>date('Y-m-d H:i:s')],['userid'=>$userid,'projectid'=>$projectid]);
        $result = PProject::find()
                ->select('projectid,projectname,projectmem,access_type,status,createtime,companyid,noproject_access_admin,noproject_access_alluser,addfeed_access,updatetime,updateuserid')
                ->where(['projectid'=>$projectid])
                ->andWhere(['in','status',[1,2,3]])
                ->asArray()
                ->one();
        #用户是否已加入项目
        $isprojectuser = PProjectUser::userInfo($userid,$projectid);
        if(!empty($isprojectuser)){
            $result['isprojectuser'] = 2;   #已加入项目
            $result['permission']    = $isprojectuser['permission'];
            //项目对于用户的快速访问状态
            $result['quick_status']  = $isprojectuser['quick_status'];
            //置顶数
            $result['top_num']       = PProjectUser::fastList($userid,1);
        }else{
            #用户是否申请过
            $isapply   = PApplyToProject::applyInfo($userid,$projectid);
            if(empty($isapply)){
                $result['isprojectuser'] = 4;   #没申请过，还没加入
            }else if($isapply['status'] == 1){
                $result['isprojectuser'] = 1;   #申请中
            }else if($isapply['status'] == 3){
                $result['isprojectuser'] = 3;   #已拒绝
            }
            $result['permission']    = '';
            $result['quick_status']  = '';
            //置顶数
            $result['top_num']       = PProjectUser::fastList($userid,1);
        }
        //项目成员个数
        $result['usercount']    = PProjectUser::countUser($projectid);
        //更新人
        $result['upusername']   = UUserInfo::userInfo($result['updateuserid'])['s_username'];
        return $result;
    }
    /**
     * 操作状态
     * @param userid
     * @param projectid
     * @param status
     * @return bool
     */
    public static function doStatus($userid,$status,$projectid){
        //项目所在企业
        $company    = PProject::projectByid($projectid);
        //用户是否为企业的管理员
        $iscadmin   = QCompanyUser::userPermission($userid,$company['companyid']);
        //用户是否为项目管理员
        $ispadmin   = PProjectUser::userInfo($userid,$projectid);
        $cadmin     = isset($iscadmin['permission']) ? $iscadmin['permission']:2;
        $padmin     = isset($ispadmin['permission']) ? $ispadmin['permission']:2;
        if($cadmin == 1 || $padmin == 1){
            $model = PProject::findOne(['projectid'=>$projectid]);
            $model -> status       = $status;
            $model -> updatetime   = date('Y-m-d H:i:s');
            $model -> updateuserid = $userid;
            $model -> save();
            if($status == 4){
                PProjectUser::updateAll(['status'=>2],['projectid'=>$projectid]);
            }
            return true;
        }
    }
    /**
     * 项目详情（项目id）
     * @param projectid
     * @return array
     */
    public static function projectByid($projectid){
        $result = PProject::find()
                ->select('projectid,projectname,access_type,companyid,addfeed_access,noproject_access_admin,noproject_access_alluser,status')
                ->where(['projectid'=>$projectid])
                ->asarray()
                ->one();
        return $result;
    }

    /**
     * @param $projectid
     * @return bool
     */
    public static function upProject($projectid){
        $model = PProject::findOne(['projectid'=>$projectid]);
        $model -> projectname               = Yii::$app->getRequest()->getBodyParam('name');
        $model -> projectmem                = Yii::$app->getRequest()->getBodyParam('mem');
        $model -> access_type               = Yii::$app->getRequest()->getBodyParam('accesstype');
        $model -> noproject_access_admin    = Yii::$app->getRequest()->getBodyParam('noproject_access_admin');
        $model -> noproject_access_alluser  = Yii::$app->getRequest()->getBodyParam('noproject_access_alluser');
        $model -> addfeed_access            = Yii::$app->getRequest()->getBodyParam('addfeed_access');
        $model -> updatetime                = date('Y-m-d H:i:s');
        $model -> save();
        return true;
    }
}
