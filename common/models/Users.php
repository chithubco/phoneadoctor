<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property integer $create_uid
 * @property integer $update_uid
 * @property string $create_date
 * @property string $update_date
 * @property string $username
 * @property resource $password
 * @property resource $pwd_history1
 * @property resource $pwd_history2
 * @property string $title
 * @property string $fname
 * @property string $mname
 * @property string $lname
 * @property string $pin
 * @property string $npi
 * @property string $fedtaxid
 * @property string $feddrugid
 * @property string $notes
 * @property string $email
 * @property string $specialty
 * @property string $taxonomy
 * @property integer $warehouse_id
 * @property integer $facility_id
 * @property integer $role_id
 * @property integer $calendar
 * @property integer $authorized
 * @property integer $active
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_uid', 'update_uid', 'warehouse_id', 'facility_id', 'role_id', 'calendar', 'authorized', 'active'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['password', 'pwd_history1', 'pwd_history2'], 'string'],
            [['username', 'title', 'fname', 'mname', 'lname', 'pin', 'npi', 'fedtaxid', 'feddrugid', 'notes', 'email', 'specialty', 'taxonomy'], 'string', 'max' => 255],
        ];
    }


    public function getCalendar()
    {
        return $this->hasMany(CalendarEvents::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'create_uid' => 'Create Uid',
            'update_uid' => 'Update Uid',
            'create_date' => 'Create Date',
            'update_date' => 'Update Date',
            'username' => 'Username',
            'password' => 'Password',
            'pwd_history1' => 'Pwd History1',
            'pwd_history2' => 'Pwd History2',
            'title' => 'Title',
            'fname' => 'Fname',
            'mname' => 'Mname',
            'lname' => 'Lname',
            'pin' => 'Pin',
            'npi' => 'Npi',
            'fedtaxid' => 'Fedtaxid',
            'feddrugid' => 'Feddrugid',
            'notes' => 'Notes',
            'email' => 'Email',
            'specialty' => 'Specialty',
            'taxonomy' => 'Taxonomy',
            'warehouse_id' => 'Warehouse ID',
            'facility_id' => 'Facility ID',
            'role_id' => 'Role ID',
            'calendar' => 'Calendar',
            'authorized' => 'Authorized',
            'active' => 'Active',
        ];
    }
}
