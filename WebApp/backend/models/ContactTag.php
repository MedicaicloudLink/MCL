<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contact_tag".
 *
 * @property integer $tagid
 * @property string $tagname
 * @property string $userid
 * @property string $updatetime
 */
class ContactTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contact_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['updatetime'], 'safe'],
            [['tagname'], 'string', 'max' => 200],
            [['userid'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tagid' => 'Tagid',
            'tagname' => 'Tagname',
            'userid' => 'Userid',
            'updatetime' => 'Updatetime',
        ];
    }
    /**
     * 是否已经有此标签
     * @param tagid
     * @return array
     */
    public static function tagInfo($tagid){
        $result = ContactTag::find()
                ->where(['tagid'=>$tagid])
                ->asArray()
                ->one();
        return $result;
    }
    /**
     * 创建/编辑标签
     * @param userid
     * @param tagname
     * @param tagid
     * @return bool
     */
    public static function addTag($userid,$tagname,$tagid){
        if($tagid != '' && !empty(ContactTag::tagInfo($tagid))){
            $model = ContactTag::findOne(['tagid'=>$tagid]);
        }else{
            $model = new ContactTag();
        }
        $model ->userid     = $userid;
        $model ->tagname    = $tagname;
        $model ->updatetime = date('Y-m-d H:i:s');
        if($model ->save()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 标签列表
     * @param userid
     * @return array
     */
    public static function tagList($userid){
        $result = ContactTag::find()
                ->where(['userid'=>$userid])
                ->orderBy(['updatetime'=>SORT_DESC])
                ->asArray()
                ->all();
        return $result;
    }
    /**
     * 每个标签下人数
     * @param userid
     * @return array
     */
    public static function tagPersonNum($userid){
        $result = ContactTag::find()
                ->where(['userid'=>$userid])
                ->orderBy(['updatetime'=>SORT_ASC])
                ->asArray()
                ->all();
        if(!empty($result)){
            foreach($result as $k=>$v){
                $result[$k]['count'] = ContactPerson::tagPersonNum($v['tagid']);
            }
        }
        return $result;
    }
    /**
     * 删除标签
     * @param userid
     * @param tagid
     * @return bool
     */
    public static function delTag($userid,$tagid){
        //删除标签
        ContactTag::deleteAll(['userid'=>$userid,'tagid'=>$tagid]);
        //把联系人表中tagid清空
        ContactPerson::delTag($userid,$tagid);
        return true;
    }
}
