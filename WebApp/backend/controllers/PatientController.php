<?php

namespace backend\controllers;

use app\models\Commonfun;
use app\models\FForm;
use app\models\PatientCollect;
use app\models\PatientShare;
use app\models\PProjectUser;
use Yii;
use app\models\PPatientFile;
use app\models\PPatientRemark;
use app\models\PatientBase;
use app\models\PatientData;
use app\models\QCompany;
use app\models\UUserLogin;
use app\models\PatientFilter;
class PatientController extends BaseController
{
    /**
     * 创建患者
     * @param patientid(创建是为空，编辑时有值)
     * @param name
     * @param sex
     * @param birthday
     * @param phone
     * @param address
     * @param ice_person
     * @param ice_relation
     * @param ice_phone
     * @param projectid
     * @param userid
     * @param sourcedata
     * @param formid
     * @param status
     * @param fileinfo [{"filename":"123.jpg","fileurl":"www.we.com","type":"1","size":"1234"}]
     * @param remark [{"content":"这个不用了"},{"content":"haohao"}]
     * @return string
     */
     public function actionCreatepatient(){
         self::checkParamIsEmpty(
             [
                 'name'      => '患者姓名不能为空',
                 'sex'       => '患者性别不能为空',
                 'birthday'  => '患者生日不能为空',
                 'projectid' => '项目id不能为空',
                 'userid'    => '创建者不能为空',
                 'status'    => '状态不能为空',
                 'formid'    => '表单id不能为空',

             ]
         );
         $userid = Yii::$app->getRequest()->getBodyParam('userid');
         // 权限身份验证
         $this   ->checkAuth($userid);
         //创建基本信息 返回患者id
         $patientid = PatientBase::editBase();
         //创建记录 返回记录id
         $recordid  = PatientData::createRecord($patientid);
         //创建附件
         PPatientFile::addFile($patientid,$recordid);
         //创建备注
         PPatientRemark::addRemark($patientid,$recordid);
         return $this->Rsuccess('成功');
     }
     /**
      * 患者数据
      * @param userid
      * @param patientid
      * @return string
      */
     public function actionPatientinfo(){
         self::checkParamIsEmpty(
             [
                 'userid'    => '用户id不能为空',
                 'patientid' => '患者id不能为空',

             ]
         );
         $userid    = Yii::$app->getRequest()->getBodyParam('userid');
         $patientid = Yii::$app->getRequest()->getBodyParam('patientid');
         // 权限身份验证
         $this   ->checkAuth($userid);
         $result = PatientData::patientInfo($userid,$patientid);
         return $this->Rsuccess('患者信息：',$result);
     }
     /**
      * 患者列表+全部+草稿+收藏+近30天
      * @param userid
      * @param projectid
      * @param page
      * @param search（all,draft,collect,time,过滤器id）
      * @param searchname
      * @return string
      */
     public function actionPatientlist(){
         $domain  = $this->getDomain();
         $company = QCompany::domainInfo($domain);
         if(empty($company)){
             return $this->renderPartial('page404');
         }
         self::checkParamIsEmpty(
             [
                 'userid'    => '用户id不能为空',
                 'projectid' => '项目id不能为空',
                 'page'      => '当前页不能为空',

             ]
         );
         $userid        = Yii::$app->getRequest()->getBodyParam('userid');
         $projectid     = Yii::$app->getRequest()->getBodyParam('projectid');
         $page          = Yii::$app->getRequest()->getBodyParam('page');
         $search        = Yii::$app->getRequest()->getBodyParam('search');
         $searchname    = Yii::$app->getRequest()->getBodyParam('searchname');
         // 权限身份验证
         $this   ->checkAuth($userid);
         if($search == ''){
             $search = 'time';
         }
         $result = PatientBase::patientList($userid,$projectid,$page,$search,$searchname);
         return $this->Rsuccess('患者信息：',$result);
     }
     /**
      * 锁定/解锁患者
      * @param userid
      * @param projectid
      * @param patientid多个用,隔开
      * @param status(1=解锁；2=锁定)
      * @return string
      */
     public function actionUplock(){
         $domain  = $this->getDomain();
         $company = QCompany::domainInfo($domain);
         if(empty($company)){
             return $this->renderPartial('page404');
         }
         self::checkParamIsEmpty(
             [
                 'userid'    => '用户id不能为空',
                 'projectid' => '项目id不能为空',
                 'patientid' => '患者id不能为空',
                 'status'    => '状态不能为空'
             ]
         );
         $userid    = Yii::$app->getRequest()->getBodyParam('userid');
         $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
         $patientid = Yii::$app->getRequest()->getBodyParam('patientid');
         $status    = Yii::$app->getRequest()->getBodyParam('status');
         if(!in_array($status,[1,2])){
             return $this->Error('参数错误','',101);
         }
         // 权限身份验证
         $this   ->checkAuth($userid);
         //用户是否为项目管理员
         $projectadmin = PProjectUser::userInfo($userid,$projectid);
         if(empty($projectadmin) || $projectadmin['permission'] != 1){
             return $this->Error('权限不足','','102');
         }
         //修改锁定状态
         PatientBase::upLock($patientid,$status);
         return $this->Rsuccess('操作成功');
     }
     /**
      * 收藏/取消收藏
      * @param userid
      * @param patientid
      * @param projectid
      * @param status(1=收藏；2=取消收藏)
      * @return string
      */
     public function actionUpcollect(){
         self::checkParamIsEmpty(
             [
                 'userid'    => '用户id不能为空',
                 'projectid' => '项目id不能为空',
                 'patientid' => '患者id不能为空',
                 'status'    => '状态不能为空'
             ]
         );
         $userid    = Yii::$app->getRequest()->getBodyParam('userid');
         $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
         $patientid = Yii::$app->getRequest()->getBodyParam('patientid');
         $status    = Yii::$app->getRequest()->getBodyParam('status');
         if(!in_array($status,[1,2])){
             return $this->Error('参数错误','',101);
         }
         // 权限身份验证
         $this   ->checkAuth($userid);
         $result = PatientCollect::upCollect($userid,$projectid,$patientid,$status);
         if($result){
             return $this->Rsuccess('操作成功');
         }

     }
     /**
      * 下载患者附件
      * @param userid
      * @param documentid
      * @return string
      */
     public function actionDownfile(){
         $userid      = Yii::$app->request->get('userid');
         $documentid  = Yii::$app->request->get('documentid');
         if($userid == '' || $documentid == ''){
             return $this->Error('参数不可以为空');
         }
         //文档信息
         $fileinfo   = PPatientFile::fileInfo($documentid);
         $url        = 'http://'.$fileinfo['fileurl'];
         $outfile    = $fileinfo['name'];
         $wrstr = htmlspecialchars_decode(file_get_contents($url));
         header('Content-type: application/octet-stream; charset=utf8');
         Header("Accept-Ranges: bytes");
         header('Content-Disposition: attachment; filename='.$outfile);
         echo $wrstr;
         exit();
     }
     /**
      * 分享患者
      * @param userid
      * @param patientid多个用，隔开
      * @param projectid
      * @param tomail多个用，隔开
      * @return string
      */
     public function actionSharepatient(){
         self::checkParamIsEmpty(
             [
                 'userid'    => '用户id不能为空',
                 'projectid' => '项目id不能为空',
                 'patientid' => '患者id不能为空',
                 'tomail'    => '邮箱不能为空'
             ]
         );
         $userid    = Yii::$app->getRequest()->getBodyParam('userid');
         $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
         $patientid = Yii::$app->getRequest()->getBodyParam('patientid');
         $tomail    = Yii::$app->getRequest()->getBodyParam('tomail');
         // 权限身份验证
         $this   ->checkAuth($userid);
         PatientShare::sharePatient($userid,$projectid,$patientid,$tomail);
         return $this->Rsuccess('成功');
     }
    /**
     * 患者分享清单
     * @param userid
     * @param patientid
     * @return string
     */
    public function actionSharelist(){
        self::checkParamIsEmpty([
                'userid'    => '用户id不能为空',
                'patientid' => '患者id不能为空',
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $patientid = Yii::$app->getRequest()->getBodyParam('patientid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = PatientShare::shareList($patientid);
        return $this->Rsuccess('分享清单',$result);
    }
    /**
     * 删除患者
     * @param userid
     * @param patientid(多个用,隔开)
     * @return bool
     */
    public function actionDeletepatient(){
        self::checkParamIsEmpty([
                'userid'    => '用户id不能为空',
                'patientid' => '患者id不能为空',
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $patientid = Yii::$app->getRequest()->getBodyParam('patientid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        PatientBase::delPatient($patientid);
        return $this->Rsuccess('嘿嘿~删除成功了');
    }
    /**
     * 导出患者
     * @param userid
     * @param patientid(勾选的时候传这个东西，多个,隔开就还好了哦亲亲)
     * @param projectid
     * @param search（all,draft,collect,time,过滤器id）
     * @param searchname
     */
    public function actionExportpatient(){
        $userid     = Yii::$app->request->get('userid');
        $patientid  = Yii::$app->request->get('patientid');
        $projectid  = Yii::$app->request->get('projectid');
        $search     = Yii::$app->request->get('search');
        $searchname = Yii::$app->request->get('searchname');
        if($userid == '' || $projectid == ''){
            return $this->Error('参数不可以为空');
        }
//        $header         = Yii::$app->getRequest()->getHeaders();
//        $authinfo       = self::_checkToken($header['token']);
//        $userarr        = UUserLogin::userLogin($userid,$header['token']);
//        if($userid != $authinfo["userid"] || empty($userarr)){
//            return $this->Error('请正确登录');
//        }
        PatientBase::exportPatient($patientid,$projectid,$userid,$search,$searchname);
        return $this->Rsuccess('导出成功');
    }
    /**
     * 筛选器字段
     * @param userid
     * @param projectid
     * @return string
     */
    public function actionFiltertitle(){
        self::checkParamIsEmpty([
                'userid'    => '用户id不能为空',
                'projectid' => '项目id不能为空',
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = FForm::getTitle($projectid,$userid);
        return $this->Rsuccess('字段：',$result);
    }
    /**
     * 用户某个项目中模板的某个title对应的所有患者的value
     * @param userid
     * @param projectid
     * @param title
     * @param type
     * @return string
     */
    public function actionFiltervalue(){
        self::checkParamIsEmpty([
                'userid'    => '用户id不能为空',
                'projectid' => '项目id不能为空',
                'title'     => 'title不能为空',
                'type'      => '类型不能为空',
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        $title     = Yii::$app->getRequest()->getBodyParam('title');
        $type      = Yii::$app->getRequest()->getBodyParam('type');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = PatientData::getValue($userid,$projectid,$title,$type);
        return $this->Rsuccess('值：',$result);
    }
    /**
     * 创建筛选器
     * @param userid
     * @param projectid
     * @param name
     * @param title
     * @param type  时间
     * @param value
     * @param start_time
     * @param end_time
     * @param time_unit
     * @param time_value
     * @param filter_operator
     */
    public function actionAddfilter(){
        self::checkParamIsEmpty([
                'userid'            => '用户id不能为空',
                'projectid'         => '项目id不能为空',
                'name'              => '名称不能为空',
                'title'             => '字段类型不能为空',
                'type'              => '类型不能为空',
                'value'             => '值不能为空',
                'filter_operator'   => '操作符不能为空',
            ]
        );
        $operator = ['=','!=','>','>=','<','<=','like','not like'];
        if(!in_array(Yii::$app->getRequest()->getBodyParam('filter_operator'),$operator)){
            return $this->Error('参数有误','','1003');
        }
        $result   = PatientFilter::addFilter();
        if($result){
            return $this->Rsuccess('创建成功');
        }else{
            return $this->Error('失败');
        }
    }
    /**
     * 过滤器-运算符
     * @param userid
     * @param type
     * @param value
     * @return string
     */
    public function actionOperators(){
        self::checkParamIsEmpty([
                'type'  => '类型不能为空',
                'value' => '值不能为空',
            ]
        );
        $type  = Yii::$app->getRequest()->getBodyParam('type');
        $value = Yii::$app->getRequest()->getBodyParam('value');
        if($type == 'DATE' || $type == 'DATETIME' || $type == 'TIME' || $type == 'updateTime'){
            if($value == '日期选择'){
                $result = ['>','>=','<','<=','=','!='];
            }
            if($value == '日期区间'){
                $result = ['=','!='];
            }
            if($value == '时间长度'){
                $result = ['>='];
            }
        }elseif ($type == 'updateuser'){
            $result = ['='];
        } else{
            $result = ['=','!=','like'];
        }
        return $this->Rsuccess('操作符',$result);
    }
    /**
     * 过滤器列表
     * @param userid
     * @param projectid
     * @return string
     */
    public function actionFilterlist(){
        self::checkParamIsEmpty([
                'userid'    => '用户id不能为空',
                'projectid' => '项目id不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $projectid = Yii::$app->getRequest()->getBodyParam('projectid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = PatientFilter::filterList($userid,$projectid);
        return $this->Rsuccess('列表',$result);
    }
    /**
     * 过滤器详情
     * @param userid
     * @param filterid
     * @return string
     */
    public function actionFilterinfo(){
        self::checkParamIsEmpty([
                'userid'    => '用户id不能为空',
                'filterid'  => '筛选器id不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $filterid  = Yii::$app->getRequest()->getBodyParam('filterid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        $result = PatientFilter::filterInfo($filterid);
        $result['name'] = $result['filtername'];
        unset($result['filtername']);
        return $this->Rsuccess('详情',$result);
    }
    /**
     * 删除过滤器
     * @param userid
     * @param filterid
     * @return string
     */
    public function actionDelfilter(){
        self::checkParamIsEmpty([
                'userid'    => '用户id不能为空',
                'filterid'  => '筛选器id不能为空'
            ]
        );
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        $filterid  = Yii::$app->getRequest()->getBodyParam('filterid');
        // 权限身份验证
        $this   ->checkAuth($userid);
        PatientFilter::delFilter($userid,$filterid);
        return $this->Rsuccess('成功');
    }

}
