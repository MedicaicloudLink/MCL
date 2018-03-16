<?php

namespace backend\controllers;
use yii;
use backend\controllers\BaseController;
use yii\Org\Aes;
class BlogController extends BaseController
{
    public function actionA(){
        $token    = Yii::$app->getRequest()->getBodyParam('a');
        $authuser = self::_checkToken($token);
        if(isset($authuser['refresh_token'])){
            
        }
        print_r($authuser);
    }
    public function actionB(){
        $token = Yii::$app->getRequest()->getBodyParam('token');
        $model = new Aes();
        $string = $model->decrypt($token);
        echo $string;
    }
    /**
     * @todo 创建随访计划             createfollow
     * @param userid
     * @param projectid
     * @param name
     * @param mem
     * @param start
     * @param end
     * @param interval
     * @param followpeople
     * @return string
     */
    public function actionCreatefollow(){
        self::checkParamIsEmpty(
                [
                    'userid'      => '创建人不能为空',
                    'projectid'   => '项目不能为空',
                    'name'        => '随访名称不能为空'
                ]
        );
        $result = '';
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
                $regaddress = '/([^\"]+(?<=[\x{7701}\x{5e02}\x{533a}\x{53bf}\x{9547}\x{6751}\x{8857}\x{9053}])[^\"]+)/u';
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

	
}
