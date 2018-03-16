<?php

namespace app\models;

use Yii;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Cell;
/**
 * This is the model class for table "contact_person".
 *
 * @property string $id
 * @property string $userid
 * @property string $touserid
 * @property string $createtime
 * @property integer $tagid
 */
class ContactPerson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contact_person';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createtime'], 'safe'],
            [['tagid'], 'integer'],
            [['userid', 'touserid'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'touserid' => 'Touserid',
            'createtime' => 'Createtime',
            'tagid' => 'Tagid',
        ];
    }
    /**
     * 关联用户表
     */
    public function getUUserInfo(){
        return $this->hasMany(UUserInfo::className(), ['s_userid'  => 'touserid']);
    }
    /**
     * 关联标签表
     */
    public function getContactTag(){
        return $this->hasMany(ContactTag::className(), ['tagid'  => 'tagid']);
    }
    /**
     * 是否已经是联系人
     * @param $userid
     * @param touserid
     * @return array
     */
    public static function contactInfo($userid,$touserid){
        $result = ContactPerson::find()
                ->where(['userid'=>$userid,'touserid'=>$touserid])
                ->asArray()
                ->one();
        return $result;
    }
    /**
     * 创建联系人
     * @param $userid
     * @param $touserid
     * @return bool
     */
    public static function addContact($userid,$touserid){
        $user = ContactPerson::contactInfo($userid,$touserid);
        if(empty($user)){
            $tag   = ContactPersonLog::logInfo($userid,$touserid);
            $model = new ContactPerson();
            $model ->userid     = $userid;
            $model ->touserid   = $touserid;
            $model ->tagid      = empty($tag)?'':$tag['tagid'];
            $model ->createtime = date('Y-m-d H:i:s');
            $model ->save();
        }
        $touser = ContactPerson::contactInfo($touserid,$userid);
        if(empty($touser)){
            $tag   = ContactPersonLog::logInfo($touserid,$userid);
            $model = new ContactPerson();
            $model ->userid     = $touserid;
            $model ->touserid   = $userid;
            $model ->tagid      = empty($tag)?'':$tag['tagid'];
            $model ->createtime = date('Y-m-d H:i:s');
            $model ->save();
        }
        return true;
    }
    /**
     * 联系人列表
     * @param userid
     * @param page
     * @param type
     * @return array
     */
    public static function contactList($userid,$page,$type){
        $wherenum = ['contact_person.userid'=>$userid];
        $where    = "contact_person.userid = '".$userid."' ";
        if(is_numeric($type)){
            #数字就是tagid
            $wherenum = ['contact_person.userid'=>$userid,'contact_person.tagid'=>$type];
            $where = "contact_person.userid = '".$userid."' and contact_person.tagid ='".$type."' ";
        }
        if($type == 'always'){
            //用户所在项目
            $allproject = PProjectUser::userProject($userid);
            //所有项目中的所有成员
            $member     = PProjectUser::allMemberNoJoin($allproject);
            $wherenum      = ['contact_person.userid'=>$userid,'contact_person.touserid'=>$member];
            $where = "contact_person.userid = '".$userid."' and contact_person.touserid in ('".$member."') ";
        }
        $start  = $page-1 <= 0 ? 0 : ($page-1) * 15;
//        $data   = ContactPerson::find()
//                ->joinWith('uUserInfo')
//                ->joinWith('contactTag')
//                ->select('contact_person.*,s_username,s_mail,s_avatar,s_cellphone,tagname')
//                ->where($where)
//                ->orderBy( [mb_convert_encoding('s_username','UTF-8','GBK')=>SORT_ASC])
//                ->offset($start)
//                ->limit(15);
//                echo $data->createCommand()->getRawSql();exit;
//                ->asArray()
//                ->all();
        $data = Yii::$app->db->createCommand("SELECT
	contact_person.*, s_username,
	s_mail,
	s_avatar,
	s_cellphone,
	tagname
FROM
	contact_person
LEFT JOIN u_user_info ON contact_person.touserid = u_user_info.s_userid
LEFT JOIN contact_tag ON contact_person.tagid = contact_tag.tagid
WHERE
	$where
ORDER BY
	convert_to(s_username,'GBK')
LIMIT 15 offset $start")->queryAll();
        foreach ($data as $k=>$v){
            unset($data[$k]['uUserInfo']);
            unset($data[$k]['contactTag']);
        }
        $count  = ContactPerson::find()
                ->where($wherenum)
                ->count();
        $result['data']  = $data;
        $result['count'] = $count;
        return $result;
    }
    /**
     * 各个标签下联系人个数
     * @param tagid
     * @return integer
     */
    public static function tagPersonNum($tagid){
        $count = ContactPerson::find()
               ->where(['tagid'=>$tagid])
               ->count();
        return $count;
    }
    /**
     * 修改用户的某个标签为空
     * @param userid
     * @param tagid
     * @return bool
     */
    public static function delTag($userid,$tagid){
        ContactPerson::updateAll(['tagid'=>""],['userid'=>$userid,'tagid'=>$tagid]);
        return true;
    }
    /**
     * 修改用户的标签
     * @param userid
     * @param $touserid
     * @param $tagid
     * @return bool
     */
    public static function editTagPerson($userid,$touserid,$tagid){
        ContactPerson::updateAll(['tagid'=>$tagid],['userid'=>$userid,'touserid'=>$touserid]);
        return true;
    }
    /**
     * 导出我的联系人
     * @param userid
     * @return string
     */
    public static function exportContact($userid){
        $data   = ContactPerson::find()
                ->joinWith('uUserInfo')
                ->joinWith('contactTag')
                ->select('contact_person.*,s_username,s_mail,s_avatar,s_cellphone,tagname')
                ->where(['contact_person.userid'=>$userid])
                ->asArray()
                ->all();
        if(!empty($data)) {
            //生成excel
            $objPHPExcel = new PHPExcel();
            // Add some data
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '姓名')
                ->setCellValue('B1', '电子邮箱')
                ->setCellValue('C1', '手机号')
                ->setCellValue('D1', '标签');
            $i = 2;
            foreach ($data as $k=>$v) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $v['s_username'])
                    ->setCellValue('B' . $i, $v['s_mail'])
                    ->setCellValue('C' . $i, $v['s_cellphone'])
                    ->setCellValue('D' . $i, $v['tagname']);
                $i++;
            }
            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('我的联系人');
            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);
            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="我的联系人.xls"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        }
    }
    /**
     * 移除联系人
     * @param userid
     * @param touserid
     * @return bool
     */
    public static function delContact($userid,$touserid){
        ContactPerson::deleteAll(['or',['userid'=>$userid,'touserid'=>$touserid],['userid'=>$touserid,'touserid'=>$userid]]);
        return true;
    }
}
