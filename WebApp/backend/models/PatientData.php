<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "patient_data".
 *
 * @property string $id
 * @property string $recordid
 * @property string $patientid
 * @property string $sourcedata
 * @property string $createtime
 * @property string $createuser
 * @property string $formid
 * @property integer $status
 * @property string $updatetime
 * @property string $updateuser
 * @property string $projectid
 */
class PatientData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sourcedata'], 'string'],
            [['createtime', 'updatetime'], 'safe'],
            [['status'], 'integer'],
            [['recordid', 'patientid', 'createuser', 'formid', 'updateuser','projectid'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'recordid' => 'Recordid',
            'patientid' => 'Patientid',
            'sourcedata' => 'Sourcedata',
            'createtime' => 'Createtime',
            'createuser' => 'Createuser',
            'formid' => 'Formid',
            'status' => 'Status',
            'updatetime' => 'Updatetime',
            'updateuser' => 'Updateuser',
            'projectid' => 'Projectid',
        ];
    }
    /**
     * 关联患者表
     */
    public function getPatientBase(){
        return $this->hasMany(PatientBase::className(), ['patientid'  => 'patientid']);
    }
    /**
     * 创建患者记录
     * @param patientid
     * @return string
     */
    public static function createRecord($patientid){
        //把此患者之前的记录都设置为不可用
        PatientData::updateAll(['isuse'=>2],['patientid'=>$patientid]);
        $model = new PatientData();
        $model ->recordid   = Commonfun::randpw();
        $model ->patientid  = $patientid;
        $model ->sourcedata = Yii::$app->getRequest()->getBodyParam('sourcedata');
        $model ->createtime = date('Y-m-d H:i:s');
        $model ->createuser = Yii::$app->getRequest()->getBodyParam('userid');
        $model ->formid     = Yii::$app->getRequest()->getBodyParam('formid');
        $model ->status     = Yii::$app->getRequest()->getBodyParam('status');
        $model ->updatetime = date('Y-m-d H:i:s');
        $model ->updateuser = Yii::$app->getRequest()->getBodyParam('userid');
        $model ->projectid  = Yii::$app->getRequest()->getBodyParam('projectid');
        $model ->save();
        return $model ->recordid;
    }
    /**
     * 患者信息
     * @param userid
     * @param patientid
     * @return array
     */
    public static function patientInfo($userid,$patientid){
        $result = PatientData::find()
                ->joinWith('patientBase')
                ->select('patient_base.patientid,name,sex,birthday,phone,address,ice_person,ice_relation,ice_phone,patient_base.projectid,patient_base.updatetime,patient_base.updateuser,patient_data.recordid,sourcedata,formid,patient_data.status,islock')
                ->where(['patient_data.patientid'=>$patientid])
                ->orderBy(['patient_data.updatetime'=>SORT_DESC])
                ->limit(1)
                ->asArray()
                ->one();
        unset($result['patientBase']);
        //更新人
        $result['username']   = UUserInfo::userInfo($result['updateuser'])['s_username'];;
        //表单信息
        $result['formdata']   = FForm::formInfo($result['formid'])['sourcedata'];
        //附件
        $result['filelist']   = PPatientFile::fileList($result['recordid']);
        //备注
        $result['remarklist'] = PPatientRemark::remarkList($result['recordid']);
        //患者收藏状态
        $patientCollect       = PatientCollect::collectInfo($userid,$result['projectid'],$patientid);
        if(!empty($patientCollect)){
            $result['collect'] = 1; #收藏
        }else{
            $result['collect'] = 2; #未收藏
        }
        return $result;
    }
    /**
     * 多个患者详情
     * @param patientid
     * @return array
     */
    public static function patientsInfo($patientids){
        $result = PatientData::find()
                ->select('patientid,sourcedata,formid,recordid')
                ->where(['in','patientid',$patientids])
                ->andWhere(['isuse'=>1])
                ->orderBy(['updatetime'=>SORT_ASC])
                ->asArray()
                ->all();
        return $result;
    }
    /**
     * 用户某个项目中模板的某个title对应的所有患者的value
     * @param $userid
     * @param $projectid
     * @param $title
     * @param $type
     * @return array
     */
    public static function getValue($userid,$projectid,$title,$type)
    {
        if($type == 'DATE' || $type == 'DATETIME' || $type == 'TIME' || $type == 'updateTime'){
            $value = ['日期区间','日期选择','时间长度'];
        }else{
            //用户是否为项目管理员
            $projectadmin = PProjectUser::userInfo($userid, $projectid);
            $where        = '1=1'; #普通成员独有的 管理员不需要
            if (empty($projectadmin) || $projectadmin['permission'] != 1) {
                //此用户邮箱
                $mail      = UUserInfo::userInfo($userid)['s_mail'];
                //分享表里有没有此项目中分享给这个人的患者
                $patientid = PatientShare::sharePatientids($userid, $projectid, $mail);
                //非管理员 在分享表里的和自己创建发
                $patientid = implode(',',$patientid);
                if($patientid == ''){
                    $where = "createuser = '".$userid."'";
                }else{
                    $where = "createuser = '".$userid."' OR patientid in (".$patientid.")";
                }
            }
            if($title == '更新人' && $type == 'updateuser'){
                $upuser = PProjectUser::allMember($projectid);
                $value  = array_column($upuser,'s_username');
                return $value;
            }
            $othervalue = '';
            if($type == 'RADIO'){
                $othervalue = ",json_array_elements (
                                    json_array_elements (sourcedata -> 'form') #> '{children}'
                                ) #>> '{otherValue}' AS othervalue";
            }
            //患者数据
            $result = Yii::$app->db->createCommand("SELECT
                     *
                    FROM
                        (
                            SELECT projectid,updatetime,isuse,createuser,patientid,
                                json_array_elements (
                                    json_array_elements (sourcedata -> 'form') #> '{children}'
                                ) #>> '{value}' AS VALUE,
                                json_array_elements (
                                    json_array_elements (sourcedata -> 'form') #> '{children}'
                                ) #>> '{title}' AS title $othervalue
                            FROM
                                patient_data
                        ) T
                    WHERE(
                            title :: VARCHAR = '".$title."'
                        AND
                            projectid = '".$projectid."'
                        AND 
                            isuse = 1
                    )
                    AND(
                            $where  
                    )                
                    ORDER BY
                        updatetime DESC")->queryAll();
            $value = [];
            foreach ($result as $k=>$v){
                if(!in_array($v['value'],$value) && $v['value']!=''){
                    $value[] = $v['value'];
                }
                if($type == 'RADIO'){
                    if(!in_array($v['othervalue'],$value) && $v['othervalue']!=''){
                        $value[] = $v['othervalue'];
                    }
                }
            }
        }
        #多选
        if($type == 'CHECKBOXES'){
            $check = [];
            foreach ($value as $k1=>$v1){
                $v1 = json_decode($v1,true);
                if(!empty($v1)){
                    foreach ($v1 as $k2=>$v2){
                        if(!in_array($v2,$check)){
                            $check[] = $v2;
                        }
                    }
                }
            }
            $value = $check;
        }
        #地址
        if($type == 'ADDRESS'){
            $address = [];
            foreach ($value as $k1=>$v1){
                $v1 = json_decode($v1,true);
                if(!empty($v1)){
                    foreach ($v1 as $k2=>$v2){
                        if(!in_array($v1['province'].$v1['city'].$v1['text'],$address) && $v1['province']!= ''){
                            $address[] = $v1['province'].$v1['city'].$v1['text'];
                        }
                    }
                }
            }
            $value = $address;
        }
        return $value;
    }
    /**
     * 过滤出来的患者
     * @param $userid
     * @param $projectid
     * @param $page
     * @param $filterid
     * @param $searchname
     * @param type=1时是导出
     * @return array
     */
    public static function filterSearch($userid,$projectid,$page=null,$filterid,$searchname,$type){
        //过滤条件
        $filter       = PatientFilter::filterInfo($filterid);
        $filterwhere  = "AND value :: VARCHAR ".$filter['filter_operator']." '".$filter['value']."'";
        //包含不包含特殊处理
        if($filter['filter_operator'] == 'not like' || $filter['filter_operator'] == 'like'){
            $filterwhere = " AND value :: VARCHAR ".$filter['filter_operator']." '%".$filter['value']."%'";
        }
        //日期选择
        if($filter['start_time'] != '' && $filter['value'] == '日期选择'){
            $filterwhere  = "AND extract(epoch FROM date(value)) ".$filter['filter_operator']." extract(epoch FROM date('".$filter['start_time']."'))";
        }
        //日期区间
        if($filter['start_time'] != '' && $filter['value'] == '日期区间' && $filter['end_time'] != ''){
            if($filter['filter_operator'] == '='){
                $filter['filter_operator'] = 'between';
            }else{
                $filter['filter_operator'] = 'not between';
            }
            $filterwhere  = "AND extract(epoch FROM date(value)) ".$filter['filter_operator']." extract(epoch FROM date('".$filter['start_time']."')) and extract(epoch FROM date('".$filter['end_time']."'))";
        }
        //时间长度
        if($filter['time_unit'] != '' && $filter['value'] == '时间长度'){
            if($filter['time_unit'] == '年'){
                $s = '31536000'; #365天
            }
            if($filter['time_unit'] == '月'){
                $s = '2678400'; #31天
            }
            if($filter['time_unit'] == '天'){
                $s = '86400'; #1天
            }
            $filterwhere = " AND extract(epoch FROM date(value)) ".$filter['filter_operator']." extract(epoch FROM date(current_timestamp))-$s*".$filter["time_value"]."";
        }
        //用户是否为项目管理员
        $projectadmin = PProjectUser::userInfo($userid,$projectid);
        $where        = ['patient_base.projectid'=>$projectid];#公共的条件
        $where1       = []; #普通成员独有的 管理员不需要
        $where2       = []; #搜索患者姓名
        if($searchname != ''){
            $where2     = ['like','name',$searchname]; #搜索患者姓名
        }
        if(empty($projectadmin) || $projectadmin['permission'] != 1){
            //此用户邮箱
            $mail       = UUserInfo::userInfo($userid)['s_mail'];
            //分享表里有没有此项目中分享给这个人的患者
            $patientid  = PatientShare::sharePatientids($userid,$projectid,$mail);
            //非管理员 在分享表里的和自己创建发
            $where1     = ['or',['in','patient_base.patientid',$patientid],['patient_base.createuser'=>$userid]];
        }
        $allpatient = PatientBase::find()
                ->select('patientid')
                ->where($where)
                ->andWhere(['in','status',[1,2]])
                ->andWhere($where1)
                ->andWhere($where2)
                ->asArray()
                ->all();
        $patientid = array_column($allpatient,'patientid');
        $patientid = implode("','",$patientid);
        $patientid = "'".$patientid."'";
        //过滤出的
        $filresult = Yii::$app->db->createCommand("SELECT
                    *
                FROM
                    (
                        SELECT
                            isuse,
                            patientid,
                            json_array_elements (
                                json_array_elements (sourcedata -> 'form') #> '{children}'
                            ) #>> '{value}' AS value
                            ,
                        json_array_elements (
                                json_array_elements (sourcedata -> 'form') #> '{children}'
                            ) #>> '{otherValue}' AS othervalue
                            ,
                            json_array_elements (
                                json_array_elements (sourcedata -> 'form') #> '{children}'
                            ) #>> '{title}' AS title
                        FROM
                            patient_data
                    ) T
                WHERE
                    (
                        title :: VARCHAR = '".$filter['title']."'
                        AND isuse = 1
                        AND patientid in ($patientid)
                        $filterwhere
                    )
                ")->queryAll();
        $finish_patientid = array_column($filresult,'patientid');
        if($type == 1){
            return $finish_patientid;
        }
        $start  = $page-1 <= 0 ? 0 : ($page-1) * 15;
        $result = PatientBase::find()
            ->joinWith('uUserInfo')
            ->select('s_username,patient_base.patientid,name,sex,birthday,patient_base.updateuser,patient_base.updatetime,status,islock')
            ->where(['in','patientid',$finish_patientid])
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
        $count         = count($finish_patientid);
        $data['data']  = $result;
        $data['count'] = $count;
        return $data;
    }

}
