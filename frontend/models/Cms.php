<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cms".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $title
 * @property string $content
 * @property string $cms_type
 * @property string $status
 */
class Cms extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cms';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'title', 'content'], 'required'],
            [['content', 'cms_type', 'status'], 'string'],
            [['name', 'description', 'title'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'title' => 'Title',
            'content' => 'Content',
            'cms_type' => 'Cms Type',
            'status' => 'Status',
        ];
    }
}
