<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "verify_phone".
 *
 * @property string $id
 * @property integer $phone_no
 * @property integer $verification_code
 * @property string $verified
 */
class VerifyPhone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'verify_phone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone_no', 'verification_code'], 'integer'],
            [['verified'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone_no' => 'Phone No',
            'verification_code' => 'Verification Code',
            'verified' => 'Verified',
        ];
    }
}
