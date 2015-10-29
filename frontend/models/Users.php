<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property string $id
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
            [['username', 'title', 'fname', 'mname', 'lname', 'pin', 'npi', 'fedtaxid', 'feddrugid', 'notes', 'email', 'specialty', 'taxonomy'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'create_uid' => 'create user ID',
            'update_uid' => 'update user ID',
            'create_date' => 'create date',
            'update_date' => 'last update date',
            'username' => 'username',
            'password' => 'password',
            'pwd_history1' => 'first password history backwards',
            'pwd_history2' => 'second password history backwards',
            'title' => 'title (Mr. Mrs.)',
            'fname' => 'first name',
            'mname' => 'middle name',
            'lname' => 'last name',
            'pin' => 'pin number',
            'npi' => 'National Provider Identifier',
            'fedtaxid' => 'federal tax id',
            'feddrugid' => 'federal drug id',
            'notes' => 'notes',
            'email' => 'email',
            'specialty' => 'specialty',
            'taxonomy' => 'taxonomy',
            'warehouse_id' => 'default warehouse',
            'facility_id' => 'default facility',
            'role_id' => 'acl_user_roles relation',
            'calendar' => 'has calendar? 0=no 1=yes',
            'authorized' => 'Authorized',
            'active' => 'Active',
        ];
    }
}
