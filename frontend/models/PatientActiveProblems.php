<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "patient_active_problems".
 *
 * @property string $id
 * @property integer $pid
 * @property integer $eid
 * @property string $code
 * @property string $code_text
 * @property string $code_type
 * @property string $begin_date
 * @property string $end_date
 * @property string $occurrence
 * @property string $referred_by
 * @property string $outcome
 * @property string $create_date
 * @property string $update_date
 * @property string $created_uid
 * @property string $updated_uid
 */
class PatientActiveProblems extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient_active_problems';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'eid', 'created_uid', 'updated_uid'], 'integer'],
            [['begin_date', 'end_date', 'create_date', 'update_date'], 'safe'],
            [['code', 'code_text', 'code_type', 'occurrence'], 'string', 'max' => 255],
            [['referred_by', 'outcome'], 'string', 'max' => 50]
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
            'code' => 'Code',
            'code_text' => 'Code Text',
            'code_type' => 'Code Type',
            'begin_date' => 'Begin Date',
            'end_date' => 'End Date',
            'occurrence' => 'Occurrence',
            'referred_by' => 'Referred By',
            'outcome' => 'Outcome',
            'create_date' => 'Create Date',
            'update_date' => 'Update Date',
            'created_uid' => 'created by User ID',
            'updated_uid' => 'updated by User ID',
        ];
    }
}
