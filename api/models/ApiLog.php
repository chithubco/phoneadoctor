<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "api_log".
 *
 * @property string $id
 * @property integer $api_method_id
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
            [['api_method_id', 'type', 'api_log_description_id', 'user_id', 'notes', 'created', 'device_ip_address'], 'required'],
            [['api_method_id', 'api_log_description_id', 'user_id'], 'integer'],
            [['type', 'notes'], 'string'],
            [['created'], 'safe'],
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
            'api_method_id' => 'Api Method ID',
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
