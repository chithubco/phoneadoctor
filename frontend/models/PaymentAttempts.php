<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "payment_attempts".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $gateway
 * @property string $email
 * @property double $amount
 * @property string $status
 * @property string $response_code
 * @property string $response
 * @property string $details
 * @property string $date
 */
class PaymentAttempts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_attempts';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'gateway', 'email', 'amount', 'status', 'response_code', 'response', 'details'], 'required'],
            [['user_id'], 'integer'],
            [['amount'], 'number'],
            [['date'], 'safe'],
            [['gateway'], 'string', 'max' => 50],
            [['email', 'response', 'details'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 20],
            [['response_code'], 'string', 'max' => 10],
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
            'gateway' => 'Gateway',
            'email' => 'Email',
            'amount' => 'Amount',
            'status' => 'Status',
            'response_code' => 'Response Code',
            'response' => 'Response',
            'details' => 'Details',
            'date' => 'Date',
        ];
    }
}