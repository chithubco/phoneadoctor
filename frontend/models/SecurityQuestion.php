<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "security_question".
 *
 * @property string $id
 * @property string $question
 */
class SecurityQuestion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'security_question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question' => 'Question',
        ];
    }
}
