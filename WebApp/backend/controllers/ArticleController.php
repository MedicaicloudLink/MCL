<?php

namespace backend\controllers;

use Yii;
use app\models\User;
use app\models\A;
use app\models\MUserinfo;
use app\models\UNotice;
use app\models\UUserProject;
use app\models\UUsernotices;
class ArticleController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->renderPartial('index');
    }
    
    public function actionB(){
//         header('Content-Type:text/event-stream');//通知浏览器开启事件推送功能
//         header('Cache-Control:no-cache');//告诉浏览器当前页面不进行缓存
        //$arr = MUserinfo::findOne(['s_username'=>'刘帅']);
        //$ar1 = MUserinfo::find()->where(['s_username'=>'刘帅'])->asarray()->one();
//         $a = MUserinfo::find()->max('s_userid');
//         echo $a;exit;
//         echo '4'.print(2)+3;die;
//         echo time();die;
//         echo md5(md5(123456));die;
//         $arr = User::userInfo() ;
//         $arr = A::B();
//         echo "data: The server time is: \n\n".date('Y-m-d H:i:s');
//          ob_flush();//刷新
//          flush();//刷新huohuo申请加入阜外CT随访
        //$arr = UUsernotices::createUsernotice(2,1,2,1,1);
        //$result = UUsernotices::getList(1);
        $model = UUsernotices::updateAll(['content' => 'huohuo申请加入阜外CT随访'], ['logid'=>40]);
    }
    public function actionOrderby(){
        $arr = [5,4,6,1];
        $num = count($arr);
        #循环轮数
        for($i = 1; $i < $num; $i++){
            #数需要比较的次数
            for($j = 0; $j < $num-$i; $j++){
                if($arr[$j] > $arr[$j+1]){
                    $temp      = $arr[$j];
                    $arr[$j]   = $arr[$j+1];
                    $arr[$j+1] = $temp;
                }
            }
        }
        print_r($arr);
    }

}
