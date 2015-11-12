<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "patient_medications".
 *
 * @property string $id
 * @property integer $pid
 * @property integer $eid
 * @property string $STR
 * @property string $RXCUI
 * @property string $CODE
 * @property string $ICDS
 * @property string $begin_date
 * @property string $end_date
 * @property string $ocurrence
 * @property string $referred_by
 * @property string $outcome
 * @property integer $prescription_id
 * @property string $route
 * @property string $dispense
 * @property string $dose
 * @property string $prescription_often
 * @property string $prescription_when
 * @property string $refill
 * @property integer $take_pills
 * @property string $form
 * @property integer $uid
 * @property string $date_ordered
 * @property string $created_date
 */
class PatientMedications extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient_medications';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'eid', 'prescription_id', 'take_pills', 'uid'], 'integer'],
            [['begin_date', 'end_date', 'date_ordered', 'created_date'], 'safe'],
            [['STR'], 'string', 'max' => 200],
            [['RXCUI', 'CODE', 'ocurrence', 'referred_by', 'outcome'], 'string', 'max' => 50],
            [['ICDS', 'route', 'dispense', 'prescription_often', 'prescription_when', 'refill', 'form'], 'string', 'max' => 255],
            [['dose'], 'string', 'max' => 25]
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
            'STR' => 'Str',
            'RXCUI' => 'Rxcui',
            'CODE' => 'Code',
            'ICDS' => 'Icds',
            'begin_date' => 'Begin Date',
            'end_date' => 'End Date',
            'ocurrence' => 'Ocurrence',
            'referred_by' => 'Referred By',
            'outcome' => 'Outcome',
            'prescription_id' => 'Prescription ID',
            'route' => 'Route',
            'dispense' => 'Dispense',
            'dose' => 'Dose',
            'prescription_often' => 'Prescription Often',
            'prescription_when' => 'Prescription When',
            'refill' => 'Refill',
            'take_pills' => 'Take Pills',
            'form' => 'Form',
            'uid' => 'Uid',
            'date_ordered' => 'Date Ordered',
            'created_date' => 'Created Date',
        ];
    }
}
