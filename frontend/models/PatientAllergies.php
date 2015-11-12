<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "patient_allergies".
 *
 * @property string $id
 * @property integer $pid
 * @property integer $eid
 * @property string $allergy_type
 * @property string $allergy
 * @property string $begin_date
 * @property string $end_date
 * @property string $reaction
 * @property string $severity
 * @property string $location
 * @property string $create_date
 * @property string $update_date
 * @property string $created_uid
 * @property string $updated_uid
 * @property string $allergy_code
 */
class PatientAllergies extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient_allergies';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'eid', 'created_uid', 'updated_uid'], 'integer'],
            [['begin_date', 'end_date', 'create_date', 'update_date'], 'safe'],
            [['allergy_type', 'allergy', 'reaction'], 'string', 'max' => 50],
            [['severity', 'location', 'allergy_code'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'eid' => 'Eid',
            'allergy_type' => 'Allergy Type',
            'allergy' => 'Allergy',
            'begin_date' => 'Begin Date',
            'end_date' => 'End Date',
            'reaction' => 'Reaction',
            'severity' => 'Severity',
            'location' => 'Location',
            'create_date' => 'Create Date',
            'update_date' => 'Update Date',
            'created_uid' => 'created by User ID',
            'updated_uid' => 'updated by User ID',
            'allergy_code' => 'RxNORM RXCUI code if food allergy',
        ];
    }
}
