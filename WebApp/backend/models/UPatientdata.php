<?php

namespace app\models;

use Yii;
use app\models\UTelemplate;
use app\models\Commonfun;
use app\models\UPatientbase;
use app\models\RNewpatientdata;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Cell;

/**
 * This is the model class for table "u_patientdata".
 *
 * @property integer $id
 * @property string $u_MDID
 * @property string $u_templetid
 * @property string $u_patientdata
 */
class UPatientdata extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_patientdata';
    }

    /**
     * @inheritdoc
     */
//     public function rules()
//     {
//         return [
//             [['u_MDID,u_createuserid,u_templetid,u_createtime,u_updateuserid,u_updatetime','u_recordid'], 'safe'],
//             [['u_patientdata,sourcedata,imgs,remark'], 'string'],
//             [['u_MDID', 'u_templetid'], 'string', 'max' => 32],
//         ];
//     }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'u_MDID' => 'U  Mdid',
            'u_templetid' => 'U Templetid',
            'u_patientdata' => 'U Patientdata',
        ];
    }
    /**
     * @todo 关联患者基本信息表
     */
    public function getUPatientbase(){
        return $this->hasMany(UPatientbase::className(), ['u_MDID' => 'u_MDID']);
    }
    /**
     * @todo 关联模板表
     */
    public function getUTelemplate(){
        return $this->hasMany(UTelemplate::className(), ['u_templateid'=>'u_templetid']);
    }
    
    /**
     * @todo 创建患者数据
     * @param mdid
     * @param templateid
     * @param patientdata
     * @param createuserid
     * return success
     */
    public static function createPatientdata(){
        $model = new UPatientdata();
        $model -> u_MDID         = Yii::$app->getRequest()->getBodyParam('mdid');
        $model -> u_templetid    = Yii::$app->getRequest()->getBodyParam('templateid');
        $model -> u_patientdata  = Yii::$app->getRequest()->getBodyParam('patientdata');
        $model -> u_createuserid = Yii::$app->getRequest()->getBodyParam('createuserid');
        $model -> u_createtime   = date('Y-m-d H:i:s');
        $model -> u_updatetime   = date('Y-m-d H:i:s');
        $model -> u_recordid     = UPatientdata::maxRecordid();
        $model -> sourcedata     = empty(Yii::$app->getRequest()->getBodyParam('sourcedata')) ? json_encode('') : Yii::$app->getRequest()->getBodyParam('sourcedata');
        $model -> imgs           = empty(Yii::$app->getRequest()->getBodyParam('imgs')) ? json_encode('') : Yii::$app->getRequest()->getBodyParam('imgs');
        $model -> remark         = Yii::$app->getRequest()->getBodyParam('remark');
        if($model->save()){
            //修改患者基本信息里的最新更新时间
            UPatientbase::uptime($model -> u_MDID, $model -> u_updatetime);
            //日志内容
            $filename                  = date('Y-m-d').'-patientdata.log';
            $content['u_createuserid'] = $model -> u_createuserid;
            $content['requesturl']     = 'patient/createpatientdata';
            $content['u_recordid']     = $model -> u_recordid;
            $content['u_MDID']         = $model -> u_MDID;
            $content['templateid']     = $model -> u_templetid;
            $content['u_patientdata']  = $model -> u_patientdata;
            $content['sourcedata']     = $model -> sourcedata;
            $content['imgs']           = $model -> imgs;
            $content['remark']         = $model -> remark;
            $content['u_createtime']   = $model -> u_createtime;
            Commonfun::createLog($filename,$content);
            return $model->u_recordid;
        }else{
            return false;
        }
    }
    /**
     * @todo 修改患者记录
     * @param        createuserid   修改人
     * @param        recordid       记录id
     * @param  json  patientdata
     */
    public static function updatePatientdata(){
        $model = UPatientdata::findOne(['u_recordid'=>Yii::$app->getRequest()->getBodyParam('recordid')]);
        $model -> u_patientdata  = Yii::$app->getRequest()->getBodyParam('patientdata');
        $model -> u_updateuserid = Yii::$app->getRequest()->getBodyParam('createuserid');
        $model -> u_updatetime   = date('Y-m-d H:i:s');
        $model -> sourcedata     = empty(Yii::$app->getRequest()->getBodyParam('sourcedata')) ? json_encode('') : Yii::$app->getRequest()->getBodyParam('sourcedata');
        $model -> imgs           = empty(Yii::$app->getRequest()->getBodyParam('imgs')) ? json_encode('') : Yii::$app->getRequest()->getBodyParam('imgs');
        $model -> remark         = Yii::$app->getRequest()->getBodyParam('remark');
        if($model->save()){
            //修改患者基本信息里的最新更新时间
            UPatientbase::uptime($model -> u_MDID, $model -> u_updatetime);
            //日志内容
            $filename                  = date('Y-m-d').'-patientdata.log';
            $content['u_updateuserid'] = $model -> u_updateuserid;
            $content['requesturl']     = 'patient/updatepatientdata';
            $content['recordid']       = $model -> u_recordid;
            $content['u_patientdata']  = $model -> u_patientdata;
            $content['sourcedata']     = $model -> sourcedata;
            $content['imgs']           = $model -> imgs;
            $content['remark']         = $model -> remark;
            $content['u_updatetime']   = $model -> u_updatetime;
            Commonfun::createLog($filename,$content);
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 获取患者记录  
     * @param mdid
     * @param templateid (选填)
     * return array
     */
    public static function getRecords($mdid){
        //患者所在项目
        //$projectid = UPatientbase::getPatientInfo($mdid)['u_projectid'];
//         if($projectid == 1){
//             if(Yii::$app->getRequest()->getBodyParam('templateid') == ''){
//                 $where['u_patientdata.u_MDID'] = $mdid;
//             }else{
//                 $where['u_patientdata.u_MDID']      = $mdid;
//                 $where['u_patientdata.u_templetid'] = Yii::$app->getRequest()->getBodyParam('templateid');
//             }
            
//             $result = UPatientdata::find()
//             ->select('u_patientdata.*,u_telemplate.u_templatename')
//             ->joinWith('uTelemplate')
//             //->where(['u_patientdata.u_MDID'=>$mdid])
//             ->where($where)
//             ->orderby(['u_patientdata.u_updatetime'=>SORT_ASC])
//             ->asarray()
//             ->all();
//             $newarr = [];
//             foreach ($result as $k=>$v){
//                 $newarr[$v['u_templetid']]['id']              = $v['id'];
//                 $newarr[$v['u_templetid']]['u_MDID']          = $v['u_MDID'];
//                 $newarr[$v['u_templetid']]['u_templetid']     = $v['u_templetid'];
//                 $newarr[$v['u_templetid']]['u_patientdata']   = $v['u_patientdata'];
//                 $newarr[$v['u_templetid']]['sourcedata']      = $v['sourcedata'];
//                 $newarr[$v['u_templetid']]['imgs']            = $v['imgs'];
//                 $newarr[$v['u_templetid']]['remark']          = $v['remark'];
//                 $newarr[$v['u_templetid']]['u_createuserid']  = $v['u_createuserid'];
//                 $newarr[$v['u_templetid']]['u_createtime']    = $v['u_createtime'];
//                 $newarr[$v['u_templetid']]['u_updateuserid']  = $v['u_updateuserid'];
//                 $newarr[$v['u_templetid']]['u_updatetime']    = $v['u_updatetime'];
//                 $newarr[$v['u_templetid']]['u_recordid']      = $v['u_recordid'];
//                 $newarr[$v['u_templetid']]['u_templatename']  = $v['u_templatename'];
//             }
//             $arr = array_values($newarr);
//         }else{
            $arr = RNewpatientdata::patientData($mdid);
        //}
        return $arr;
    }
    /**
     * @todo 获取患者某条记录
     * @param recordid
     */
    public static function getRecord($recordid){
        $result = UPatientdata::find()
        ->where(['u_recordid'=>$recordid])
        ->asarray()
        ->all();
        if(empty($result)){
            $newresult = '';
        }
        $newresult = $result[0];
        return $newresult;
    }
    /**
     * @todo 最大的记录id
     * return   string
     */
    public static function maxRecordid(){
        $arr = UPatientdata::find()
        ->select('u_recordid')
        ->orderBy(['u_recordid'=>SORT_DESC])
        ->limit(1)
        ->asarray()
        ->all();
        if(empty($arr) || $arr[0]['u_recordid']==''){
            $record = 1;
        }else{
            $record = $arr[0]['u_recordid']+1;
        }
        return $record;
    }
    /**
     * @todo 患者最新地址  正则匹配 u_patientdata
     * @param $mdid
     */
    public static function lastAddress($mdid){
        #患者信息 （取出每个模板下最新的一条）
        $result = UPatientdata::find()
        ->select('u_templetid,u_patientdata,u_updatetime')
        ->where(['u_MDID'=>$mdid])
        ->orderby(['u_updatetime'=>SORT_ASC])
        ->asarray()
        ->all();
        $newarr = [];
        foreach ($result as $k=>$v){
            $newarr[$v['u_templetid']]['u_templetid']     = $v['u_templetid'];
            $newarr[$v['u_templetid']]['u_patientdata']   = $v['u_patientdata'];
            $newarr[$v['u_templetid']]['u_updatetime']    = $v['u_updatetime'];
        }
        //print_R($newarr);exit;
        $arr = array_values($newarr);
        if(empty($arr)){
            return $newaddressarr = [];
        }
        foreach ($arr as $k=>$v){
            $jsonarr[] = json_decode($v['u_patientdata'],true);
        }
        foreach($jsonarr as $key=>$val){
           foreach($val as $k1=>$v1){
               if($k1 == '住址' || $k1 == '地址' || $k1 == '现居住地'){
                   $newaddressarr[] = $v1;
               }
           }
        }
        if(!empty($newaddressarr)){
            return $newaddressarr;
        }
        $addressarr = [];
        #地址   省 ｜市｜区｜县｜镇｜村｜街｜道｜楼｜号｜单元｜
        $regaddress = '/([^\"]+(?<=[\x{7701}\x{5e02}\x{533a}\x{53bf}\x{9547}\x{6751}\x{8857}\x{9053}\x{697c}\x{53f7}\x{5355}\x{5143}])[^\"]*)/u';
        foreach ($arr as $k=>$v){
            preg_match_all($regaddress,$v['u_patientdata'],$addressarr[$k][]);
        }
        if(empty($addressarr)){
            return $newaddressarr = [];
        }
        foreach($addressarr as $k=>$v){
            foreach($v as $k1=>$v1){
                foreach($v1 as $k2=>$v2){
                    foreach($v2 as $k3=>$v3){
                        if($v3 != ''){
                            $newaddressarr[$k3] = $v3;
                        }
                    }
                }
            }
        }
        if(empty($newaddressarr)){
            return $newaddressarr = [];
        }
        return $newaddressarr;
    }
    /**
     * @todo 删除患者记录
     * @param userid
     * @param recordid
     */
    public static function deletePatient($userid,$recordid){
        //$arr      = explode(',', $recordid);
        //foreach($arr as $k=>$v){
            if(UPatientdata::userPermission($userid,$recordid)){
                UPatientdata::deleteAll(['u_recordid'=>$recordid]);
                return true;
            }else{
                return false;
            }
        //}
        
    }
    /**
     * @todo 是否有删除权限
     * 
     */
    public static function userPermission($userid,$recordid){
        $arr = UPatientdata::find()
             ->where(['u_recordid'=>$recordid])
             ->asarray()
             ->all();
        if(!empty($arr)){
            if($userid == $arr[0]['u_createuserid'] || $userid == $arr[0]['u_updateuserid']){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    /**
     * @todo 导出患者
     * @param mdid
     */
    public static function exportPatient($mdid){
        $mdidarr = explode(',',$mdid);
        $result  = UPatientbase::patientInfo($mdidarr);
//         $result  = UPatientdata::find()
//                 ->joinWith('uPatientbase')
//                 ->select('u_patientdata.*,u_patientbase.u_MDID')
//                 ->where(['in','u_patientbase.u_MDID',$mdidarr])
//                 ->orderby(['u_patientdata.u_updatetime'=>SORT_ASC])
//                 ->asarray()
//                 ->all();
        //患者数据整理  每种记录的最新一条
        $newarr = [];
        foreach ($result as $k=>$v){
            $newarr[$v['u_MDID']][$v['u_templetid']]['id']              = $v['id'];
            $newarr[$v['u_MDID']][$v['u_templetid']]['u_MDID']          = $v['u_MDID'];
            $newarr[$v['u_MDID']][$v['u_templetid']]['u_templetid']     = $v['u_templetid'];
            $newarr[$v['u_MDID']][$v['u_templetid']]['u_patientdata']   = $v['u_patientdata'];
            $newarr[$v['u_MDID']][$v['u_templetid']]['sourcedata']      = $v['sourcedata'];
            $newarr[$v['u_MDID']][$v['u_templetid']]['imgs']            = $v['imgs'];
            $newarr[$v['u_MDID']][$v['u_templetid']]['remark']          = $v['remark'];
            $newarr[$v['u_MDID']][$v['u_templetid']]['u_createuserid']  = $v['u_createuserid'];
            $newarr[$v['u_MDID']][$v['u_templetid']]['u_createtime']    = $v['u_createtime'];
            $newarr[$v['u_MDID']][$v['u_templetid']]['u_updateuserid']  = $v['u_updateuserid'];
            $newarr[$v['u_MDID']][$v['u_templetid']]['u_updatetime']    = $v['u_updatetime'];
        }
        //导出的excel字段
        $tempcontent = Yii::$app->params['patientdata'];
        foreach ($tempcontent as $key=>$val){
            $template['baseinfo'] = ['u_MDID'=>'','姓名'=>'','性别（1男2女）'=>'','生日'=>''];
            $template[$key]       = json_decode($val,true);
            //tempid
            $temparr[]            = $key;
        }
        //需要导出的内容
        $objPHPExcel = new PHPExcel();
        //字段
        $count = 0; #列
        foreach($template as $k=>$v){
            foreach ($v as $key=>$val){
                //数字转化excel字母
                $letter = PHPExcel_Cell::stringFromColumnIndex($count);
                $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue($letter.'1', $key);
                $count++;
            }
        }
        //值  整理模板id
        foreach($newarr as $k=>$v){
            foreach($v as $k1=>$v1){
                $nowtemp[$k][] = $k1;
            }
        }
        //把没有值的模板内容设成配置文件中内容
        foreach ($nowtemp as $k=>$v){
            foreach($temparr as $key=>$val){
                if(!in_array($val,$v)){
                    $newarr[$k][$val] = ['u_MDID'=>$k,'u_templetid'=>$val,'u_patientdata'=>Yii::$app->params['patientdata'][$val]];
                }
            }
            ksort($newarr[$k]);
        }
        foreach ($newarr as $k=>$v){
            foreach($v as $key=>$val){
                $content[$k]['baseinfo']['u_MDID']        = isset($val['u_MDID'])?$val['u_MDID']:'';
                $baseinfo = UPatientbase::getPatientBase($content[$k]['baseinfo']['u_MDID']);
                $content[$k]['baseinfo']['u_patientname'] = isset($baseinfo[0]['u_patientname'])?$baseinfo[0]['u_patientname']:'';
                $content[$k]['baseinfo']['u_gender']      = isset($baseinfo[0]['u_gender'])?$baseinfo[0]['u_gender']:'';
                $content[$k]['baseinfo']['u_birthday']    = isset($baseinfo[0]['u_birthday'])?$baseinfo[0]['u_birthday']:'';
                //药品信息特殊处理
                if(isset($val['u_MDID']) && $val['u_templetid'] == 5){
                    $drugstr = '';
                    if(!empty($val['u_patientdata'])){
                        $drug = json_decode($val['u_patientdata'],true);
                        foreach($drug as $key1=>$val1){
                            $drugstr .= $key1.":".$val1.";";
                        }
                    }
                    $content[$k][$val['u_templetid']]['药品'] = $drugstr;
                }else{
                    $content[$k][$val['u_templetid']] = json_decode($val['u_patientdata'],true);
                }
            }
        }
        foreach($content as $k=>$v){
            foreach ($v as $key=>$val){
                if($key == ''){
                    unset($content[$k][$key]);
                }
            }
        }
        $num = 2;#计算行 从第二行开始
        foreach($content as $k=>$v){
            $let = 0; #计算字母 列
            foreach ($v as $key=>$val){
                foreach ($val as $k1=>$v1){
                    //数字转化excel字母
                    $letter = PHPExcel_Cell::stringFromColumnIndex($let);
                    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($letter.$num, $v1);
                    $let++;
                }
            }
            $num++;
        }
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('患者统计数据');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
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
        return true;
    }
}
