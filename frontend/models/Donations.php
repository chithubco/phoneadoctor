<?php
namespace app\models;
use Yii;
/**
 * This is the model class for table "donations".
 *
 * @property integer $id
 * @property string $email
 * @property string $name
 * @property double $amount
 * @property string $date
 */
class Donations extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'donations';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'name', 'amount'], 'required'],
            [['amount'], 'number'],
            [['date'], 'safe'],
            [['email', 'name'], 'string', 'max' => 255],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'name' => 'Name',
            'amount' => 'Amount',
            'date' => 'Date',
        ];
    }
}