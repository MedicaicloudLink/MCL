<?php
namespace backend\controllers;

use Yii;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Cell;
use app\models\A;
use app\models\UploadForm;
use yii\web\UploadedFile;
use yii\web\Controller;
use app\models\UPatientdata;
use app\models\Commonfun;
use app\models\UMedicineData;
use app\models\UPatientbase;
use app\models\UTelemplate;
use yii\Image\Imgcompress;
/**
 * 
 * @todo test
 * @author 梅地卡尔医疗
 * @return 
 * 
 */
class TestController extends Controller
{
    public $enableCsrfValidation = false;
    public function actionIndex()
    {
        
        $this->layout = false;
        return $this->render('index');
    }
    public function actionAaa(){
        echo 222;die;
        $name=Yii::$app->request->post('textval');
        echo $name;
    }
    /**
     * test redis
     */
    public function actionTest(){
        $postid          = Yii::$app->redis->incr('post');
        $data['title']   = '文章标题2';
        $data['content'] = '文章内容2';
        $data['author']  = '文章作者2';
        $data['time']    = date('Y-m-d H:i:s');
        $serializedata   = serialize($data);
        Yii::$app -> redis ->set('post:'.$postid.':data',$serializedata);
        
    }
    public function actionTestresult(){
        //------------------------------
        /*$arr = Yii::$app->redis->hgetall('car');
        foreach ($arr as $k =>$v){
            if($k % 2 == 0){
                $arrnew[$v] = $arr[$k+1];
            }
        }*/
        //------------------------------>
        //$serdata = Yii::$app -> redis ->get('post:4:data');
        //$data    = unserialize($serdata);
        //Yii::$app->redis->type('str');
//         Yii::$app->redis->append('str',2);
//         echo Yii::$app->redis->get('str');
//         echo Yii::$app->redis->strlen('str');
        //echo Yii::$app->redis->executeCommand('mget',['0'=>'a','1'=>'b']);
        /*//模板个数
         foreach($newarr as $k=>$v){
        $newarr1[$k] = count($newarr[$k]);
        }
         
        //用的模板最多患者
        $maxmdid = array_search(max($newarr1),$newarr1);
        //模板信息
        ksort($newarr[$maxmdid]);
        foreach ($newarr[$maxmdid] as $k=>$v){
        //所有模板id
        //$temparr[]                      = $v['u_templetid'];
        
        $template['baseinfo']['u_MDID'] = $v['u_MDID'];
        //药品信息特殊处理
        if($v['u_templetid'] == 5){
        $drugstr = '';
        $drug = json_decode($v['u_patientdata'],true);
        foreach($drug as $key=>$val){
        $drugstr .= $key.":".$val.";";
        }
        $template[$v['u_templetid']]['药品'] = $drugstr;
        }else{
        $template[$v['u_templetid']] = json_decode($v['u_patientdata'],true);
        }
        
        }*/
    }
    /**
     * redis 队列
     */
    public function actionRedis(){
        $redis = Yii::$app -> redis;
        $redis->set('msg','qqqq');
        echo $redis->get('msg');
//         $redis->sadd('user:123','msg:1');
//         echo $redis->smembers('user:123:msg');
    }
    /**
     * @todo test 导出excel
     */
    public function actionExcel(){
        $mdidarr = ['4gohjdQWWs','XT6LWE8uXn','Az8Ic2S4ll'];
        $result = UPatientdata::find()
            ->where(['in','u_MDID',$mdidarr])
            ->orderby(['u_patientdata.u_updatetime'=>SORT_ASC])
            ->asarray()
            ->all();
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
            $template['baseinfo'] = ['u_MDID'=>'','u_patientname'=>'','u_gender'=>'','u_birthday'=>''];
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
                print_r($baseinfo);exit;
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
        header('Content-Disposition: attachment;filename="statistics_item.xls"');
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
    /**
     * @todo 导入excel
     */
    public function actionImportexcel(){
        $model = new UploadForm();
        #获取上传文件信息
        $file  = UploadedFile::getInstance($model, 'file');
        if($file){
            #给文件取名字
            $filename =  date('Y-m-d',time()).'_'.rand(1,9999).'.' .$file->extension;
            #保存文件
            $file     -> saveAs(Yii::$app->basePath.'/uploads/'.$filename);
            #文件后缀名
            $format   =  $file->extension;
            if(in_array($format,array('xls','xlsx'))){
                #获取文件名
                $excelFile  = Yii::getAlias(Yii::$app->basePath.'/uploads/'.$filename);
                $phpexcel   = new PHPExcel();  
                if ($format == "xls") {
                    $excelReader = \PHPExcel_IOFactory::createReader('Excel5');
                } else {
                    $excelReader = \PHPExcel_IOFactory::createReader('Excel2007');
                }
                #载入文件并获取第一个sheet
                $phpexcel        = $excelReader->load($excelFile)->getSheet(0);
                $total_line      = $phpexcel->getHighestRow();    #总行数
                $total_column    = $phpexcel->getHighestColumn();    #总列数
                #第二行开始循环 第一行大多为标题
                if($total_line > 1){
                    for($row = 2; $row <= $total_line; $row++){
                        $data = array();
                        #循环列
                        for($column = 'A'; $column <= $total_column; $column++){
                            $data[] = trim($phpexcel->getCell($column.$row)->getValue());
                        }
                        print_r($data);exit;
                        
                        /**@todo 数据入库 一行一行**/
                    }
                }else{
                    echo "好像木有内容";
                }
            }else{
                echo "乖乖上传excel格式的文件";
            }
            
        }else{
            echo "快快选择文件";
        }
    }
    public function actionShowup(){
        return $this->renderPartial('index');
    }
    public function actionUfile(){
        A::up();
        //$rootdir = Yii::getRootAlias('@vendor');
        
    }
    public function actionDatedetails(){
        #患者
        $mdid = '0zlI5DBxSv';
        #患者信息 （取出每个模板下最新的一条）
        $arr = UPatientdata::find()
        ->select('u_templetid,u_patientdata,max(u_updatetime)')
        ->where(['u_MDID'=>$mdid])
        ->orderby(['u_templetid'=>SORT_DESC,'u_updatetime'=>SORT_DESC])
        ->groupBy(['u_templetid'])
        ->asarray()
        ->all();
        if(empty($arr)){
            echo '木有这个人';exit;
        }
        #正则
        #1.手机号
        #2.座机号
        #3.400电话
        $regtel = array(
            'sj'  =>  '/(\+?86-?)?(18|15|13)[0-9]{9}/',
            'tel' =>  '/(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?/',
            '400' =>  '/400(-\d{3,4}){2}/',
        );
        foreach ($arr as $k=>$v){
            foreach($regtel as $kt=>$kv){
                preg_match_all($kv,$v['u_patientdata'],$telarr[$k][]);
            }
        }
        foreach($telarr as $k=>$v){
            foreach($v as $k1=>$v1){
                foreach($v1 as $k2=>$v2){
                    foreach($v2 as $k3=>$v3){
                        if($v3 != '' && strlen($v3)>5){
                            $newtelarr[] = $v3;
                        }
                    }
                }
            }
        }
        #$newtelarr  匹配出的电话
        //print_r($newtelarr);exit;
    
        #地址   省 ｜市｜区｜县｜镇｜村｜街｜道｜楼｜号｜单元｜
        $regaddress = '/([^\"]+(?<=[\x{7701}\x{5e02}\x{533a}\x{53bf}\x{9547}\x{6751}\x{8857}\x{9053}\x{697c}\x{53f7}\x{5355}\x{5143}])[^\"]*)/u';
        foreach ($arr as $k=>$v){
            preg_match_all($regaddress,$v['u_patientdata'],$addressarr[$k][]);
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
        $result['tel']     = $newtelarr;
        $result['address'] = $newaddressarr;
        print_r($result);
    }
    /**
     * @todo 根据首字母查询
     */
    public function actionSelectfirst(){
        $drugname = 'a';
        $arr = UMedicineData::find()
        ->where(['like', Commonfun::getfirstchar('u_drugname'), $drugname]);
        //->all();
        $commandQuery = clone $arr;  
        echo $commandQuery->createCommand()->getRawSql();  
    }
    /**
     * @todo 微信openid
     */
    public function actionWechat(){
        $appid  = 'wx77b31c93a3df432b';
        $secret = 'a86be26e52be68f46b72b4a92753990f';
        $code   = Yii::$app->getRequest()->getBodyParam('code');
        $url    = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
        //$result = file_get_contents($url);
        $ch = curl_init(); 
        $http = curl_init ( $url );
        curl_setopt ( $http, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $http, CURLOPT_SSL_VERIFYHOST, 2 );
        curl_setopt ( $http, CURLOPT_SSL_VERIFYPEER, FALSE );
        $data = curl_exec ( $http );
        var_dump($data); 
    }
    public function actionGetcity($ip = '115.29.187.62')
    {
        if($ip == ''){
            $url = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json";
            $ip=json_decode(file_get_contents($url),true);
            $data = $ip;
        }else{
            $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
            $ip=json_decode(file_get_contents($url));
            if((string)$ip->code=='1'){
                return false;
            }
            $data = (array)$ip->data;
        }
    
        var_dump($data) ;
    }
    public function actionShortcom(){
        $url    = 'www.medicayun.com';
        $url    = crc32($url);
        $x      = sprintf("%u",$url);
        $show   = ''; 
          while($x>0){ 
            $s=$x % 62; 
            if ($s>35){ 
              $s=chr($s+61); 
            }elseif($s>9&&$s<=35){ 
              $s=chr($s+55); 
            } 
            $show.=$s; 
            $x=floor($x/62); 
          } 
         echo $show; 
    }
    public function actionElastic(){
        A::c();
    }
    public function actionSendmail(){
        echo 444555;exit;
        //echo Yii::$app->request->getActualBaseUrl();exit;
        $sendmail = 'support@medicayun.cn';
        $tomail   = '937274669@qq.com';
        $path     = '/mail/regist_mail';
        $content  = ['url'=>'北京','path'=>Yii::$app->request->baseUrl];
        $subject  = 'this is a subject';
        Commonfun::sendMail($sendmail,$tomail,$subject,$path,$content);
    }
    public function actionYl(){
        //原图
        //$source = 'http://medicayun.cn-bj.ufileos.com/project-15108213990ef0628ba1593236c4145094c3040780.gif?UCloudPublicKey=ucloudzhexcel%40163.com14280235690001077411631&Signature=234n1UyNVMaOfz3RFFisQpZO3ro%3D';
        $source = '0.gif';
        $dst_img = '40.gif';//压缩后图片的名称
        $percent = 1;  #原图压缩，不缩放，但体积大大降低
        $image = new Imgcompress($source,$percent);
        $image ->compressImg($dst_img);
    }


}
