<?php
namespace app\models;

use Yii;

/**
* This is the model class for collection "customer".
*
* @property \MongoDB\BSON\ObjectID|string $_id
* @property mixed $id
* @property mixed $name
* @property mixed $province
* @property mixed $city
* @property mixed $town
* @property mixed $address
* @property mixed $lng
* @property mixed $lat
* @property mixed $create_time
*/
class Testm extends \yii\mongodb\ActiveRecord {

/**
* @inheritdoc
*/
public static function collectionName() {
 return ['test', 'runoob'];
}

/**
* @inheritdoc
*/
public function attributes() {
    return [
        '_id',
        'name',
        'sex',
    ];
}

/**
* @inheritdoc
* 参考 YII2，rules规则
*/
public function rules() {
    return [
    ];
}

/**
* @inheritdoc
*/
public function attributeLabels() {
    return [
        '_id' => 'ID',
        'name' => 'Name',
    ];
}
public static function datalist(){
    $model = new Testm();
    $model -> name = '123';
    $model -> sex  = 1;
    $model -> save();

}

}