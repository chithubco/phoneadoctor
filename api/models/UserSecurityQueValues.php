<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_security_que_values".
 *
 * @property string $id
 * @property integer $user_id
 * @property integer $que_id
 * @property string $user_value
 * @property string $custom_question
 */
class UserSecurityQueValues extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_security_que_values';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'que_id'], 'integer'],
            [['user_value'], 'string', 'max' => 150],
            [['custom_question'], 'string', 'max' => 200]
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
            'que_id' => 'Que ID',
            'user_value' => 'User Value',
            'custom_question' => 'Custom Question',
        ];
    }
}
