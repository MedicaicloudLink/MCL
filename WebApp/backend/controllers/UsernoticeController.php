<?php

namespace backend\controllers;

use Yii;
use app\models\NNotice;
class UsernoticeController extends BaseController
{
    /**
     * 导航下拉的通知列表(10个)
     * @param userid
     * @return string
     */
    public function actionBarnotice(){
        self::checkParamIsEmpty(
            [
                'userid' => '用户id不能为空'
            ]
        );
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        //权限身份验证
        $this   ->checkAuth($userid);
        $result = NNotice::noticeList($userid,'bar');
        return  $this->Rsuccess('导航栏通知：',$result);
    }
    /**
     * 全部通知
     * @param userid
     * @param page
     * @return string
     */
    public function actionAllnotice(){
        self::checkParamIsEmpty(
            [
                'userid' => '用户id不能为空',
                'page'   => '当前页不能为空'
            ]
        );
        $userid = Yii::$app->getRequest()->getBodyParam('userid');
        $page   = Yii::$app->getRequest()->getBodyParam('page');
        //权限身份验证
        $this   ->checkAuth($userid);
        $result = NNotice::noticeList($userid,'all',$page);
        return  $this->Rsuccess('全部通知：',$result);
    }
    /**
     * 删除通知
     * @param noticeid
     * @param userid
     * @return string
     */
    public function actionDeletenotice(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户id不能为空',
                'noticeid'  => '通知id不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $noticeid   = Yii::$app->getRequest()->getBodyParam('noticeid');
        //权限身份验证
        $this   ->checkAuth($userid);
        $result     = NNotice::delNotice($userid,$noticeid);
        if($result){
            return $this->Rsuccess('删除成功');
        }else{
            return $this->Error('删除失败');
        }
    }
    /**
     * 设置消息已读
     * @param userid
     * @param type(all 全部已读)
     * @param noticeid(多个用逗号隔开)
     * @return string
     */
    public function actionSetread(){
        self::checkParamIsEmpty(
            [
                'userid'    => '用户id不能为空'
            ]
        );
        $userid     = Yii::$app->getRequest()->getBodyParam('userid');
        $noticeid   = Yii::$app->getRequest()->getBodyParam('noticeid');
        $type       = Yii::$app->getRequest()->getBodyParam('type');
        if($noticeid == '' && $type == ''){
            return $this->Error('缺少参数');
        }
        //权限身份验证
        $this   ->checkAuth($userid);
        if($type == ''){
            $type = 'noall';
        }
        NNotice::setNotice($userid,$type,$noticeid);
        return $this->Rsuccess('设置成功');
    }

}
