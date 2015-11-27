<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $username
 * @property string $password
 * @property integer $auth_key
 * @property integer $createtime
 * @property integer $lastvisit
 * @property integer $status
 * @property string $role
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['auth_key', 'createtime', 'lastvisit', 'status'], 'integer'],
            [['username'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 128],
            [['role'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'auth_key' => 'Auth Key',
            'createtime' => 'Createtime',
            'lastvisit' => 'Lastvisit',
            'status' => 'Status',
            'role' => 'Role',
        ];
    }
}
