<?php

namespace app\models;

use Yii;
use yii\log\FileTarget;
use yii\Image\Imgcompress;
use yii\Gateway\Gateway;
/**
 * This is the model class for common fun.
 * for  easy
 * 一些公共的方法
 */
class Commonfun extends \yii\db\ActiveRecord
{
    /**
     * @todo 生成日志
     * @param 日志名称     filename
     * @param 日志内容      content
     */
     public static function createLog($filename,$content){
         $time            = microtime(true);
         $log             = new FileTarget();
         $log->logFile    = Yii::$app->getRuntimePath() . '/logs/'.$filename; //文件名自定义
         $log->messages[] = [$content,1,'application',$time];
         $log->export();
     }

    /**
     * @todo 格式化打印数组
     * @param $array
     */
    public static function fmtPrint_r($array) {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }
    /**
     * @todo 生成随机串
     * @param number $len
     * @param string $format
     * @return string
     */
    public static function randpw($len=10,$format='ALL'){
        $is_abc = $is_numer = 0;
        $password = $tmp ='';
        switch($format){
            case 'ALL':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
            case 'CHAR':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case 'NUMBER':
                $chars='0123456789';
                break;
            default :
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        }
        // mt_srand((double)microtime()*1000000*getmypid());
        while(strlen($password) < $len){
            $tmp =substr($chars, (mt_rand() % strlen($chars)), 1);
            if(($is_numer <> 1 && is_numeric($tmp) && $tmp > 0 )|| $format == 'CHAR'){
                $is_numer = 1;
            }
            if(($is_abc <> 1 && preg_match('/[a-zA-Z]/',$tmp)) || $format == 'NUMBER'){
                $is_abc = 1;
            }
            $password.= $tmp;
        }
        if($is_numer <> 1 || $is_abc <> 1 || empty($password) ){
            $password = Commonfun::randpw($len,$format);
        }
        return $password;
    }
    /**
     * @todo 取出电话号码
     * @param
     */
    public static function getfirstchar($s){  
        if($s[0]=='I' || $s[0]=='i'){  
            return "I";  
        }elseif($s[0]=='U' || $s[0]=='u'){  
            return 'U';  
        }elseif($s[0]=='V' || $s[0]=='v'){  
            return 'V';  
        }else{  
        $fchar = ord($s{0});  
        if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s{0});  
        $s1 = iconv("UTF-8","gb2312", $s);  
        $s2 = iconv("gb2312","UTF-8", $s1);  
        if($s2 == $s){$s = $s1;}else{ $s = $s;}  
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;  
        if($asc >= -20319 and $asc <= -20284) return "A";  
        if($asc >= -20283 and $asc <= -19776) return "B";  
        if($asc >= -19775 and $asc <= -19219) return "C";  
        if($asc >= -19218 and $asc <= -18711) return "D";  
        if($asc >= -18710 and $asc <= -18527) return "E";  
        if($asc >= -18526 and $asc <= -18240) return "F";  
        if($asc >= -18239 and $asc <= -17923) return "G";  
        if($asc >= -17922 and $asc <= -17418) return "H";  
        if($asc >= -17417 and $asc <= -16475) return "J";  
        if($asc >= -16474 and $asc <= -16213) return "K";  
        if($asc >= -16212 and $asc <= -15641) return "L";  
        if($asc >= -15640 and $asc <= -15166) return "M";  
        if($asc >= -15165 and $asc <= -14923) return "N";  
        if($asc >= -14922 and $asc <= -14915) return "O";  
        if($asc >= -14914 and $asc <= -14631) return "P";  
        if($asc >= -14630 and $asc <= -14150) return "Q";  
        if($asc >= -14149 and $asc <= -14091) return "R";  
        if($asc >= -14090 and $asc <= -13319) return "S";  
        if($asc >= -13318 and $asc <= -12839) return "T";  
        if($asc >= -12838 and $asc <= -12557) return "W";  
        if($asc >= -12556 and $asc <= -11848) return "X";  
        if($asc >= -11847 and $asc <= -11056) return "Y";  
        if($asc >= -11055 and $asc <= -10247) return "Z";  
        return null;  
                   }  
    }
    /**
     * @todo 当前周的结束日期和开始日期
     * @param date 日期
     */
    public static function getWeekday($date){
        $first         =1; //$first =1 表示每周星期一为开始时间 0表示每周日为开始时间
        $w             = date("w", strtotime($date)); //获取当前周的第几天 周日是 0 周一 到周六是 1 -6
        $d             = $w ? $w - $first : 6; //如果是周日 -6天
        $start         = date("Y-m-d", strtotime("$date -".$d." days")); //本周开始时间;
        $week['start']          = $start;
        $week['start2']         = date("Y-m-d", strtotime("$start +1 days"));
        $week['start3']         = date("Y-m-d", strtotime("$start +2 days"));
        $week['start4']         = date("Y-m-d", strtotime("$start +3 days"));
        $week['start5']         = date("Y-m-d", strtotime("$start +4 days"));
        $week['start6']         = date("Y-m-d", strtotime("$start +5 days"));
        $week['end']            = date("Y-m-d", strtotime("$start +6 days")); //本周结束时间
        
        return $week;
    }
    /**
     * @todo 当月第一天最后一天
     */
    public static  function getMonth($date){
        $firstday = date('Y-m-01', strtotime($date));
        $lastday  = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
        $month['start'] = $firstday;
        $month['end']   = $lastday;
        return $month;
   }
    /**
     * 上传文件
     * @param $uploadFile
     * @param $pre (区分名称)
     * @param type
     * @return array(url)
     */
    public static function upFile($uploadFile,$pre,$type=''){
        //print_r($uploadFile);exit;
        $data      = date("Y", time()) . '/' . date("m", time()) . '/' . date("d", time()) . '/';
        $save_path = Yii::$app->basePath.'/uploads/'.$data;
        //创建相应了、目录
        if (!file_exists($save_path)) {
            mkdir($save_path, 0777, true);
        }
        foreach($uploadFile as $k=>$v){
            //保存到本地的文件名
            $filename              = explode(".", $v->name);
            $namef                 = md5(uniqid(rand(), true)) . '.' . end($filename);
            //保存到本地
            //$v -> saveAs($save_path . $namef);
            if($type == 'invite'){
                $v -> saveAs($save_path . $namef);
                return $save_path . $namef;
            }
            $result[$k]['url']     = Commonfun::upUfile($v->tempName,$namef,$pre.'-');
            //======== 以下是压缩图片 ==========
            //是否为图片
            $type = ['jpg', 'jpeg', 'png', 'bmp', 'wbmp','gif'];
            if(in_array(end($filename),$type)){
                $v -> saveAs($save_path . $namef);
                $namecompress = 'compress_'.$namef;
                $image        = new Imgcompress($save_path.$namef,1);
                $image ->compressImg($namecompress,$save_path);
                $result[$k]['compressurl']     = Commonfun::upUfile($save_path . $namecompress,$namef,'compress-'.$pre.'-');
            }
            //========== 压缩结束 ============
            $result[$k]['name']    = $v->name;
            $result[$k]['size']    = $v->size;
        }
        $arr = array_values($result);
        return $arr;
    }
    /**
     * @todo 上传文件到ufile
     * @author hjh
     */
    public static function upUfile($dir,$namef,$prefix){
        $path = Yii::$app->basePath.'/../';
        require_once("".$path."vendor/uclouds/ucloud/proxy.php");
        //存储空间名
        $bucket = Yii::$app->params['bucket'];
        //上传至存储空间后的文件名称
        $key    = $prefix.time().$namef;
        //待上传文件的本地路径
        $file   = $dir;
        //该接口适用于0-10MB小文件,更大的文件建议使用分片上传接口
        list($data, $err) = UCloud_PutFile($bucket, $key, $file);
        if ($err) {
            return false;
        }else{
            $url = UCloud_MakePrivateUrl($bucket, $key);
            return $url;
        }
    }
    /**
     * @todo 验证邮箱
     * @param mail邮箱
     * @return string
     */
    public static function isMail($mail){
        $mode = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
        if(preg_match($mode,$mail)){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @todo 发送邮件
     */
    public static function sendMail($sendmail,$tomail,$subject='',$path,$content=[]){
        $message = Yii::$app -> mail;
        $message ->htmlLayout = false;
        $message ->compose($path,$content)
                 ->setFrom($sendmail)
                 ->setTo($tomail)
                 ->setSubject($subject)
                 ->send();
        return true;
    }
    /**
     * 发送通知
     * @param userid
     * @param content
     */
    public static function sendMess($userid,$content){
        Gateway::$registerAddress = '106.75.32.143:1238';
        Gateway::sendToUid($userid,$content);
    }
    /**
     * websocket绑定userid
     * @param $uid
     * @param $client_id
     */
    public static function bindUserid($uid,$client_id){
        Gateway::$registerAddress = '106.75.32.143:1238';
        Gateway::bindUid($client_id, $uid);
    }
    /**
     * 是否为项目管理员或企业管理员中的一个
     * @param userid
     * @param projectid
     * @param companyid
     * @return string
     */
    public static function isAdmin($userid,$projectid,$companyid){
        $admin = 0;
        $projectadmin = PProjectUser::userInfo($userid,$projectid);
        if(!empty($projectadmin) && $projectadmin['permission'] == 1){
            $admin = 1;
        }
        $companyadmin = QCompanyUser::userPermission($userid,$companyid);
        if(!empty($companyadmin) && $companyadmin['permission'] == 1){
            $admin = 1;
        }
        return $admin;
    }

}
