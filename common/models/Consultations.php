<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "consultations".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $details
 * @property string $type
 * @property string $date
 */
class Consultations extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'consultations';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'details'], 'required'],
            [['user_id'], 'integer'],
            [['details', 'type'], 'string'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'details' => 'Details',
            'type' => 'Type',
            'date' => 'Date',
        ];
    }
}
