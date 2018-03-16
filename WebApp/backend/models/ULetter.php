<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "u_letter".
 *
 * @property string $fPY
 * @property integer $cBegin
 * @property integer $cEnd
 */
class ULetter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'u_letter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fPY', 'cBegin', 'cEnd'], 'required'],
            [['cBegin', 'cEnd'], 'integer'],
            [['fPY'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fPY' => 'F Py',
            'cBegin' => 'C Begin',
            'cEnd' => 'C End',
        ];
    }
}
