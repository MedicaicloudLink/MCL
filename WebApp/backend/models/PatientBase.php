<?php

namespace app\models;

use Yii;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Cell;

/**
 * This is the model class for table "patient_base".
 *
 * @property string $patientid
 * @property string $name
 * @property integer $sex
 * @property string $birthday
 * @property string $phone
 * @property string $address
 * @property string $id
 * @property string $ice_person
 * @property integer $ice_relation
 * @property string $ice_phone
 * @property string $projectid
 * @property string $createtime
 * @property string $createuser
 * @property string $updatetime
 * @property string $updateuser
 * @property string $status
 * @property string $islock
 */
class PatientBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient_base';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['patientid'], 'required'],
            [['sex', 'ice_relation','status','islock'], 'integer'],
            [['birthday', 'createtime', 'updatetime'], 'safe'],
            [['patientid', 'projectid', 'createuser', 'updateuser'], 'string', 'max' => 11],
            [['name', 'ice_person'], 'string', 'max' => 25],
            [['phone', 'ice_phone'], 'string', 'max' => 20],
            [['address'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'patientid' => 'Patientid',
            'name' => 'Name',
            'sex' => 'Sex',
            'birthday' => 'Birthday',
            'phone' => 'Phone',
            'address' => 'Address',
            'id' => 'ID',
            'ice_person' => 'Ice Person',
            'ice_relation' => 'Ice Relation',
            'ice_phone' => 'Ice Phone',
            'projectid' => 'Projectid',
            'createtime' => 'Createtime',
            'createuser' => 'Createuser',
            'updatetime' => 'Updatetime',
            'updateuser' => 'Updateuser',
            'status' => 'Status',
            'islock' => 'Islock',
        ];
    }
    /**
     * 关联用户表
     */
    public function getUUserInfo(){
        return $this->hasMany(UUserInfo::className(), ['s_userid'  => 'updateuser']);
    }
    /**
     * 关联患者数据表
     */
    public function getPatientData(){
        return $this->hasMany(UUserInfo::className(), ['patientid'  => 'patientid']);
    }
    /**
     * 编辑患者基本信息
     *
     */
    public static function editBase(){
        $patientid = Yii::$app->getRequest()->getBodyParam('patientid');
        $userid    = Yii::$app->getRequest()->getBodyParam('userid');
        if($patientid == ''){
            $model = new PatientBase();
            $model ->patientid  = Commonfun::randpw();
            $model ->createuser = $userid;
            $model ->createtime = date('Y-m-d H:i:s');
        }else{
            $model = PatientBase::findOne(['patientid'=>$patientid]);
        }
        $model ->name         = Yii::$app->getRequest()->getBodyParam('name');
        $model ->sex          = Yii::$app->getRequest()->getBodyParam('sex');
        $model ->birthday     = Yii::$app->getRequest()->getBodyParam('birthday');
        $model ->phone        = Yii::$app->getRequest()->getBodyParam('phone');
        $model ->address      = Yii::$app->getRequest()->getBodyParam('address');
        $model ->ice_person   = Yii::$app->getRequest()->getBodyParam('ice_person');
        $model ->ice_relation = Yii::$app->getRequest()->getBodyParam('ice_relation');
        $model ->ice_phone    = Yii::$app->getRequest()->getBodyParam('ice_phone');
        $model ->projectid    = Yii::$app->getRequest()->getBodyParam('projectid');
        $model ->updatetime   = date('Y-m-d H:i:s');
        $model ->updateuser   = $userid;
        $model ->status       = Yii::$app->getRequest()->getBodyParam('status');
        $model ->save();
        return $model ->patientid;
    }
    /**
     * 患者列表
     * @param $userid
     * @param $projectid
     * @param $page
     * @param $search
     * @param $searchname
     * @return array
     */
    public static function patientList($userid,$projectid,$page,$search,$searchname){
        //判断$search是否为更新人
        if(is_numeric($search)){
            $filterinfo = PatientFilter::filterInfo($search);
            if(!empty($filterinfo) && $filterinfo['type'] == 'updateuser'){
               $search = 'updateuser';
            }
        }
        if($search == 'collect'){
            //收藏的
            $data = PatientCollect::userProjectCollect($userid,$projectid,$page,$searchname);
        }else if($search == 'all' || $search == 'draft' || $search == 'time' || $search =='updateuser'){
            //用户是否为项目管理员
            $projectadmin = PProjectUser::userInfo($userid,$projectid);
            $where        = ['patient_base.projectid'=>$projectid];#公共的条件
            $where1       = []; #普通成员独有的 管理员不需要
            $where2       = []; #搜索患者姓名
            if($searchname != ''){
                $where2   = ['like','name',$searchname]; #搜索患者姓名
            }
            if($search == 'draft'){
                $where3   = ['in','patient_base.status',[2]]; #草稿
            }
            if($search == 'all'){
                $where3   = ['in','patient_base.status',[1,2]]; #全部
            }
            if($search == 'time'){
                $where3   = ['and',['in','patient_base.status',[1,2]],['>','patient_base.updatetime',date('Y-m-d H:i:s',time()-86400*30)]]; #近30天
            }
            if($search == 'updateuser'){
                $where3   = ['u_user_info.s_username'=>$filterinfo['value']]; #更新人
            }
            if(empty($projectadmin) || $projectadmin['permission'] != 1){
                //此用户邮箱
                $mail       = UUserInfo::userInfo($userid)['s_mail'];
                //分享表里有没有此项目中分享给这个人的患者
                $patientid  = PatientShare::sharePatientids($userid,$projectid,$mail);
                //非管理员 在分享表里的和自己创建发
                $where1     = ['or',['in','patient_base.patientid',$patientid],['patient_base.createuser'=>$userid]];
            }
            $start  = $page-1 <= 0 ? 0 : ($page-1) * 15;
            $result = PatientBase::find()
                ->joinWith('uUserInfo')
                ->select('s_username,patient_base.patientid,name,sex,birthday,patient_base.updateuser,patient_base.updatetime,status,islock')
                ->where($where)
                ->andWhere($where1)
                ->andWhere($where2)
                ->andWhere($where3)
                ->orderBy(['patient_base.updatetime'=>SORT_DESC])
                ->offset($start)
                ->limit(15)
                ->asArray()
                ->all();
            foreach ($result as $k=>$v){
                unset($result[$k]['uUserInfo']);
                //患者收藏状态
                $patientCollect = PatientCollect::collectInfo($userid,$projectid,$v['patientid']);
                if(!empty($patientCollect)){
                    $result[$k]['collect'] = 1; #收藏
                }else{
                    $result[$k]['collect'] = 2; #未收藏
                }
            }
            $count  = PatientBase::find()
                    ->joinWith('uUserInfo')
                    ->select('patientid')
                    ->where($where)
                    ->andWhere($where1)
                    ->andWhere($where2)
                    ->andWhere($where3)
                    ->count();
            $data['data']  = $result;
            $data['count'] = $count;
        }else{
            $data = PatientData::filterSearch($userid,$projectid,$page,$search,$searchname,'');
        }
        return $data;
    }
    /**
     * 修改锁定状态
     * @param patientid
     * @param status
     * @return bool
     */
    public static function upLock($patientid,$status){
        $patientidarr = explode(',',$patientid);
        PatientBase::updateAll(['islock'=>$status,'updatetime'=>date('Y-m-d H:i:s')],['in','patientid',$patientidarr]);
        return true;
    }
    /**
     * 患者详情
     * @param patientid
     * @return array
     */
    public static function patientInfo($patientid){
        $result = PatientBase::find()
                ->where(['patientid'=>$patientid])
                ->asArray()
                ->one();
        return $result;
    }
    /**
     * 删除患者
     * @param patientid
     * @return bool
     */
    public static function delPatient($patientid){
        $patientarr = explode(',',$patientid);
        foreach ($patientarr as $k=>$v){
            //患者是否被锁定
            $patientinfo = PatientBase::patientInfo($v);
            if($patientinfo['islock'] == 2){
                unset($patientarr[$k]);
            }
        }
        //把此患者之前的记录都设置为不可用
        PatientData::updateAll(['isuse'=>2],['patientid'=>$patientid]);
        //修改状态
        PatientBase::updateAll(['status'=>3],['in','patientid',$patientarr]);
        //删除分享表里的
        PatientShare::deleteAll(['in','patientid',$patientarr]);
        //删除收藏表里的
        PatientCollect::deleteAll(['in','patientid',$patientarr]);
        return true;
    }
    /**
     * 患者导出
     * @param patientid
     * @param projectid
     * @param userid
     * @param $search
     * @param $searchname
     * @return string
     */
    public static function exportPatient($patientid,$projectid,$userid,$search,$searchname){
        //判断$search是否为更新人
        if(is_numeric($search)){
            $filterinfo = PatientFilter::filterInfo($search);
            if(!empty($filterinfo) && $filterinfo['type'] == 'updateuser'){
                $search = 'updateuser';
            }
        }
        if($patientid != ''){
            //这个当当然就是人家勾选的那几个喽
            $patientids  = explode(',',$patientid);
        }else{
            //加了搜索的哦亲亲

            if($search == 'all' || $search == 'draft' || $search == 'time'){
                //用户是否为项目管理员
                $projectadmin = PProjectUser::userInfo($userid,$projectid);
                $where        = ['patient_base.projectid'=>$projectid];#公共的条件
                $where1       = []; #普通成员独有的 管理员不需要
                $where2       = []; #搜索患者姓名
                if($searchname != ''){
                    $where2   = ['like','name',$searchname]; #搜索患者姓名
                }
                if($search == 'draft'){
                    $where3   = ['in','patient_base.status',[2]]; #草稿
                }
                if($search == 'all'){
                    $where3   = ['in','patient_base.status',[1,2]]; #全部
                }
                if($search == 'time'){
                    $where3   = ['and',['in','patient_base.status',[1,2]],['>','patient_base.updatetime',date('Y-m-d H:i:s',time()-86400*30)]]; #近30天
                }
                if($search == 'updateuser'){
                    $where3   = ['u_user_info.s_username'=>$filterinfo['value']]; #更新人
                }
                if(empty($projectadmin) || $projectadmin['permission'] != 1){
                    //此用户邮箱
                    $mail       = UUserInfo::userInfo($userid)['s_mail'];
                    //分享表里有没有此项目中分享给这个人的患者
                    $patientid  = PatientShare::sharePatientids($userid,$projectid,$mail);
                    //非管理员 在分享表里的和自己创建发
                    $where1     = ['or',['in','patient_base.patientid',$patientid],['patient_base.createuser'=>$userid]];
                }
                $allpatientid = PatientBase::find()
                            ->select('patientid')
                            ->where($where)
                            ->andWhere($where1)
                            ->andWhere($where2)
                            ->andWhere($where3)
                            ->asArray()
                            ->all();
                $patientids = array_column($allpatientid,'patientid');
            }else{
                $patientids = PatientData::filterSearch($userid,$projectid,'',$search,$searchname,1);
            }
        }
        //患者信息
        $patientinfo = PatientData::patientsInfo($patientids);
        //项目中已经发布的所有表单
        $form        = FForm::allPublish($projectid);
        if(!empty($patientinfo)){
            $sheetindex = 0;
            //需要导出的内容
            $objPHPExcel = new PHPExcel();
            foreach ($form as $k3=>$v3){
                $formarr  = json_decode($v3['sourcedata'],true);
                //模板值
                $forminfo = [];
                foreach ($formarr['form'] as $key=>$value){
                    foreach ($value['children'] as $k=>$v){
                        if($v['type'] != 'CAPTION' && $v['type'] != 'updateTime'){
                            $forminfo[] = $v['title'];
                        }
                    }
                }
                if($sheetindex != 0){
                    $objPHPExcel ->createSheet();
                }
                $objPHPExcel ->setActiveSheetIndex($sheetindex);
                //设置工作表名称
                $sheettitle = mb_substr($formarr['formname'],0,19,'utf-8');
                $objPHPExcel ->getActiveSheet($sheetindex)->setTitle($sheettitle);
                //字段
                $count = 0; #列
                foreach($forminfo as $k4=>$v4){
                    //数字转化excel字母
                    $letter = PHPExcel_Cell::stringFromColumnIndex($count);
                    $objPHPExcel->setActiveSheetIndex($sheetindex)->setCellValue($letter.'1', $v4);
                    $count++;
                }
                $num = 2;#计算行 从第二行开始
                foreach ($patientinfo as $k2=>$v2){
                    if($v2['formid'] == $v3['id']){
                        $let = 0; #计算字母 列
                        $data = json_decode($v2['sourcedata'],true);
                        foreach ($data['form'] as $k5=>$v5){
                            foreach ($v5['children'] as $k6=>$v6){
                                if($v6['type'] != 'CAPTION' && $v6['type'] != 'updateTime'){
                                    if(is_array($v6['value'])){
                                        $v6['value'] = implode(',',$v6['value']);
                                    }
                                    $letter      = PHPExcel_Cell::stringFromColumnIndex($let);
                                    $objPHPExcel ->setActiveSheetIndex($sheetindex)->setCellValue($letter.$num, $v6['value']);
                                    $let++;
                                }
                            }
                        }
                        $num++;
                    }
                }
                $sheetindex++;
            }
        }
        $objPHPExcel->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="statistics_patient.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

}
