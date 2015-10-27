<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "patients".
 *
 * @property string $id
 * @property integer $user_id
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $birth_date
 * @property string $location
 * @property string $pin_code
 */
class Patients extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patients';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['firstname', 'lastname', 'location'], 'string', 'max' => 100],
            [['email', 'birth_date', 'pin_code'], 'string', 'max' => 50]
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
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'email' => 'Email',
            'birth_date' => 'Birth Date',
            'location' => 'Location',
            'pin_code' => 'Pin Code',
        ];
    }
}
