<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rxnconso".
 *
 * @property string $RXCUI
 * @property string $LAT
 * @property string $TS
 * @property string $LUI
 * @property string $STT
 * @property string $SUI
 * @property string $ISPREF
 * @property string $RXAUI
 * @property string $SAUI
 * @property string $SCUI
 * @property string $SDUI
 * @property string $SAB
 * @property string $TTY
 * @property string $CODE
 * @property string $STR
 * @property string $SRL
 * @property string $SUPPRESS
 * @property string $CVF
 */
class Rxnconso extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rxnconso';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['RXCUI', 'RXAUI', 'SAB', 'TTY', 'CODE', 'STR'], 'required'],
            [['RXCUI', 'LUI', 'SUI', 'RXAUI'], 'string', 'max' => 8],
            [['LAT', 'STT'], 'string', 'max' => 3],
            [['TS', 'ISPREF', 'SUPPRESS'], 'string', 'max' => 1],
            [['SAUI', 'SCUI', 'SDUI', 'CODE', 'CVF'], 'string', 'max' => 50],
            [['SAB', 'TTY'], 'string', 'max' => 20],
            [['STR'], 'string', 'max' => 3000],
            [['SRL'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'RXCUI' => 'Rxcui',
            'LAT' => 'Lat',
            'TS' => 'Ts',
            'LUI' => 'Lui',
            'STT' => 'Stt',
            'SUI' => 'Sui',
            'ISPREF' => 'Ispref',
            'RXAUI' => 'Rxaui',
            'SAUI' => 'Saui',
            'SCUI' => 'Scui',
            'SDUI' => 'Sdui',
            'SAB' => 'Sab',
            'TTY' => 'Tty',
            'CODE' => 'Code',
            'STR' => 'Str',
            'SRL' => 'Srl',
            'SUPPRESS' => 'Suppress',
            'CVF' => 'Cvf',
        ];
    }
}
