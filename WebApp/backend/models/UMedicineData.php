<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "u_medicine_data".
 *
 * @property string $u_drugname
 * @property string $u_generalname
 * @property string $u_specifications
 * @property string $u_unit
 * @property string $u_company
 */
class UMedicineData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_medicine_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_drugname', 'u_generalname', 'u_specifications', 'u_unit', 'u_company'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'u_drugname' => 'U Drugname',
            'u_generalname' => 'U Generalname',
            'u_specifications' => 'U Specifications',
            'u_unit' => 'U Unit',
            'u_company' => 'U Company',
        ];
    }
    /**
     * @todo 药品列表
     */
    public static function selectDrug($drugname){
        $result = UMedicineData::find()
        ->where(['like', 'u_drugname', $drugname] )
        ->limit(4)
        ->asarray()
        ->all();
        return $result;
    }
}
