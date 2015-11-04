<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "api_log".
 *
 * @property string $id
 * @property string $api_method
 * @property string $type
 * @property integer $api_log_description_id
 * @property integer $user_id
 * @property string $notes
 * @property string $created
 * @property string $device_ip_address
 * @property string $trans_id
 */
class ApiLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['api_method', 'type', 'api_log_description_id', 'user_id', 'notes', 'created', 'device_ip_address'], 'required'],
            [['type', 'notes'], 'string'],
            [['api_log_description_id', 'user_id'], 'integer'],
            [['created'], 'safe'],
            [['api_method'], 'string', 'max' => 150],
            [['device_ip_address'], 'string', 'max' => 100],
            [['trans_id'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'api_method' => 'Api Method',
            'type' => 'Type',
            'api_log_description_id' => 'Api Log Description ID',
            'user_id' => 'User ID',
            'notes' => 'Notes',
            'created' => 'Created',
            'device_ip_address' => 'Device Ip Address',
            'trans_id' => 'Trans ID',
        ];
    }
}
