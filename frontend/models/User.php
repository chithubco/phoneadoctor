<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $phone
 * @property string $accessKey
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
            [['createtime', 'lastvisit', 'status'], 'integer'],
            [['username'], 'string', 'max' => 50],
            [['password', 'accessKey'], 'string', 'max' => 128],
            [['phone', 'role'], 'string', 'max' => 100]
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
            'phone' => 'Phone',
            'accessKey' => 'Access Key',
            'createtime' => 'Createtime',
            'lastvisit' => 'Lastvisit',
            'status' => 'Status',
            'role' => 'Role',
        ];
    }
}
