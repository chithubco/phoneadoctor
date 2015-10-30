<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "patient_documents".
 *
 * @property integer $id
 * @property integer $eid
 * @property integer $pid
 * @property integer $uid
 * @property string $docType
 * @property string $name
 * @property string $url
 * @property string $date
 * @property string $note
 * @property string $title
 * @property string $hash
 * @property integer $encrypted
 */
class PatientDocuments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient_documents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['eid', 'pid', 'uid', 'encrypted'], 'integer'],
            [['date'], 'safe'],
            [['docType', 'name', 'url', 'note', 'title', 'hash'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'eid' => 'Eid',
            'pid' => 'Pid',
            'uid' => 'Uid',
            'docType' => 'Doc Type',
            'name' => 'Name',
            'url' => 'Url',
            'date' => 'Date',
            'note' => 'Note',
            'title' => 'Title',
            'hash' => 'Hash',
            'encrypted' => 'Encrypted',
        ];
    }
}
