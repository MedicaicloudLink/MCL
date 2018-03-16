<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "patient_filter".
 *
 * @property integer $filterid
 * @property string $filtername
 * @property string $userid
 * @property string $projectid
 * @property string $title
 * @property string $type
 * @property string $value
 * @property string $filter_operator
 * @property string $start_time
 * @property string $end_time
 * @property integer $time_value
 * @property string $time_unit
 * @property string $updatetime
 */
class PatientFilter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient_filter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time_value'], 'integer'],
            [['start_time', 'end_time','updatetime'], 'safe'],
            [['filtername'], 'string', 'max' => 200],
            [['userid', 'projectid'], 'string', 'max' => 11],
            [['title', 'value', 'filter_operator'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 25],
            [['time_unit'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'filterid' => 'Filterid',
            'filtername' => 'Filtername',
            'userid' => 'Userid',
            'projectid' => 'Projectid',
            'title' => 'Title',
            'type' => 'Type',
            'value' => 'Value',
            'filter_operator' => 'Filter Operator',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'time_value' => 'Time Value',
            'time_unit' => 'Time Unit',
            'updatetime' => 'Updatetime',
        ];
    }
    /**
     * 创建过滤器
     */
    public static function addFilter(){
        $model = new PatientFilter();
        if(Yii::$app->getRequest()->getBodyParam('filterid') != ''){
            $model = PatientFilter::findOne(['filterid'=>Yii::$app->getRequest()->getBodyParam('filterid')]);
        }
        $model ->userid          = Yii::$app->getRequest()->getBodyParam('userid');
        $model ->projectid       = Yii::$app->getRequest()->getBodyParam('projectid');
        $model ->filtername      = Yii::$app->getRequest()->getBodyParam('name');
        $model ->title           = Yii::$app->getRequest()->getBodyParam('title');
        $model ->type            = Yii::$app->getRequest()->getBodyParam('type');
        $model ->value           = Yii::$app->getRequest()->getBodyParam('value');
        $model ->start_time      = Yii::$app->getRequest()->getBodyParam('start_time');
        $model ->end_time        = Yii::$app->getRequest()->getBodyParam('end_time');
        $model ->time_unit       = Yii::$app->getRequest()->getBodyParam('time_unit');
        $model ->time_value      = Yii::$app->getRequest()->getBodyParam('time_value');
        $model ->filter_operator = Yii::$app->getRequest()->getBodyParam('filter_operator');
        $model ->updatetime      = date('Y-m-d H:i:s');
        if($model->save()){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 过滤器详情
     * @param $filterid
     * @return array
     */
    public static function filterInfo($filterid){
        $result = PatientFilter::find()
                ->where(['filterid'=>$filterid])
                ->asArray()
                ->one();
        return $result;
    }
    /**
     * 列表
     * @param userid
     * @param projectid
     * @return array
     */
    public static function filterList($userid,$projectid){
        $result = PatientFilter::find()
                ->select('filterid,filtername')
                ->where(['userid'=>$userid,'projectid'=>$projectid])
                ->orderBy(['updatetime'=>SORT_DESC])
                ->asArray()
                ->all();
        return $result;
    }
    /**
     * 删除过滤器
     * @param userid
     * @param filterid
     * @return bool
     */
    public static function delFilter($userid,$filterid){
        PatientFilter::deleteAll(['userid'=>$userid,'filterid'=>$filterid]);
        return true;
    }
}
