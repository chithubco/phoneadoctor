<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "calendar_events".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $category
 * @property integer $facility
 * @property integer $billing_facillity
 * @property integer $patient_id
 * @property string $title
 * @property string $status
 * @property string $start
 * @property string $end
 * @property string $rrule
 * @property string $loc
 * @property string $notes
 * @property string $url
 * @property string $ad
 */
class CalendarEvents extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calendar_events';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'start', 'end'], 'required'],
            [['user_id', 'category', 'facility', 'billing_facillity', 'patient_id'], 'integer'],
            [['start', 'end'], 'safe'],
            [['title', 'status', 'rrule', 'loc', 'notes', 'url', 'ad'], 'string', 'max' => 255],
        ];
    }

    public function getUsers()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'category' => 'Category',
            'facility' => 'Facility',
            'billing_facillity' => 'Billing Facillity',
            'patient_id' => 'Patient ID',
            'title' => 'Title',
            'status' => 'Status',
            'start' => 'Start',
            'end' => 'End',
            'rrule' => 'Rrule',
            'loc' => 'Loc',
            'notes' => 'Notes',
            'url' => 'Url',
            'ad' => 'Ad',
        ];
    }
}
