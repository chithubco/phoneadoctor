<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "facility".
 *
 * @property integer $id
 * @property string $name
 * @property integer $active
 * @property string $phone
 * @property string $fax
 * @property string $street
 * @property string $city
 * @property string $state
 * @property string $postal_code
 * @property string $country_code
 * @property string $federal_ein
 * @property integer $service_location
 * @property integer $billing_location
 * @property integer $accepts_assignment
 * @property string $pos_code
 * @property string $x12_sender_id
 * @property string $attn
 * @property string $domain_identifier
 * @property string $facility_npi
 * @property string $tax_id_type
 */
class Facility extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'facility';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['active', 'service_location', 'billing_location', 'accepts_assignment'], 'integer'],
            [['name', 'street', 'city', 'pos_code'], 'string', 'max' => 255],
            [['phone', 'fax'], 'string', 'max' => 30],
            [['state'], 'string', 'max' => 50],
            [['postal_code'], 'string', 'max' => 11],
            [['country_code'], 'string', 'max' => 10],
            [['federal_ein', 'facility_npi'], 'string', 'max' => 15],
            [['x12_sender_id'], 'string', 'max' => 25],
            [['attn'], 'string', 'max' => 65],
            [['domain_identifier'], 'string', 'max' => 60],
            [['tax_id_type'], 'string', 'max' => 31],
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
            'active' => 'Active',
            'phone' => 'Phone',
            'fax' => 'Fax',
            'street' => 'Street',
            'city' => 'City',
            'state' => 'State',
            'postal_code' => 'Postal Code',
            'country_code' => 'Country Code',
            'federal_ein' => 'Federal Ein',
            'service_location' => 'Service Location',
            'billing_location' => 'Billing Location',
            'accepts_assignment' => 'Accepts Assignment',
            'pos_code' => 'Pos Code',
            'x12_sender_id' => 'X12 Sender ID',
            'attn' => 'Attn',
            'domain_identifier' => 'Domain Identifier',
            'facility_npi' => 'Facility Npi',
            'tax_id_type' => 'Tax Id Type',
        ];
    }
}
