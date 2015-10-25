<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "calendar_categories".
 *
 * @property integer $catid
 * @property string $catname
 * @property string $catcolor
 * @property string $catdesc
 * @property integer $duration
 * @property integer $cattype
 */
class CalendarCategories extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calendar_categories';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['catdesc'], 'string'],
            [['duration', 'cattype'], 'integer'],
            [['catname', 'catcolor'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'catid' => 'Catid',
            'catname' => 'Catname',
            'catcolor' => 'Catcolor',
            'catdesc' => 'Catdesc',
            'duration' => 'Duration',
            'cattype' => 'Cattype',
        ];
    }
}
