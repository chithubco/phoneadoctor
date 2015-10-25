<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "patient".
 *
 * @property integer $pid
 * @property integer $create_uid
 * @property integer $update_uid
 * @property string $create_date
 * @property string $update_date
 * @property string $title
 * @property string $fname
 * @property string $mname
 * @property string $lname
 * @property string $sex
 * @property string $DOB
 * @property string $marital_status
 * @property string $SS
 * @property string $pubpid
 * @property string $drivers_license
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $country
 * @property string $zipcode
 * @property string $home_phone
 * @property string $mobile_phone
 * @property string $work_phone
 * @property string $email
 * @property string $mothers_name
 * @property string $guardians_name
 * @property string $emer_contact
 * @property string $emer_phone
 * @property string $provider
 * @property string $pharmacy
 * @property string $hipaa_notice
 * @property string $race
 * @property string $ethnicity
 * @property string $language
 * @property integer $allow_leave_msg
 * @property integer $allow_voice_msg
 * @property integer $allow_mail_msg
 * @property integer $allow_sms
 * @property integer $allow_email
 * @property integer $allow_immunization_registry
 * @property integer $allow_immunization_info_sharing
 * @property integer $allow_health_info_exchange
 * @property integer $allow_patient_web_portal
 * @property string $occupation
 * @property string $employer_name
 * @property string $employer_address
 * @property string $employer_city
 * @property string $employer_state
 * @property string $employer_country
 * @property string $employer_postal_code
 * @property integer $rating
 * @property string $image
 * @property string $qrcode
 */
class Patient extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_uid', 'update_uid', 'allow_leave_msg', 'allow_voice_msg', 'allow_mail_msg', 'allow_sms', 'allow_email', 'allow_immunization_registry', 'allow_immunization_info_sharing', 'allow_health_info_exchange', 'allow_patient_web_portal', 'rating'], 'integer'],
            [['create_date', 'update_date', 'DOB'], 'safe'],
            [['image', 'qrcode'], 'string'],
            [['title', 'sex', 'zipcode', 'language', 'employer_postal_code'], 'string', 'max' => 10],
            [['fname', 'lname', 'email'], 'string', 'max' => 60],
            [['mname', 'marital_status', 'SS', 'pubpid', 'drivers_license', 'city', 'state', 'country', 'mothers_name', 'guardians_name', 'emer_contact', 'provider', 'pharmacy', 'hipaa_notice', 'race', 'ethnicity', 'occupation', 'employer_name', 'employer_address', 'employer_city', 'employer_state', 'employer_country'], 'string', 'max' => 40],
            [['address'], 'string', 'max' => 80],
            [['home_phone', 'mobile_phone', 'work_phone', 'emer_phone'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pid' => 'Pid',
            'create_uid' => 'Create Uid',
            'update_uid' => 'Update Uid',
            'create_date' => 'Create Date',
            'update_date' => 'Update Date',
            'title' => 'Title',
            'fname' => 'Fname',
            'mname' => 'Mname',
            'lname' => 'Lname',
            'sex' => 'Sex',
            'DOB' => 'Dob',
            'marital_status' => 'Marital Status',
            'SS' => 'Ss',
            'pubpid' => 'Pubpid',
            'drivers_license' => 'Drivers License',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'zipcode' => 'Zipcode',
            'home_phone' => 'Home Phone',
            'mobile_phone' => 'Mobile Phone',
            'work_phone' => 'Work Phone',
            'email' => 'Email',
            'mothers_name' => 'Mothers Name',
            'guardians_name' => 'Guardians Name',
            'emer_contact' => 'Emer Contact',
            'emer_phone' => 'Emer Phone',
            'provider' => 'Provider',
            'pharmacy' => 'Pharmacy',
            'hipaa_notice' => 'Hipaa Notice',
            'race' => 'Race',
            'ethnicity' => 'Ethnicity',
            'language' => 'Language',
            'allow_leave_msg' => 'Allow Leave Msg',
            'allow_voice_msg' => 'Allow Voice Msg',
            'allow_mail_msg' => 'Allow Mail Msg',
            'allow_sms' => 'Allow Sms',
            'allow_email' => 'Allow Email',
            'allow_immunization_registry' => 'Allow Immunization Registry',
            'allow_immunization_info_sharing' => 'Allow Immunization Info Sharing',
            'allow_health_info_exchange' => 'Allow Health Info Exchange',
            'allow_patient_web_portal' => 'Allow Patient Web Portal',
            'occupation' => 'Occupation',
            'employer_name' => 'Employer Name',
            'employer_address' => 'Employer Address',
            'employer_city' => 'Employer City',
            'employer_state' => 'Employer State',
            'employer_country' => 'Employer Country',
            'employer_postal_code' => 'Employer Postal Code',
            'rating' => 'Rating',
            'image' => 'Image',
            'qrcode' => 'Qrcode',
        ];
    }
}
